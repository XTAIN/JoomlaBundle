<?php
/**
 * @package     Joomla
 * @subpackage  com_symfony
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Symfony Component
 *
 * @since  0.0.1
 */
class SymfonyViewWrap extends JViewLegacy
{
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected static $response;

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setResponse(\Symfony\Component\HttpFoundation\Response $response)
    {
        self::$response = $response;
    }

    /**
     * Display the Hello World view
     *
     * @param string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        // Display the view
        parent::display($tpl);
    }
}

\XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies('SymfonyViewWrap');