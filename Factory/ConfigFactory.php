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
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $tmpPath;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @param string $secret
     * @param string $tmpPath
     * @param bool   $debug
     */
    public function __construct($secret, $tmpPath, $debug)
    {
        $this->secret = $secret;
        $this->tmpPath = $tmpPath;
        $this->debug = $debug;
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
        $config = $this->config;

        if (!isset($config['log_path'])) {
            $config['log_path'] = $this->kernel->getLogDir();
        }

        if (!isset($config['secret'])) {
            $config['secret'] = $this->secret;
        }

        if (!isset($config['tmp_path'])) {
            $config['tmp_path'] = $this->tmpPath;
        }

        if (!isset($config['debug'])) {
            $config['debug'] = $this->debug ? 2 : 0;
        }

        Config::setConfiguration($config);
    }

    /**
     * @return Config
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getConfig()
    {
        $this->injectStaticDependencies();

        return new Config();
    }
}