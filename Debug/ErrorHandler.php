<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Debug;

class ErrorHandler extends \Symfony\Component\Debug\ErrorHandler
{
    /**
     * @var int
     */
    private $joomlaErrors = E_ALL - E_DEPRECATED - E_USER_DEPRECATED - E_STRICT - E_NOTICE - E_WARNING;

    /**
     * @var string
     */
    protected static $joomlaPath;

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
            return true;
        }

        return parent::handleError($type, $message, $file, $line, $context, $backtrace);
    }
}