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
}
