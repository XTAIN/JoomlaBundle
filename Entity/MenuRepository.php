<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class MenuRepository
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Entity
 */
class MenuRepository extends EntityRepository implements MenuRepositoryInterface
{
    /**
     * @param string $component
     * @param string $view
     *
     * @return Menu[]
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByComponentAndView($component, $view)
    {
        $menus = $this->findAll();

        $matchedMenus = [];
        /** @var Menu $menu */
        foreach ($menus as $menu) {
            if (preg_match(
                '/^index\.php\?option=' . preg_quote($component, '/') . '&view=' . preg_quote($view, '/') . '&/',
                $menu->getLink()
            )) {
                $matchedMenus[] = $menu;
            }
        }

        return $matchedMenus;
    }

    /**
     * @param string $routeName
     *
     * @return null|Menu
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByViewRoute($routeName)
    {
        return $this->findOneBy(
            [
                'link' => 'index.php?option=com_symfony&view=symfony&route=' . $routeName
            ]
        );
    }

}