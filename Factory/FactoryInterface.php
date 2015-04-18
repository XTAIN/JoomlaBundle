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
 * Interface FactoryInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Factory
 */
interface FactoryInterface extends AbstractFactoryInterface
{

    /**
     * @return object
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance();
}
