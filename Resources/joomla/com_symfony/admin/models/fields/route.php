<?php
/**
 * @package     Joomla
 * @subpackage  com_symfony
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports a route picker.
 *
 * @since  1.6
 */
class JFormFieldRoute extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var		string
     * @since   1.6
     */
    protected $type = 'Route';

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected static $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setRouter(\Symfony\Component\Routing\RouterInterface $router)
    {
        self::$router = $router;
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function getRoutes()
    {
        $routeCollection = self::$router->getRouteCollection();
        return array_keys($routeCollection->all());
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions()
    {
        $options = array();

        foreach ($this->getRoutes() as $route)
        {
            // Create a new option object based on the <option /> element.
            $tmp = JHtml::_(
                'select.option', $route,
                JText::_(trim((string) $route)), 'value', 'text'
            );

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }
}

\XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies('JFormFieldRoute');
