<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Log;

use XTAIN\Bundle\JoomlaBundle\Joomla\LoggerProxyInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Log\Log;

/**
 * Class LogFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Log
 */
class LogFactory implements LogFactoryInterface
{
    /**
     * @var LoggerProxyInterface
     */
    protected $loggerProxy;

    /**
     * @param LoggerProxyInterface $loggerProxy
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setLoggerProxy(LoggerProxyInterface $loggerProxy)
    {
        $this->loggerProxy = $loggerProxy;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        $callback = function (\JLogEntry $entry) {
            $this->loggerProxy->addLogEntry($entry);
        };

        Log::addLogger(
            [
                'callback' => $callback,
                'logger'   => 'callback'
            ]
        );

        Log::freeze();
    }
}
