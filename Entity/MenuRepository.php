<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
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