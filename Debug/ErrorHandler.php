<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Debug;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ErrorHandler extends \Symfony\Component\Debug\ErrorHandler
{
    /**
     * @var int
     */
    private $joomlaErrors = E_ALL - E_DEPRECATED - E_USER_DEPRECATED - E_STRICT - E_NOTICE - E_WARNING;

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
     * @var string
     */
    protected static $joomlaPath;

    /**
     * @var LoggerInterface
     */
    protected static $joomlaLogger;

    /**
     * @param string $path
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setJoomlaPath($path)
    {
        self::$joomlaPath = $path;
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
        if (static::$joomlaPath === null) {
            return false;
        }

        $file = realpath($file);

        if (strpos($file, self::$joomlaPath) === 0) {
            return true;
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

                    $e['scope_vars'] = $context;
                    $e['stack'] = $backtrace ?: debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);

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