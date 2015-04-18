<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Uri;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Library\CMS\Application\Administrator;

/**
 * Class Uri
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Uri
 */
class Uri extends \JProxy_JUri
{
    /**
     * @var RouterInterface
     */
    protected static $router;

    /**
     * @param RouterInterface $router
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setRouter(RouterInterface $router = null)
    {
        self::$router = $router;
    }

    /**
     * @return null|\Symfony\Component\Routing\Route
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function getAdministratorRoute()
    {
        static $administratorRoute;

        $collection = self::$router->getRouteCollection();

        if (!isset($administratorRoute)) {
            $administratorRoute = $collection->get(Administrator::ROUTE);
        }

        return $administratorRoute;
    }

    /**
     * @param bool $pathonly
     * @param bool $admin
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function base($pathonly = false, $admin = true)
    {
        $administratorRoute = self::getAdministratorRoute();

        /** @var RequestContext $context */
        $context = self::$router->getContext();
        $scheme = $context->getScheme();
        $port = null;
        switch ($scheme) {
            case 'https':
                $port = $context->getHttpsPort() == 443 ? null : $context->getHttpsPort();
                break;
            case 'http':
                $port = $context->getHttpPort() == 80 ? null : $context->getHttpPort();
                break;
        }
        $prefix = $scheme . '://' . $context->getHost();
        if ($port !== null) {
            $prefix = $prefix . ':' . $port;
        }
        $path = $context->getBaseUrl();

        if ($admin &&
            preg_match('/^' . preg_quote($administratorRoute->getPath(), '/') . '/', $context->getPathInfo())
        ) {
            $path .= preg_replace('#/$#', '', $administratorRoute->getPath());
        }

        self::$base['prefix'] = $prefix;
        self::$base['path'] = $path;

        return parent::base($pathonly);
    }

    public static function root($pathonly = false, $path = null)
    {
        /** @var RequestContext $context */
        $context = self::$router->getContext();

        $path = preg_replace('#/.*?$#', '', $context->getBaseUrl());

        return parent::root($pathonly, $path);
    }
}
