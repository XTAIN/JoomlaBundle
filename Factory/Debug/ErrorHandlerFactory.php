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
     * @var string
     */
    protected $appPath;

    /**
     * ErrorHandlerFactory constructor.
     *
     * @param LoggerInterface $logger
     * @param string          $rootPath
     * @param string          $appPath
     */
    public function __construct(LoggerInterface $logger, $rootPath, $appPath)
    {
        $this->logger = $logger;
        $this->rootPath = $rootPath;
        $this->appPath = $appPath;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        ErrorHandler::setJoomlaLogger($this->logger);
        ErrorHandler::setJoomlaPaths(array(
            $this->appPath,
            $this->rootPath
        ));
    }
}