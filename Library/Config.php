<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library;

class Config extends \JProxy_Config
{
    /**
     *
     */
    public function __construct()
    {
        \XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(__CLASS__);
    }
}