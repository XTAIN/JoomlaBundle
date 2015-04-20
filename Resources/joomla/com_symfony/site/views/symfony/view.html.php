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
class SymfonyViewSymfony extends JViewLegacy
{
    /**
     * @var string
     */
    protected $output;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected static $router;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected static $requestStack;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected static $kernel;

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
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setRequestStack(\Symfony\Component\HttpFoundation\RequestStack $requestStack)
    {
        self::$requestStack = $requestStack;
    }

    /**
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setKernel(\Symfony\Component\HttpKernel\HttpKernelInterface $kernel)
    {
        self::$kernel = $kernel;
    }

    /**
     * Display the Hello World view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $path = array();
        $query = array();

        $input = JFactory::getApplication()->input;
        $route = self::$router->getRouteCollection()->get($input->get('route'));
        unset($path['_controller']);

        $defaults = $route->getDefaults();

        foreach ($defaults as $key => $default) {
            if (!isset($path[$key])) {
                $path[$key] = $default;
            }
        }

        $subRequest = self::$requestStack->getCurrentRequest()->duplicate($query, null, $path);

        $this->response = self::$kernel->handle(
            $subRequest,
            \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST,
            false
        );

        // Display the view
        parent::display($tpl);
    }
}

\XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies('SymfonyViewSymfony');