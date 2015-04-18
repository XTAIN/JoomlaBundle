<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class DefaultRouter
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class DefaultRouter extends Router implements RouterInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return null
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
