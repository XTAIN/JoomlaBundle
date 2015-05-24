<?php
/**
 * @package     Joomla
 * @subpackage  com_symfony
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class SymfonyRouter
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class SymfonyRouter implements JComponentRouterInterface
{
    /**
     * @var \JMenuSite
     */
    protected $menu;

    /**
     * @var \JApplicationCms
     */
    protected $application;

    /**
     * @param JApplicationCms $application
     * @param JMenuSite       $menu
     */
    public function __construct(JApplicationCms $application, JMenuSite $menu)
    {
        $this->application = $application;
        $this->menu = $menu;
    }

    /**
     * Prepare-method for URLs
     * This method is meant to validate and complete the URL parameters.
     * For example it can add the Itemid or set a language parameter.
     * This method is executed on each URL, regardless of SEF mode switched
     * on or not.
     *
     * @param   array $query An associative array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
    public function preprocess($query)
    {
        return $query;
    }

    /**
     * Build method for URLs
     * This method is meant to transform the query parameters into a more human
     * readable form. It is only executed when SEF mode is switched on.
     *
     * @param   array &$query An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
    public function build(&$query)
    {
        return array();
    }

    /**
     * Parse method for URLs
     * This method is meant to transform the human readable URL back into
     * query parameters. It is only executed when SEF mode is switched on.
     *
     * @param   array &$segments The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     *
     * @since   3.3
     */
    public function parse(&$segments)
    {
        $active = $this->menu->getActive();
        $vars = array();

        if (!empty($active)) {
            $vars = $active->query;
        }

        return array_merge(
            $vars,
            array(
                'path' => implode('/', $segments)
            )
        );
    }
}