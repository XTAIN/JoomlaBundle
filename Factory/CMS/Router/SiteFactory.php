<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\CMS\Router;

use XTAIN\Bundle\JoomlaBundle\Entity\MenuRepositoryInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\FactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\CMS\Router\Site;

/**
 * Class SiteFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\CMS\Router
 */
class SiteFactory implements FactoryInterface, DependencyFactoryInterface
{
    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @param MenuRepositoryInterface $menuRepository
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setMenuRepository(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * @return \JRouterSite
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance()
    {
        return Site::getInstance('site');
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Site::setMenuRepository($this->menuRepository);
    }
}
