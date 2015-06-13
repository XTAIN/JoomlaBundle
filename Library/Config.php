<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class Config
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library
 */
class Config
{
    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @param array $config
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setConfiguration(array $config = [])
    {
        self::$config = $config;
    }

    /**
     *
     */
    public function __construct()
    {
        if (empty(self::$config)) {
            \XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(__CLASS__);
        }

        foreach (self::$config as $key => $value) {
            $this->{$key} = $value;
        }

        if (!is_dir($this->tmp_path)) {
            mkdir($this->tmp_path, 0777, true);
        }
    }

    /**
     * @param string     $property
     * @param mixed|null $default
     *
     * @return mixed
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function get($property, $default = null)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        return $default;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function __call($name, array $arguments) {
        $action = substr($name, 0, 3);
        $property = substr($name, 3);

        if ($action == 'get') {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $property, $matches);
            $propertyUnderscore = $matches[0];
            foreach ($propertyUnderscore as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
            }
            $propertyUnderscore = implode('_', $propertyUnderscore);
            if (property_exists($this, $propertyUnderscore)) {
                return $this->{$propertyUnderscore};
            }

            $propertyLower = strtolower($property);
            if (property_exists($this, $propertyLower)) {
                return $this->{$propertyLower};
            }
        }

        $class = get_class($this);
        $trace = debug_backtrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        trigger_error("Call to undefined method $class::$name() in $file on line $line", E_USER_ERROR);

        return null;
    }
}

\class_alias(Config::class, 'JConfig');