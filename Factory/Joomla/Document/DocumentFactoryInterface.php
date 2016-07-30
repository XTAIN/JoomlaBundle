<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Document;

use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Interface DocumentFactoryInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Document
 */
interface DocumentFactoryInterface extends DependencyFactoryInterface
{
    /**
     * @param AssetsHelper $helper
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setAssetsHelper(AssetsHelper $helper);
}
