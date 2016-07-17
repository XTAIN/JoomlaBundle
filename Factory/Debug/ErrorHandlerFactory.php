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
     * @var string
     */
    protected $overridePath;

    /**
     * ErrorHandlerFactory constructor.
     *
     * @param LoggerInterface $logger
     * @param string          $rootPath
     * @param string          $overridePath
     * @param string          $appPath
     */
    public function __construct(LoggerInterface $logger, $rootPath, $overridePath, $appPath)
    {
        $this->logger = $logger;
        $this->rootPath = $rootPath;
        $this->overridePath = $overridePath;
        $this->appPath = $appPath;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        ErrorHandler::setJoomlaLogger($this->logger);
        ErrorHandler::setJoomlaPaths(array(
            $this->overridePath,
            $this->rootPath,
            $this->appPath
        ));
    }
}