<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use XTAIN\Bundle\JoomlaBundle\Entity\Menu;

/**
 * Interface PathMatcherInterface
 * @package XTAIN\Bundle\JoomlaBundle\Routing
 */
interface PathMatcherInterface
{
    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getSortedRoutes();

    /**
     * @param Route $searchRoute
     *
     * @return null|string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findMatchingPaths(Route $searchRoute);

    /**
     * @param string $name
     * @param bool   $referenceType
     *
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getBasePath($name, $referenceType = RouterInterface::ABSOLUTE_PATH);

    /**
     * @param Request $request
     *
     * @return null|Menu
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findMenuPointForRequest(Request $request);
}