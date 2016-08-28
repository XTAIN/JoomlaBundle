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
     * Get modules (by position)
     *
     * @param   string|null  $position  The position of the module
     *
     * @return  array  An array of module objects
     *
     * @since   1.5
     */
    public static function &getModules($position = null)
    {
        if ($position === null) {
            return static::load();
        }

        return parent::getModules($position);
    }


    /**
     * Module list
     *
     * @return  array
     */
    public static function getModuleList()
    {
        $app = \JFactory::getApplication();
        $Itemid = $app->input->getInt('Itemid');
        $groups = implode(',', \JFactory::getUser()->getAuthorisedViewLevels());
        $lang = \JFactory::getLanguage()->getTag();
        $clientId = (int) $app->getClientId();

        $db = \JFactory::getDbo();

        $query = new \stdClass;
        $query->select = array();
        $query->from = array();
        $query->join = array();
        $query->where = array();
        $query->order = array();

        $query->select[] = 'm.published, m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid';
        $query->from[] = '#__modules AS m';
        $query->join[] = '#__modules_menu AS mm ON mm.moduleid = m.id';
        $query->where[] = 'm.published = 1';

        $query->join[] = '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id';
        $query->where[] = 'e.enabled = 1';

        $date = \JFactory::getDate();
        $now = $date->toSql();
        $nullDate = $db->getNullDate();
        $query->where[] = '(m.publish_up = ' . $db->q($nullDate) . ' OR m.publish_up <= ' . $db->q($now) . ')';
        $query->where[] = '(m.publish_down = ' . $db->q($nullDate) . ' OR m.publish_down >= ' . $db->q($now) . ')';

        $query->where[] = 'm.access IN ('.$groups.')';
        $query->where[] = 'm.client_id = ' . $clientId;
        $query->where[] = '(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)';

        // Filter by language
        if ($app->isSite() && $app->getLanguageFilter())
        {
            $query->where[] = 'm.language IN (' . $db->q($lang) . ',' . $db->q('*') . ')';
        }

        $query->order[] = 'm.position, m.ordering';

        // Do 3rd party stuff to change query
        $app->triggerEvent( 'onCreateModuleQuery', array( &$query ) );

        $q = $db->getQuery(true);
        // convert array object to query object
        foreach ( $query as $type => $strings )
        {
            foreach ( $strings as $string )
            {
                if ( $type == 'join' )
                {
                    $q->{$type}( 'LEFT', $string );
                }
                else
                {
                    $q->{$type}( $string );
                }
            }
        }

        // Set the query
        $db->setQuery($q);

        try
        {
            $modules = $db->loadObjectList();
        }
        catch (\RuntimeException $e)
        {
            \JLog::add(\JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), \JLog::WARNING, 'jerror');

            return array();
        }

        return $modules;
    }


    /**
     * Clean the module list
     *
     * @param   array  $modules  Array with module objects
     *
     * @return  array
     */
    public static function cleanModuleList($modules)
    {
        $app = \JFactory::getApplication();

        // Apply negative selections and eliminate duplicates
        $Itemid = \JFactory::getApplication()->input->getInt('Itemid');
        $negId = $Itemid ? -(int) $Itemid : false;
        $clean = array();
        $dupes = array();

        foreach ($modules as $i => $module)
        {
            // The module is excluded if there is an explicit prohibition
            $negHit = ($negId === (int) $module->menuid);

            if (isset($dupes[$module->id]))
            {
                // If this item has been excluded, keep the duplicate flag set,
                // but remove any item from the modules array.
                if ($negHit)
                {
                    unset($clean[$module->id]);
                }

                continue;
            }

            $dupes[$module->id] = true;

            // Only accept modules without explicit exclusions.
            if ($negHit)
            {
                continue;
            }

            $module->name = substr($module->module, 4);
            $module->style = null;
            $module->position = strtolower($module->position);

            $clean[$module->id] = $module;
        }

        unset($dupes);

        // Do 3rd party stuff to manipulate module array.
        // Any plugins using this architecture may make alterations to the referenced $modules array.
        // To remove items you can do unset($modules[n]) or $modules[n]->published = false.

        // "onPrepareModuleList" may alter or add $modules, and does not need to return anything.
        // This should be used for module addition/deletion that the user would expect to happen at an
        // early stage.
        $app->triggerEvent( 'onPrepareModuleList', array( &$clean ) );

        // "onAlterModuleList" may alter or add $modules, and does not need to return anything.
        $app->triggerEvent( 'onAlterModuleList', array( &$clean ) );

        // "onPostProcessModuleList" allows a plugin to perform actions like parameter changes
        // on the completed list of modules and is guaranteed to occur *after*
        // the earlier plugins.
        $app->triggerEvent( 'onPostProcessModuleList', array( &$clean ) );

        // Remove any that were marked as disabled during the preceding steps
        /*foreach ( $clean as $id => $module )
        {
            if ( !isset( $module->published ) || $module->published == 0 )
            {
                unset( $clean[$id] );
            }
        }*/

        // Return to simple indexing that matches the query order.
        return array_values($clean);
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
            $file = self::$fileLocator->locate($layout);

            if (is_file($file)) {
                return $file;
            }
        } catch (\InvalidArgumentException $e) {
        }

        // Do 3rd party stuff to detect layout path for the module
        // onGetLayoutPath should return the path to the $layout of $module or false
        // $results holds an array of results returned from plugins, 1 from each plugin.
        // if a path to the $layout is found and it is a file, return that path
        $app = \JFactory::getApplication();
        $result = $app->triggerEvent( 'onGetLayoutPath', array( $module, $layout ) );
        if (is_array($result))
        {
            foreach ($result as $path)
            {
                if ($path !== false && is_file ($path))
                {
                    return $path;
                }
            }
        }

        return parent::getLayoutPath($module, $layout);
    }
}