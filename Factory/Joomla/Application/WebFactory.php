<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Application;

use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Application\Web;

/**
 * Class WebFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Application
 */
class WebFactory implements DependencyFactoryInterface
{
    /**
     * @var \JConfig
     */
    protected $config;

    /**
     * @param \JConfig $config
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setConfig(\JConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Web::setConfig($this->config);
    }
}
