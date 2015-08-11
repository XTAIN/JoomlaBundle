<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Application;

/**
 * Class Web
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Application
 */
class Web extends \JProxy_JApplicationWeb
{
    /**
     * @var \JConfig
     */
    protected static $defaultConfig;

    /**
     * @param \JConfig $config
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setConfig(\JConfig $config)
    {
        self::$defaultConfig = $config;
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function detectRequestUri()
    {
        if (php_sapi_name() == "cli") {
            return 'http://localhost';
        }
        return parent::detectRequestUri();
    }

    /**
     * @param string $file
     * @param string $class
     *
     * @return \JConfig
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function fetchConfigurationData($file = '', $class = 'JConfig')
    {
        return self::$defaultConfig;
    }
}
