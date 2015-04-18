<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\CMS\Application;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\FactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\CMS\Application\Site;

/**
 * Class AdministratorFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\CMS\Application
 */
class AdministratorFactory implements FactoryInterface
{
    /**
     * @return \JApplicationSite
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = Site::getInstance('administrator');
        }

        return $instance;
    }
}
