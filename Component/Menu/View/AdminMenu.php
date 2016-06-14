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

use JMenuNode;
use JText;
use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Admin\MenuItem;
use XTAIN\Bundle\JoomlaBundle\Admin\MenuLink;
use XTAIN\Bundle\JoomlaBundle\Admin\MenuManager;

/**
 * Class AdminMenu
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Menu\View\AdminMenu
 */
class AdminMenu extends \JProxy_JAdminCssMenu
{
    /**
     * @var RouterInterface
     */
    protected static $router;

    /**
     * @var MenuManager
     */
    protected static $menuManager;

    /**
     * @param RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setRouter(RouterInterface $router)
    {
        self::$router = $router;
    }

    /**
     * @param MenuManager $menuManager
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setMenuManager(MenuManager $menuManager)
    {
        self::$menuManager = $menuManager;
    }

    /**
     * @param $link
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function route($link)
    {
        $admin = self::$router->generate('joomla_administrator');

        return $admin . $link;
    }

    /**
     * @param MenuItem|null $link
     *
     * @return string|null
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function transformLink(MenuItem $item = null)
    {
        $link = $item->getLink();

        if ($link === null) {
            return '#';
        }

        if ($link->isFramed()) {
            return $this->route(
                'index.php?option=com_symfony&view=frame&title=' . urlencode($item->getName()) . '&url=' . urlencode($link->getLink())
            );
        }

        return $link->getLink();
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function addAdditionalMenuItems(array $items, $level = 0)
    {
        /** @var MenuItem $item */
        foreach ($items as $item) {
            $children = $item->getChildren();
            if (count($children) == 0) {
                $this->addChild(
                    new \JMenuNode(
                        $item->getName(),
                        $this->transformLink($item)
                    )
                );
            } else {
                $class = null;

                if ($level > 0) {
                    $class = 'dropdown-submenu';
                }

                $this->addChild(
                    new \JMenuNode(
                        $item->getName(),
                        $this->transformLink($item),
                        $class
                    ),
                    true
                );

                $this->addAdditionalMenuItems($children, $level + 1);

                $this->getParent();
            }
        }
    }

    /**
     * @param string $id
     * @param string $class
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renderMenu($id = 'menu', $class = '')
    {
        $this->addAdditionalMenuItems(self::$menuManager->getMenuItems());

        parent::renderMenu($id, $class);
    }
}

\XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(AdminMenu::class);
