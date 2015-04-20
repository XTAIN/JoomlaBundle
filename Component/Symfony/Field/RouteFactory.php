<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Symfony\Field;

use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Class RouteFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class RouteFactory implements DependencyFactoryInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Routing\RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        \JFormFieldRoute::setRouter($this->router);
    }
}