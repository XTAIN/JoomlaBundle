<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Log\Log;

/**
 * Class LoggerProxy
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
class LoggerProxy implements LoggerProxyInterface, LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \JLogEntry $entry
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addLogEntry(\JLogEntry $entry)
    {
        if ($this->logger !== null) {
            $level = $entry->priority;
            $message = $entry->message;
            $context = [];

            switch (true) {
                case $level <= Log::EMERGENCY:
                    $this->logger->emergency($message, $context);
                    break;
                case $level <= Log::ALERT:
                    $this->logger->alert($message, $context);
                    break;
                case $level <= Log::CRITICAL:
                    $this->logger->critical($message, $context);
                    break;
                case $level <= Log::ERROR:
                    $this->logger->error($message, $context);
                    break;
                case $level <= Log::WARNING:
                    $this->logger->warning($message, $context);
                    break;
                case $level <= Log::NOTICE:
                    $this->logger->notice($message, $context);
                    break;
                case $level <= Log::INFO:
                    $this->logger->info($message, $context);
                    break;
                case $level <= Log::DEBUG:
                default:
                    $this->logger->debug($message, $context);
                    break;
            }
        }
    }
}
