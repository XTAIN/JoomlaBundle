<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\CMS\Module;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use XTAIN\Bundle\JoomlaBundle\Event\AfterRenderModuleEvent;
use XTAIN\Bundle\JoomlaBundle\Event\BeforeRenderModuleEvent;

/**
 * Class Helper
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\CMS\Application
 */
class Helper extends \JProxy_JModuleHelper
{
    /**
     * @var FileLocator
     */
    protected static $fileLocator;

    /**
     * @var EventDispatcherInterface
     */
    protected static $eventDispatcher;

    /**
     * @param FileLocator $fileLocator
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setFileLocator(FileLocator $fileLocator)
    {
        self::$fileLocator = $fileLocator;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setEventDisptacher(EventDispatcherInterface $eventDispatcher)
    {
        self::$eventDispatcher = $eventDispatcher;
    }

    /**
     * @param object $module
     * @param array $attribs
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function renderModule($module, $attribs = array())
    {
        $event = new BeforeRenderModuleEvent($module, $attribs);
        self::$eventDispatcher->dispatch('joomla.before_render_module', $event);

        $module = $event->getModule();
        $attribs = $event->getAttribs();

        if ($event->getContent() !== null) {
            return $event->getContent();
        }

        $content = parent::renderModule($module, $attribs);

        $event = new AfterRenderModuleEvent($module, $attribs, $content);
        self::$eventDispatcher->dispatch('joomla.after_render_module', $event);

        return $event->getContent();
    }

    /**
     * Get the path to a layout for a module
     *
     * @param   string  $module  The name of the module
     * @param   string  $layout  The name of the module layout. If alternative layout, in the form template:filename.
     *
     * @return  string  The path to the module layout
     *
     * @since   1.5
     */
    public static function getLayoutPath($module, $layout = 'default')
    {
        try {
            return self::$fileLocator->locate($layout);
        } catch (\InvalidArgumentException $e) {
            return parent::getLayoutPath($module, $layout);
        }
    }
}