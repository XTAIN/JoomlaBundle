<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Menu\View;

use XTAIN\Bundle\JoomlaBundle\Admin\MenuManager;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AdminMenuFactrory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Menu\View\AdminMenu
 */
class AdminMenuFactory implements DependencyFactoryInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var MenuManager
     */
    protected $menuManager;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param MenuManager $menuManager
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setAdminMenuManager(MenuManager $menuManager)
    {
        $this->menuManager = $menuManager;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        AdminMenu::setMenuManager($this->menuManager);
        AdminMenu::setRouter($this->router);
    }
}