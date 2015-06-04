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
class Config extends \JProxy_Config
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @var KernelInterface
     */
    protected static $kernel;

    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setKernel(KernelInterface $kernel)
    {
        self::$kernel = $kernel;
    }

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
        \XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(__CLASS__);

        $this->secret = self::$container->getParameter('kernel.secret');
        $this->log_path = self::$kernel->getLogDir();
        $this->tmp_path = self::$container->getParameter('joomla.tmp_dir');

        if (!is_dir($this->tmp_path)) {
            mkdir($this->tmp_path, 0777, true);
        }

        $override = array_merge([
            'debug' => '0',
            'debug_lang' => '0',
            'dbtype' => null,
            'host' => null,
            'user' => null,
            'password' => null,
            'db' => null,
            'dbprefix' => 'cms_',
            'gzip' => '0',
            'error_reporting' => 'default',
            'helpurl' => 'https://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:{keyref}',
            'ftp_host' => '127.0.0.1',
            'ftp_port' => '21',
            'ftp_user' => 'admin',
            'ftp_pass' => 'admin',
            'ftp_root' => '',
            'ftp_enable' => '0',
            /*
            'mailonline' => '1',
            'mailer' => 'mail',
            'sendmail' => '/usr/sbin/sendmail',
            'smtpauth' => '0',
            'smtpuser' => '',
            'smtppass' => '',
            'smtphost' => 'localhost',
            'smtpsecure' => 'none',
            'smtpport' => '25',
            */
            'caching' => JDEBUG ? '0' : '1',
            'cache_handler' => 'file',
            'cachetime' => '15',
            'robots' => '',
            'sef' => '1',
            'sef_rewrite' => '1',
            'lifetime' => '999',
            'session_handler' => 'database',
            'force_ssl' => '0',
            'frontediting' => '0',
            'cookie_domain' => '',
            'cookie_path' => '',
            'asset_id' => '1',
            'memcache_persist' => '1',
            'memcache_compress' => '0',
            'memcache_server_host' => 'localhost',
            'memcache_server_port' => '11211',
            'memcached_persist' => '1',
            'memcached_compress' => '0',
            'memcached_server_host' => 'localhost',
            'memcached_server_port' => '11211',
            'proxy_enable' => '0',
            'proxy_host' => '',
            'proxy_port' => '',
            'proxy_user' => '',
            'proxy_pass' => '',
            'session_memcache_server_host' => 'localhost',
            'session_memcache_server_port' => '11211',
            'session_memcached_server_host' => 'localhost',
            'session_memcached_server_port' => '11211',
            'redis_persist' => '1',
            'redis_server_host' => 'localhost',
            'redis_server_port' => '6379',
            'redis_server_auth' => '',
            'redis_server_db' => '0',
            'massmailoff' => '0'
        ], self::$config);

        foreach ($override as $key => $value) {
            $this->{$key} = $value;
        }
    }
}