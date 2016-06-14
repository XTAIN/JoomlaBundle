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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

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
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Helper::setFileLocator($this->fileLocator);
        Helper::setEventDisptacher($this->eventDispatcher);
    }
}