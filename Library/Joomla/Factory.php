<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla;

use Joomla\Registry\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Factory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla
 */
class Factory extends \JProxy_JFactory
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @var \SplStack
     */
    protected static $applicationStack;

    /**
     * @param ContainerInterface $container
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * @return ContainerInterface
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * @param null $id
     * @return \JUser
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function getUser($id = null)
    {
        // force load of JUser so that the instanceof in parent method works correctly
        class_exists('JUser');

        return parent::getUser($id);
    }

    /**
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function hasApplication()
    {
        return !empty(self::$application);
    }

    /**
     * @param \JApplicationCms $application
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function pushApplication(\JApplicationCms $application)
    {
        if (!(static::$applicationStack instanceof \SplStack)) {
            static::$applicationStack = new \SplStack();
        }

        static::$applicationStack->push(static::$application);
        static::$application = $application;
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function popApplication()
    {
        if (!(static::$applicationStack instanceof \SplStack)) {
            static::$applicationStack = new \SplStack();
        }

        static::$application = static::$applicationStack->pop();
    }

    /**
     * Create a configuration object
     *
     * @param   string  $file       The path to the configuration file.
     * @param   string  $type       The type of the configuration file.
     * @param   string  $namespace  The namespace of the configuration file.
     *
     * @return  Registry
     *
     * @see     Registry
     * @since   11.1
     */
    protected static function createConfig($file, $type = 'PHP', $namespace = '')
    {
        // Sanitize the namespace.
        $namespace = ucfirst((string) preg_replace('/[^A-Z_]/i', '', $namespace));

        // Build the config name.
        $name = 'JConfig' . $namespace;

        if (!class_exists($name) && is_file($file))
        {
            include_once $file;
        }

        // Create the registry with a default namespace of config
        $registry = new Registry;

        // Handle the PHP configuration type.
        if ($type == 'PHP' && class_exists($name))
        {
            // Create the JConfig object
            $config = new $name;

            // Load the configuration values into the registry
            $registry->loadObject($config);
        }

        return $registry;
    }
}
