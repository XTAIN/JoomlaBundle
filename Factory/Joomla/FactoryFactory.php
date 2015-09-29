<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla;

use Symfony\Component\DependencyInjection\ContainerInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Factory;

/**
 * Class FactoryFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla
 */
class FactoryFactory implements DependencyFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Factory::setContainer($this->container);
    }
}
