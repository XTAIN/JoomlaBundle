<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\CMS\Router;
use XTAIN\Bundle\JoomlaBundle\Entity\Menu;
use XTAIN\Bundle\JoomlaBundle\Entity\MenuRepositoryInterface;
use XTAIN\Bundle\JoomlaBundle\Routing\PathMatcher;

/**
 * Class Site
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\CMS\Router
 */
class Site extends \JProxy_JRouterSite
{
    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @param \XTAIN\Bundle\JoomlaBundle\Entity\MenuRepositoryInterface $menuRepository
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setMenuRepository(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * @param \JUri $uri
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function _buildSefRoute(&$uri)
    {
        $option = $uri->getVar('option');
        $menuId = $uri->getVar('Itemid');
        parent::_buildSefRoute($uri);

        if ($option == 'com_symfony' && !empty($menuId) && isset($this->menuRepository)) {
            /** @var Menu $item */
            $item = $this->menuRepository->find($menuId);
            if ($item !== null) {
                $params = PathMatcher::parseParameters($item->getLink());
                if (isset($params['path'])) {
                    $uri->setPath($params['path']);
                }
            }
        }
    }
}
