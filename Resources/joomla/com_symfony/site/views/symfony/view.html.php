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
        $attributes = array();
        $query = array();

        $app = JFactory::getApplication();
        $input = $app->input;

        $routeName = $input->get('route', null, 'string');
        $path = $input->get('path', null, 'string');

        $route = self::$router->getRouteCollection()->get($routeName);
        $defaults = $route->getDefaults();

        if ($path !== null) {
            $path = rtrim($route->getPath(), '/') . '/' . $path;
            $defaults = self::$router->match($path, null, false);
        }

        unset($attributes['_controller']);

        foreach ($defaults as $key => $default) {
            if (!isset($attributes[$key])) {
                $attributes[$key] = $default;
            }
        }

        $subRequest = self::$requestStack->getCurrentRequest()->duplicate($query, null, $attributes);

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