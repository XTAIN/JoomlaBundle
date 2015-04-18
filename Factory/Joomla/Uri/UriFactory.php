<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Uri;

use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Uri\Uri;

/**
 * Class UriFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Uri
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRouter(RouterInterface $router = null)
    {
        $this->router = $router;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Uri::setRouter($this->router);
    }

    /**
     * @return \JUri
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance()
    {
        return Uri::getInstance();
    }
}
