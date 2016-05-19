<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Debug;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use XTAIN\Bundle\JoomlaBundle\Debug\ErrorHandler;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

class ErrorHandlerFactory implements DependencyFactoryInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * ErrorHandlerFactory constructor.
     *
     * @param LoggerInterface $logger
     * @param string          $rootPath
     */
    public function __construct(LoggerInterface $logger, $rootPath)
    {
        $this->logger = $logger;
        $this->rootPath = $rootPath;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        ErrorHandler::setJoomlaLogger($this->logger);
        ErrorHandler::setJoomlaPath($this->rootPath);
    }
}