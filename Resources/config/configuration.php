<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class JConfig
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JConfig
{
    /**
     * @var string
     */
    public $offline = '0';

    /**
     * @var string
     */
    public $offline_message = 'Diese Website ist zurzeit im Wartungsmodus.<br />Bitte später wiederkommen.';

    /**
     * @var string
     */
    public $display_offline_message = '1';

    /**
     * @var string
     */
    public $offline_image = '';

    /**
     * @var string
     */
    public $sitename = 'XTAIN Group';

    /**
     * @var string
     */
    public $editor = 'none';

    /**
     * @var string
     */
    public $captcha = '0';

    /**
     * @var string
     */
    public $list_limit = '20';

    /**
     * @var string
     */
    public $access = '1';

    /**
     * @var string
     */
    public $debug = '0';

    /**
     * @var string
     */
    public $debug_lang = '0';

    /**
     * @var string
     */
    public $dbtype = 'mysqli';

    /**
     * @var string
     */
    public $host = 'localhost';

    /**
     * @var string
     */
    public $user = 'root';

    /**
     * @var string
     */
    public $password = 'root';

    /**
     * @var string
     */
    public $db = 'cms';

    /**
     * @var string
     */
    public $dbprefix = 'cms_';

    /**
     * @var string
     */
    public $live_site = '';

    /**
     * @var string
     */
    public $secret = 'rZPU4mYIiVVbSRGh';

    /**
     * @var string
     */
    public $gzip = '0';

    /**
     * @var string
     */
    public $error_reporting = 'default';

    /**
     * @var string
     */
    public $helpurl = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:{keyref}';

    /**
     * @var string
     */
    public $ftp_host = '127.0.0.1';

    /**
     * @var string
     */
    public $ftp_port = '21';

    /**
     * @var string
     */
    public $ftp_user = 'admin';

    /**
     * @var string
     */
    public $ftp_pass = 'admin';

    /**
     * @var string
     */
    public $ftp_root = '';

    /**
     * @var string
     */
    public $ftp_enable = '0';

    /**
     * @var string
     */
    public $offset = 'UTC';

    /**
     * @var string
     */
    public $mailonline = '1';

    /**
     * @var string
     */
    public $mailer = 'mail';

    /**
     * @var string
     */
    public $mailfrom = 'info@xtain.net';

    /**
     * @var string
     */
    public $fromname = 'XTAIN Group';

    /**
     * @var string
     */
    public $sendmail = '/usr/sbin/sendmail';

    /**
     * @var string
     */
    public $smtpauth = '0';

    /**
     * @var string
     */
    public $smtpuser = '';

    /**
     * @var string
     */
    public $smtppass = '';

    /**
     * @var string
     */
    public $smtphost = 'localhost';

    /**
     * @var string
     */
    public $smtpsecure = 'none';

    /**
     * @var string
     */
    public $smtpport = '25';

    /**
     * @var string
     */
    public $caching = '0';

    /**
     * @var string
     */
    public $cache_handler = 'file';

    /**
     * @var string
     */
    public $cachetime = '15';

    /**
     * @var string
     */
    public $MetaDesc = 'XTAIN Group: Professionelle IT-Dienstleistungen aus einer Hand! IT-Recruiting, IT-Beratungen, Hosting, Programmierung, Webseiten, Seminare uvm.';

    /**
     * @var string
     */
    public $MetaKeys = 'IT-Recruiting, IT-Systemhaus, Programmierung, Apps, Webseiten, Gestaltung, Webdesign, Software-Engeneering, Mobile-Development, Online-Marketing, Netzwerk, Server, Hardware, IT-Administrator, Office-Solutions, Job, Praktikum, Ausbildung, Mediengestaltung, Mediengestalterin, IT, Automatisierungen, Karriere, Webinar, Seminar, Joomla, Wordpress, Virtuemart, Intranet, IT-Verwaltung, IT-Administration, skalierbares Hosting, skalierbares Webhosting, IT-News, News, Neuigkeiten, IT-Experten, Microsoft Programmierung, Office Programmierung, Office365 Programmierung, Microsoft Excel Programmierung, VBA Programmierung, C++, IT-Recruiting, IT-Berater, IT-Beratung, Systemhaus, Medienhaus, IT-Haus, IT-System';

    /**
     * @var string
     */
    public $MetaTitle = '1';

    /**
     * @var string
     */
    public $MetaAuthor = '1';

    /**
     * @var string
     */
    public $MetaVersion = '0';

    /**
     * @var string
     */
    public $robots = '';

    /**
     * @var string
     */
    public $sef = '1';

    /**
     * @var string
     */
    public $sef_rewrite = '1';

    /**
     * @var string
     */
    public $sef_suffix = '0';

    /**
     * @var string
     */
    public $unicodeslugs = '0';

    /**
     * @var string
     */
    public $feed_limit = '10';

    /**
     * @var string
     */
    public $log_path = '/var/www/cms/web/logs';

    /**
     * @var string
     */
    public $tmp_path = '/var/www/cms/web/tmp';

    /**
     * @var string
     */
    public $lifetime = '999';

    /**
     * @var string
     */
    public $session_handler = 'database';

    /**
     * @var string
     */
    public $MetaRights = 'Copyright by XTAIN oHG. All rights reserved.';

    /**
     * @var string
     */
    public $sitename_pagetitles = '1';

    /**
     * @var string
     */
    public $force_ssl = '0';

    /**
     * @var string
     */
    public $frontediting = '1';

    /**
     * @var string
     */
    public $feed_email = 'author';

    /**
     * @var string
     */
    public $cookie_domain = '';

    /**
     * @var string
     */
    public $cookie_path = '';

    /**
     * @var string
     */
    public $asset_id = '1';

    /**
     * @var string
     */
    public $memcache_persist = '1';

    /**
     * @var string
     */
    public $memcache_compress = '0';

    /**
     * @var string
     */
    public $memcache_server_host = 'localhost';

    /**
     * @var string
     */
    public $memcache_server_port = '11211';

    /**
     * @var string
     */
    public $memcached_persist = '1';

    /**
     * @var string
     */
    public $memcached_compress = '0';

    /**
     * @var string
     */
    public $memcached_server_host = 'localhost';

    /**
     * @var string
     */
    public $memcached_server_port = '11211';

    /**
     * @var string
     */
    public $proxy_enable = '0';

    /**
     * @var string
     */
    public $proxy_host = '';

    /**
     * @var string
     */
    public $proxy_port = '';

    /**
     * @var string
     */
    public $proxy_user = '';

    /**
     * @var string
     */
    public $proxy_pass = '';

    /**
     * @var string
     */
    public $session_memcache_server_host = 'localhost';

    /**
     * @var string
     */
    public $session_memcache_server_port = '11211';

    /**
     * @var string
     */
    public $session_memcached_server_host = 'localhost';

    /**
     * @var string
     */
    public $session_memcached_server_port = '11211';
}
