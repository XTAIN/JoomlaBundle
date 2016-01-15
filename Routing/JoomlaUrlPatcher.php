<?php
/**
 * Created by IntelliJ IDEA.
 * User: hanmac
 * Date: 15.01.16
 * Time: 10:58
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use XTAIN\Bundle\JoomlaBundle\Entity\MenuRepositoryInterface;

class JoomlaUrlPatcher
{
    /**
     * @var MenuRepositoryInterface
     */
    private $repository;

    /**
     * JoomlaUrlPatcher constructor.
     * @param MenuRepositoryInterface $repository
     */
    public function __construct(MenuRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param $name
     * @return null|\XTAIN\Bundle\JoomlaBundle\Entity\Menu
     */
    public function getRouteByName($name) {
        return $this->repository->findByViewRoute($name);
    }
}