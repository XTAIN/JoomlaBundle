<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Document;

use Joomla\Registry\Registry;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Asset\PackageInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Uri\Uri;

/**
 * Class Html
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Document
 */
class Html extends \JProxy_JDocumentHtml
{
    /**
     * @var string
     */
    const TWIG_TEMPLATE_PATH = 'joomla/templates';

    /**
     * Document generator
     *
     * @var    string
     */
    public $_generator = 'Symfony 2 (using XTAINJoomlaBundle)';

    /**
     * @var PackageInterface
     */
    protected static $package;

    /**
     * @var RouterInterface
     */
    protected static $router;

    /**
     * @var \Twig_Environment
     */
    protected static $twigEnvironment;

    /**
     * @param PackageInterface $package
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setAssetsPackage(PackageInterface $package)
    {
        self::$package = $package;
    }

    /**
     * @param RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setRouter(RouterInterface $router)
    {
        self::$router = $router;
    }

    /**
     * @param \Twig_Environment $twigEnvironment
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setTwigEnvironment(\Twig_Environment $twigEnvironment)
    {
        self::$twigEnvironment = $twigEnvironment;
    }

    /**
     * @param string $url
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function patchUrl($url)
    {
        if (substr($url, 0, 1) !== '/' && !preg_match('#^[a-zA-Z0-9]+://#', $url)) {
            $url = \JUri::base(true) . '/' . $url;
        }

        $patched = false;
        $base = \JUri::base(false, false);
        if (stripos($url, $base) === 0) {
            $patched = true;
            $url = substr($url, strlen($base));
        } else {
            $base = \JUri::base(true, false);
            if (stripos($url, $base) === 0) {
                $patched = true;
                $url = substr($url, strlen($base));
            }
        }

        if ($patched) {
            $url = self::$package->getUrl($url);
        }

        return $url;
    }

    /**
     * @param string $url
     * @param string $type
     * @param bool   $defer
     * @param bool   $async
     *
     * @return \JDocument
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
    {
        $url = $this->patchUrl($url);

        return parent::addScript($url, $type, $defer, $async);
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $media
     * @param array  $attribs
     *
     * @return \JDocument
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = [])
    {
        $url = $this->patchUrl($url);

        return parent::addStyleSheet($url, $type, $media, $attribs);
    }

    /**
     * @param string $href
     * @param string $relation
     * @param string $relType
     * @param array  $attribs
     *
     * @return \JDocumentHTML
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addHeadLink($href, $relation, $relType = 'rel', $attribs = [])
    {
        $href = $this->patchUrl($href);

        return parent::addHeadLink($href, $relation, $relType, $attribs);
    }

    /**
     * @param string $generator
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setGenerator($generator)
    {
        // noting
    }

    /**
     * @return \Twig_Environment
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function getTwigEnvironment()
    {
        return self::$twigEnvironment;
    }

    /**
     * @param string $path
     * @param string $template
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function findTwigFile($path, $template = null)
    {
        if ($template == null) {
            $template = $this->template;
        }

        return '::' . self::TWIG_TEMPLATE_PATH . '/' . $template . '/' . $path;
    }

    /**
     * @param string $file
     * @param array  $context
     * @param string $template
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function renderTwig($file, array $context = [], $template = null)
    {
        $twig = $this->getTwigEnvironment();

        $template = $twig->loadTemplate($this->findTwigFile($file, $template));

        return $template->render($this->getTwigContext($context));
    }

    /**
     * @param array $context
     *
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function getTwigContext(array $context)
    {
        return array_merge(
            $context,
            [
                'joomla_view' => $this
            ]
        );
    }

    /**
     * @param string $directory
     * @param string $filename
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function _loadTemplate($directory, $filename)
    {
        $adminRoute = Uri::getAdministratorRoute();

        $this->baseurl = rtrim(\JUri::root(), '/\\');

        if (\JFactory::getApplication()->isAdmin()) {
            $this->baseurl .= $adminRoute->getPath();
        }

        $this->baseurl = rtrim($this->baseurl, '/\\');

        return parent::_loadTemplate($directory, $filename);
    }
}
