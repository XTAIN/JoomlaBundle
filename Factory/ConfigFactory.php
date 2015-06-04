<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Config;

/**
 * Class ConfigFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory
 */
class ConfigFactory implements DependencyFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $config = [];

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
     * @param KernelInterface $kernel
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param array $config
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setConfiguration(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Config::setKernel($this->kernel);
        Config::setContainer($this->container);
        Config::setConfiguration($this->config);
    }
}