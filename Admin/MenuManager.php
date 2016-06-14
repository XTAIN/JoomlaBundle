<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Admin;

class MenuManager
{
    /**
     * @var array
     */
    protected $menus;

    /**
     * MenuManager constructor.
     *
     * @param array $menus
     */
    public function __construct(array $menus = array())
    {
        $this->menus = $menus;
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getMenuItems()
    {
        $items = array();

        /** @var MenuInterface $menu */
        foreach ($this->menus as $menu) {
            $items = array_merge($items, $menu->getItems());
        }

        return $items;
    }
}