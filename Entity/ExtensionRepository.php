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
 * Class ExtensionRepository
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Entity
 */
class ExtensionRepository extends EntityRepository implements ExtensionRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return Extension|null
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByName($name)
    {
        return $this->findOneBy(
            [
                'name' => $name
            ]
        );
    }
}