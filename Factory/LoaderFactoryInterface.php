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

/**
 * Interface LoaderFactoryInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Factory
 */
interface LoaderFactoryInterface extends FactoryInterface
{
    /**
     * @param string $overrideDirName
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setOverrideDir($overrideDirName);

    /**
     * @param KernelInterface $kernel
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setKernel(KernelInterface $kernel);

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getOverrideDir();

    /**
     * @param string $joomlaClass
     * @param string $dependencyFactoryService
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addDependencyFactory($joomlaClass, $dependencyFactoryService);

    /**
     * @param string $joomlaClass
     *
     * @return null|DependencyFactoryInterface
     * @throws \LogicException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getDependencyFactory($joomlaClass);
}
