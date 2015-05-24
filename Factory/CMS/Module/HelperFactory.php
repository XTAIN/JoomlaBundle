<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\CMS\Module;

use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\CMS\Module\Helper;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Class HelperFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\CMS\Module
 */
class HelperFactory implements DependencyFactoryInterface
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @param FileLocator $fileLocator
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setFileLocator(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Helper::setFileLocator($this->fileLocator);
    }
}