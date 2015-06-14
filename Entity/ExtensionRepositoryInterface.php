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
 * Interface ExtensionRepositoryInterface
 * @package XTAIN\Bundle\JoomlaBundle\Entity
 */
interface ExtensionRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @param string $name
     *
     * @return Extension|null
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByName($name);
}