<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\CMS\Installer;

use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Installation\Asset;
use XTAIN\Bundle\JoomlaBundle\Joomla\ResourceLocator;
use XTAIN\Bundle\JoomlaBundle\Library\CMS\Installer\Installer;

/**
 * @author Maximilian Ruta <mr@xtain.net>
 */
class InstallerFactory implements DependencyFactoryInterface
{
    /**
     * @var ResourceLocator
     */
    protected $resourceLocator;

    /**
     * @var Asset
     */
    protected $assetInstaller;

    /**
     * InstallerFactory constructor.
     *
     * @param ResourceLocator $resourceLocator
     */
    public function setResourceLocator(ResourceLocator $resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * InstallerFactory constructor.
     *
     * @param Asset $assetInstaller
     */
    public function setAssetInstaller(Asset $assetInstaller)
    {
        $this->assetInstaller = $assetInstaller;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Installer::setResourceLocator($this->resourceLocator);
        Installer::setAssetInstaller($this->assetInstaller);
    }
}