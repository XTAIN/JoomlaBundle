<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library;

use XTAIN\Bundle\JoomlaBundle\Factory\LoaderFactory;
use XTAIN\Bundle\JoomlaBundle\Factory\LoaderFactoryInterface;

/**
 * Class Loader
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library
 */
class Loader extends \JProxy_JLoader
{
    /**
     * @var LoaderFactoryInterface
     */
    protected static $factory;

    /**
     * @var array
     */
    protected static $injected = [];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $overridePaths = [];

    /**
     * @param LoaderFactoryInterface $factory
     */
    public function __construct(LoaderFactoryInterface $factory)
    {
        self::$instance = $this;
        self::$factory = $factory;
    }

    /**
     * @param string $path
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addOverridePath($path)
    {
        $this->overridePaths[] = $path;
    }

    /**
     * @param string $prefix
     * @param string $path
     * @param bool   $reset
     * @param bool   $prepend
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function registerPrefix($prefix, $path, $reset = false, $prepend = false)
    {
        $addPath = function () use ($prefix, $path, $reset, $prepend) {
            foreach ([
                         LoaderFactory::LIBRARIES_LEGACY,
                         LoaderFactory::LIBRARIES_JOOMLA,
                         LoaderFactory::LIBRARIES_CMS
                     ] as $overridePathPart) {
                if (preg_match('/' . preg_quote($overridePathPart, '/') . '$/', $path)) {
                    foreach (array_reverse(self::$instance->overridePaths) as $overridePath) {
                        $path = $overridePath . DIRECTORY_SEPARATOR . $overridePathPart;
                        if (is_dir($path)) {
                            parent::registerPrefix($prefix, $path, $reset, $prepend);
                        }
                    }
                    break;
                }
            }
        };

        if (!$prepend) {
            $addPath();
        }

        parent::registerPrefix($prefix, $path, $reset, $prepend);

        if ($prepend) {
            $addPath();
        }
    }

    /**
     * @return string[]
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getLibrarySearchPaths()
    {
        $paths = [];
        $paths[] = JPATH_LIBRARIES;

        foreach ($this->overridePaths as $path) {
            $paths[] = $path . DIRECTORY_SEPARATOR . LoaderFactory::LIBRARIES;
        }

        return array_reverse($paths);
    }

    /**
     * @param string $key
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected static function getClassByKey($key)
    {
        $parts = explode('.', $key);
        $class = array_pop($parts);

        // Handle special case for helper classes.
        if ($class == 'helper') {
            $class = ucfirst(array_pop($parts)) . ucfirst($class);
        } else {
            $class = ucfirst($class);
        }

        if (strpos($key, 'joomla.') === 0) {
            // Since we are in the Joomla namespace prepend the classname with J.
            $class = 'J' . $class;
        }

        return $class;
    }

    /**
     * Method to setup the autoloaders for the Joomla Platform.
     * Since the SPL autoloaders are called in a queue we will add our explicit
     * class-registration based loader first, then fall back on the autoloader based on conventions.
     * This will allow people to register a class in a specific location and override platform libraries
     * as was previously possible.
     *
     * @param   bool $enablePsr      True to enable autoloading based on PSR-0.
     * @param   bool $enablePrefixes True to enable prefix based class loading (needed to auto load the Joomla
     *                               core).
     * @param   bool $enableClasses  True to enable class map based class loading (needed to auto load the Joomla
     *                               core).
     *
     * @return  void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setup($enablePsr = true, $enablePrefixes = true, $enableClasses = true)
    {
        if ($enableClasses) {
            // Register the class map based autoloader.
            spl_autoload_register([self::$instance, 'load']);
        }

        if ($enablePrefixes) {
            // Register the J prefix and base path for Joomla platform libraries.
            self::registerPrefix('J', JPATH_PLATFORM . '/joomla');

            // Register the prefix autoloader.
            spl_autoload_register([self::$instance, '_autoload']);
        }

        if ($enablePsr) {
            // Register the PSR-0 based autoloader.
            spl_autoload_register([self::$instance, 'loadByPsr0']);
            spl_autoload_register([self::$instance, 'loadByAlias']);
        }
    }

    /**
     * @param string $key
     * @param string $base
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function import($key, $base = null)
    {
        $class = self::getClassByKey($key);
        $class = strtolower($class);
        if ($base === null) {
            $searchPaths = self::$instance->getLibrarySearchPaths();
            foreach ($searchPaths as $searchPath) {
                if (parent::import($key, $searchPath)) {
                    $dependencyFactory = self::$factory->getDependencyFactory($class);
                    if (!isset(self::$injected[$class]) && $dependencyFactory !== null) {
                        self::$injected[$class] = true;
                        $dependencyFactory->injectStaticDependencies();
                    }

                    return true;
                }
                unset(self::$imported[$key]);
            }
            self::$imported[$key] = false;

            return false;
        }

        return parent::import($key, $base);
    }

    /**
     * @param string $class
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function load($class)
    {
        $existsBefore = class_exists($class, false);
        $success = parent::load($class);
        if (!$existsBefore && class_exists($class, false)) {
            $dependencyFactory = self::$factory->getDependencyFactory($class);
            if (!isset(self::$injected[$class]) && $dependencyFactory !== null) {
                self::$injected[$class] = true;
                $dependencyFactory->injectStaticDependencies();
            }
        }

        return $success;
    }

    /**
     * @param string $class
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function loadByPsr0($class)
    {
        $existsBefore = class_exists($class, false);
        $success = parent::loadByPsr0($class);
        if (!$existsBefore && class_exists($class, false)) {
            $dependencyFactory = self::$factory->getDependencyFactory($class);
            if (!isset(self::$injected[$class]) && $dependencyFactory !== null) {
                self::$injected[$class] = true;
                $dependencyFactory->injectStaticDependencies();
            }
        }

        return $success;
    }

    /**
     * @param string $class
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function loadByAlias($class)
    {
        if (!isset(self::$classAliases[$class])) {
            foreach (self::$classAliases as $key => $value) {
                if (strlen($key) == strlen($class) && false !== stripos($key, $class)) {
                    $class = $key;
                    break;
                }
            }
        }

        $existsBefore = class_exists($class, false);
        $success = parent::loadByAlias($class);
        if (!$existsBefore && class_exists($class, false)) {
            $dependencyFactory = self::$factory->getDependencyFactory($class);
            if (!isset(self::$injected[$class]) && $dependencyFactory !== null) {
                self::$injected[$class] = true;
                $dependencyFactory->injectStaticDependencies();
            }
        }

        return $success;
    }


    /**
     * Autoload a class based on name.
     *
     * @param   string $class The class to be loaded.
     *
     * @return  bool     True if the class was loaded, false otherwise.
     * @author Maximilian Ruta <mr@xtain.net>
     */
    private static function _autoload($class)
    {
        $realClass = $class;
        if (preg_match('/^JProxy_/i', $class)) {
            $class = preg_replace('/^JProxy_/i', '', $class);
        }
        foreach (self::$prefixes as $prefix => $lookup) {
            $chr = strlen($prefix) < strlen($class) ? $class[strlen($prefix)] : 0;

            if (strpos($class, $prefix) === 0 && ($chr === strtoupper($chr))) {
                $existsBefore = class_exists($realClass, false);
                if ($realClass === $class) {
                    self::loadByAlias($realClass);
                    self::load($realClass);
                }
                $return = true;
                if (!$existsBefore && !class_exists($class, false)) {
                    $return = self::_load(substr($class, strlen($prefix)), $lookup);
                }
                if ($realClass === $class && !$existsBefore && class_exists($realClass, false)) {
                    $dependencyFactory = self::$factory->getDependencyFactory($class);
                    if (!isset(self::$injected[$class]) && $dependencyFactory !== null) {
                        self::$injected[$class] = true;
                        $dependencyFactory->injectStaticDependencies();
                    }
                }

                return $return;
            }
        }

        return false;
    }

    /**
     * Load a class based on name and lookup array.
     *
     * @param   string $class  The class to be loaded (wihtout prefix).
     * @param   array  $lookup The array of base paths to use for finding the class file.
     *
     * @return  bool     True if the class was loaded, false otherwise.
     * @author Maximilian Ruta <mr@xtain.net>
     */
    private static function _load($class, array $lookup)
    {
        // Split the class name into parts separated by camelCase.
        $parts = preg_split('/(?<=[a-z0-9])(?=[A-Z])/x', $class);

        // If there is only one part we want to duplicate that part for generating the path.
        $parts = (count($parts) === 1) ? [$parts[0], $parts[0]] : $parts;

        foreach ($lookup as $base) {
            // Generate the path based on the class name parts.
            $path = $base . '/' . implode('/', array_map('strtolower', $parts)) . '.php';

            // Load the file if it exists.
            if (file_exists($path)) {
                return include $path;
            }
        }

        return false;
    }
}
