<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XTAIN\Bundle\JoomlaBundle\Joomla\InvalidStateMethodCallException;
use XTAIN\Bundle\JoomlaBundle\Library\Loader;

/**
 * This class controls a Joomla instance to provide helper to render a
 * Joomla response into the Symfony framework
 *
 * The class is statefull
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class Joomla implements JoomlaInterface
{
    /**
     * @var int
     */
    const STATE_FRESH = 0; // the Joomla instance is not initialized

    /**
     * @var int
     */
    const STATE_INIT = 1; // the Joomla instance has been initialized

    /**
     * @var int
     */
    const STATE_STATUS_DEFINED = 2; // the response status is known

    /**
     * @var int
     */
    const STATE_INNER_CONTENT = 3; // Joomla has generated the inner content

    /**
     * @var int
     */
    const STATE_RESPONSE = 4; // Joomla has generated the Response object

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $type = self::SITE;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var array
     */
    protected $routerItem;

    /**
     * @var bool
     */
    protected $encapsulated;

    /**
     * @var \Exception
     */
    protected $encapsulatedException;

    /**
     * @var int
     */
    protected $pageResultCallback;

    /**
     * @var bool
     */
    protected $disableResponse;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $assetRoots = [
        'components',
        'images',
        'includes',
        'language',
        'layouts',
        'libraries',
        'media',
        'modules',
        'plugins',
        'templates'
    ];

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function __construct()
    {
        $this->state = self::STATE_FRESH;
        $this->response = new Response;
        $this->encapsulated = false;
        $this->disableResponse = false;
    }

    /**
     * @param string $type
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setApplication($type = self::SITE)
    {
        $this->type = $type;
    }

    /**
     * @param string $rootDir
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRootDir($rootDir)
    {
        $this->root = $rootDir;
    }

    /**
     * @param Filesystem $filesystem
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function bootstrapHelpers()
    {
        $files = [
            JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'helper.php',
            JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'toolbar.php'
        ];
        foreach ($files as $file) {
            if ($this->filesystem->exists($file)) {
                require_once $file;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;
        $currentLevel = ob_get_level();

        if (!defined('JPATH_BASE')) {
            define('JAPPLICATION_TYPE', $this->type);
        }

        $application = JAPPLICATION_TYPE;
        $cacheDir =
            rtrim(
                $this->container->getParameter('joomla.cache_dir') . DIRECTORY_SEPARATOR . strtolower($application),
                DIRECTORY_SEPARATOR
            );

        if (!defined('JPATH_BASE')) {
            $path = rtrim(JPATH_ROOT . DIRECTORY_SEPARATOR . strtolower(JAPPLICATION_TYPE), DIRECTORY_SEPARATOR);
            if (!is_dir($path)) {
                $path = JPATH_ROOT;
            }
            define('JPATH_BASE', $path);
        }

        if (!defined('JPATH_THEMES')) {
            define('JPATH_THEMES', JPATH_BASE . DIRECTORY_SEPARATOR . 'templates');
        }

        if (!defined('JPATH_CACHE')) {
            define('JPATH_CACHE', $cacheDir);
        }

        $this->bootstrapHelpers();

        // Mark afterLoad in the profiler.
        JDEBUG ? $this->container->get('joomla.profiler')->mark('afterLoad') : null;

        register_shutdown_function([$this, 'shutdown'], $currentLevel);

        $this->restoreBufferLevel($currentLevel);

        $this->state = self::STATE_INIT;
    }

    /**
     * State of initilize.
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function shutdown($level)
    {
        if (!$this->encapsulated) {
            return;
        }

        $headers = $this->cleanHeaders();

        foreach ($headers as $name => $value) {
            $this->response->headers->set($name, $value);
        }

        $content = ob_get_contents();

        $this->response->setContent($content);

        $this->restoreBufferLevel($level);

        $this->response->send();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function disableResponse()
    {
        $this->disableResponse = true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function hasResponse()
    {
        return !$this->disableResponse;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws InvalidStateMethodCallException If method called in invalid state
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function is403()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == '';
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws InvalidStateMethodCallException If method called in invalid state
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function is404()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        $code = null;
        if ($this->encapsulatedException instanceof \JException) {
            $code = $this->encapsulatedException->getCode();
        }

        return $code == 404;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws InvalidStateMethodCallException If method called in invalid state
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isOffline()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws InvalidStateMethodCallException If method called in invalid state
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isOnline()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws InvalidStateMethodCallException If method called in invalid state
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isFound()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isInstalled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function defineState(Request $request)
    {
        $this->initialize();

        $request->overrideGlobals();

        $this->state = self::STATE_INNER_CONTENT;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws InvalidStateMethodCallException If method called in invalid state
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function render()
    {
        if ($this->state < self::STATE_INNER_CONTENT) {
            throw new InvalidStateMethodCallException;
        }

        if ($this->state == self::STATE_RESPONSE) {
            return;
        }

        // Deliver the result of the page callback to the browser, or if requested,
        // return it raw, so calling code can do more processing.
        $content = $this->encapsulate(
            function (JoomlaInterface $joomla) {
                $application = $joomla->getApplication();
                $application->execute();
            },
            $this
        );

        $this->response->setContent($content);

        $this->state = self::STATE_RESPONSE;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     *
     * @return \JApplicationCms
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getApplication()
    {
        $this->initialize();

        return \JFactory::getApplication($this->type);
    }

    /**
     * {@inheritdoc}
     *
     * @return \JDocument
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getDocument()
    {
        $this->initialize();

        return \JFactory::getDocument();
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getAssetRoots()
    {
        $roots = [];

        $finder = new Finder();
        $finder->directories()->depth(0)->in(JPATH_ROOT);

        /** @var SplFileInfo $dir */
        foreach ($finder as $dir) {
            if ($dir->getFilename() == 'administrator' ||
                $dir->getFilename() == 'installation'
            ) {
                continue;
            }
            $roots[] = $dir->getFilename();
        }

        $finder = new Finder();
        $finder->directories()->depth(0)->in(JPATH_ADMINISTRATOR);

        /** @var SplFileInfo $dir */
        foreach ($finder as $dir) {
            $p = $dir->getPathname();
            $p = substr($p, strlen(JPATH_ROOT) + 1);
            $roots[] = $p;
        }

        return $roots;
    }

    /**
     * This method executes code related to the Joomla code, and builds a correct response if required
     *
     * @return string
     * @throws JoomlaRequestException If exception occurs in Jommla execution
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function encapsulate()
    {
        $this->encapsulated = true;
        $args = func_get_args();
        $function = array_shift($args);

        $content = '';

        ob_start(
            function ($buffer) use (&$content) {
                $content .= $buffer;

                return '';
            }
        );

        $level = ob_get_level();

        $applicationClosed = null;

        $statusCodeBefore = http_response_code();

        $this->encapsulatedException = null;

        try {
            call_user_func_array($function, $args);
        } catch (ApplicationClosedException $e) {
            // nothing
        } catch (\Exception $e) {
            $this->encapsulatedException = $e;
        }

        $this->restoreBufferLevel($level);
        ob_end_clean();

        $newStatusCode = http_response_code();

        $this->response->setStatusCode($newStatusCode);

        if ($statusCodeBefore != $newStatusCode) {
            http_response_code($statusCodeBefore);
        }

        $headers = $this->cleanHeaders();

        foreach ($headers as $name => $value) {
            $this->response->headers->set($name, $value);
        }

        $this->encapsulated = false;

        if ($this->encapsulatedException != null) {
            throw new JoomlaRequestException(
                "Encapsulated request to joomla failed",
                0,
                $this->encapsulatedException
            );
        }

        return $content;
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function cleanHeaders()
    {
        $headers = [];

        foreach (headers_list() as $header) {
            list($name, $value) = explode(':', $header, 2);
            $headers[$name] = trim($value);

            header_remove($name);
        }

        return $headers;
    }

    /**
     * Restores the buffer level by the given one
     *
     * @param int $level
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function restoreBufferLevel($level)
    {
        if (!is_numeric($level)) {
            return;
        }

        while (ob_get_level() > $level) {
            ob_end_flush();
        }
    }
}
