<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Hans Mackowiak <hmackowiak@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use XTAIN\Bundle\JoomlaBundle\Entity\MenuRepositoryInterface;

/**
 * Class UrlPatcher
 *
 * @author  Hans Mackowiak <hmackowiak@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Routing
 */
class UrlPatcher
{
    /**
     * @var MenuRepositoryInterface
     */
    private $repository;

    /**
     * UrlPatcher constructor.
     *
     * @author Hans Mackowiak <hmackowiak@xtain.net>
     * @param  MenuRepositoryInterface $repository
     */
    public function __construct(MenuRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    /**
     * does overwrite the route tokens
     * does return true on success, or false if wrong route.
     *
     * @author  Hans Mackowiak <hmackowiak@xtain.net>
     * @param string $name
     * @param array $tokens
     * @return null|string
     */
    public function overrideRouteTokens($name, &$tokens) {
        $menu = $this->repository->findByViewRoute($name);
        if (isset($menu)) {
            # search the key for the first token with using 'text' as type
            $key = array_search('text', array_column($tokens, 0));
            # replace thwe path with the path from the menu, don't forget the beginning /
            array_splice($tokens[$key], -1, 1, '/'.$menu->getPath());
            return true;
        }
        return null;
    }
}