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

/**
 * Interface DependencyFactoryInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Factory
 */
interface DependencyFactoryInterface extends AbstractFactoryInterface
{

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies();
}
