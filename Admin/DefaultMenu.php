<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Admin;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use XTAIN\Bundle\JoomlaBundle\Admin\MenuInterface;
use XTAIN\Bundle\JoomlaBundle\Admin\MenuItem;
use XTAIN\Bundle\JoomlaBundle\Admin\MenuLink;
use XTAIN\Bundle\JoomlaBundle\Routing\RouterInterface;

class DefaultMenu implements MenuInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * TestFrame constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getItems()
    {
        $items = array();

        try {
            $items[] = new MenuItem('Profiler', new MenuLink($this->router->generate('_profiler_home'), true));
        } catch (RouteNotFoundException $e) {}

        try {
            $items[] = new MenuItem('Sonata', new MenuLink($this->router->generate('sonata_admin_dashboard'), true));
        } catch (RouteNotFoundException $e) {}


        return $items;
    }
}