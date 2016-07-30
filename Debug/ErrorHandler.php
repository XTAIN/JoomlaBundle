<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Debug;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\BufferingLogger;

class ErrorHandler extends \Symfony\Component\Debug\ErrorHandler
{
    /**
     * @var int
     */
    private $joomlaErrors;

    /**
     * @var array
     */
    private static $joomlaLogLevel = array(
        E_DEPRECATED => LogLevel::INFO,
        E_USER_DEPRECATED => LogLevel::INFO,
        E_NOTICE => LogLevel::WARNING,
        E_USER_NOTICE => LogLevel::WARNING,
        E_STRICT => LogLevel::WARNING,
        E_WARNING => LogLevel::WARNING,
        E_USER_WARNING => LogLevel::WARNING,
        E_COMPILE_WARNING => LogLevel::WARNING,
        E_CORE_WARNING => LogLevel::WARNING,
        E_USER_ERROR => LogLevel::CRITICAL,
        E_RECOVERABLE_ERROR => LogLevel::CRITICAL,
        E_COMPILE_ERROR => LogLevel::CRITICAL,
        E_PARSE => LogLevel::CRITICAL,
        E_ERROR => LogLevel::CRITICAL,
        E_CORE_ERROR => LogLevel::CRITICAL
    );

    /**
     * @var array
     */
    protected static $joomlaPaths;

    /**
     * @var LoggerInterface
     */
    protected static $joomlaLogger;

    /**
     * ErrorHandler constructor.
     * @param BufferingLogger|null $bootstrappingLogger
     */
    public function __construct(BufferingLogger $bootstrappingLogger = null)
    {
        $this->joomlaErrors = JOMMLA_ERROR_LEVEL;

        parent::__construct($bootstrappingLogger);
    }

    /**
     * @param string $paths
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setJoomlaPaths($paths)
    {
        self::$joomlaPaths = $paths;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setJoomlaLogger(LoggerInterface $logger)
    {
        self::$joomlaLogger = $logger;
    }

    /**
     * @param string $file
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function isPartOfJoomla($file)
    {
        foreach (static::$joomlaPaths as $joomlaPath) {
            $file = realpath($file);

            if (strpos($file, $joomlaPath) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handles errors by filtering then logging them according to the configured bit fields.
     *
     * @param int    $type    One of the E_* constants
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @return bool Returns false when no handling happens so that the PHP engine can handle the error itself.
     *
     * @throws \ErrorException When $this->thrownErrors requests so
     *
     * @internal
     */
    public function handleError($type, $message, $file, $line, array $context, array $backtrace = null)
    {
        if (self::isPartOfJoomla($file) && !($this->joomlaErrors & $type)) {
            if (self::$joomlaLogger !== null) {
                try {
                    $level = ($this->joomlaErrors & $type) ? self::$joomlaLogLevel[$type] : LogLevel::DEBUG;

                    $e = array(
                        'type' => $type,
                        'level' => ($this->joomlaErrors & $type),
                        'scream' => true
                    );

                    $e['stack'] = $backtrace ?: debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

                    foreach ($e['stack'] as $key => $entry) {
                        unset($e['stack'][$key]['args']);
                    }

                    self::$joomlaLogger->log($level, $message, $e);
                } catch (\Exception $e) {
                    throw $e;
                }
            }

            return true;
        }

        return parent::handleError($type, $message, $file, $line, $context, $backtrace);
    }
}