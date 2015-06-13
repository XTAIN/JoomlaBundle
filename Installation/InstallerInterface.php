<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Installation;

/**
 * Interface InstallerInterface
 * @package XTAIN\Bundle\JoomlaBundle\Installation
 */
interface InstallerInterface
{
    /**
     * @param Configuration $configuration
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function install(Configuration $configuration);
}