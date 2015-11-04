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

use Symfony\Component\Asset\PackageInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Interface HtmlFactoryInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Document
 */
interface HtmlFactoryInterface extends DependencyFactoryInterface
{
    /**
     * @param PackageInterface $package
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setAssetsPackage(PackageInterface $package);
}
