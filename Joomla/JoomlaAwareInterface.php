<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;

/**
 * Interface JoomlaAwareInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
interface JoomlaAwareInterface
{

    /**
     * @param JoomlaInterface $joomla
     *
     * @return void
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null);
}
