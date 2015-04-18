<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Uri;

use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\FactoryInterface;

/**
 * Interface UriFactoryInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Uri
 */
interface UriFactoryInterface extends FactoryInterface, DependencyFactoryInterface
{
    /**
     * @param RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRouter(RouterInterface $router = null);
}
