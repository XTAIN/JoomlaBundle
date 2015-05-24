<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MenuRepository extends EntityRepository
{
    /**
     * @param string $routeName
     *
     * @return null|Menu
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function findByViewRoute($routeName)
    {
        return $this->findOneBy(array(
            'link' => 'index.php?option=com_symfony&view=symfony&route=' . $routeName
        ));
    }

}