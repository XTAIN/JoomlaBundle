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

use Symfony\Component\HttpKernel\KernelInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Loader;

/**
 * Class LoaderFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory
 */
class LoaderFactory implements LoaderFactoryInterface
{
    /**
     * @const string
     */
    const LIBRARIES = 'libraries';

    /**
     * @const string
     */
    const LIBRARIES_CMS = 'libraries/cms';

    /**
     * @const string
     */
    const LIBRARIES_JOOMLA = 'libraries/joomla';

    /**
     * @const string
     */
    const LIBRARIES_LEGACY = 'libraries/legacy';

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $overrideDir;

    /**
     * @var string[]
     */
    protected $dependencyFactoryServices = [];

    /**
     * @param string $overrideDirName
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setOverrideDir($overrideDirName)
    {
        $this->overrideDir = $overrideDirName;
    }

    /**
     * @param KernelInterface $kernel
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getOverrideDir()
    {
        return $this->overrideDir;
    }

    /**
     * @param string $joomlaClass
     * @param string $dependencyFactoryService
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addDependencyFactory($joomlaClass, $dependencyFactoryService)
    {
        $this->dependencyFactoryServices[$joomlaClass] = $dependencyFactoryService;
    }

    /**
     * @param string $joomlaClass
     *
     * @return null|DependencyFactoryInterface
     * @throws \LogicException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getDependencyFactory($joomlaClass)
    {
        if (isset($this->dependencyFactoryServices[$joomlaClass])) {
            /** @var DependencyFactoryInterface $container */
            $factoryService = $this->kernel->getContainer()->get($this->dependencyFactoryServices[$joomlaClass]);
            if (!($factoryService instanceof DependencyFactoryInterface)) {
                throw new \LogicException(
                    sprintf(
                        'Factory service "%s" is not an instance of "%s"',
                        get_class($factoryService),
                        DependencyFactoryInterface::CLASS
                    )
                );
            }

            return $factoryService;
        }

        return null;
    }

    /**
     * @return Loader
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            require_once $this->getOverrideDir() . DIRECTORY_SEPARATOR
                . self::LIBRARIES . DIRECTORY_SEPARATOR . 'loader.php';
            $instance = new Loader($this);
            class_alias(Loader::CLASS, 'JLoader');
            $instance->addOverridePath($this->getOverrideDir());
            require_once $this->getOverrideDir() . DIRECTORY_SEPARATOR
                . 'classmap.php';
        }

        return $instance;
    }
}
