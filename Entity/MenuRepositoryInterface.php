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

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Interface MenuRepositoryInterface
 * @package XTAIN\Bundle\JoomlaBundle\Entity
 */
interface MenuRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @param string $component
     * @param string $view
     *
     * @return Menu[]
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByComponentAndView($component, $view);

    /**
     * @param string $routeName
     *
     * @return null|Menu
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByViewRoute($routeName);
}