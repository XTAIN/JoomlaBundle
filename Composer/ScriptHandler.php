<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Composer;

use Composer\Script\CommandEvent;
use XTAIN\Composer\Symfony\Util\Console;
use XTAIN\Composer\Symfony\Util\Kernel;

/**
 * Class ScriptHandler
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Composer
 */
class ScriptHandler
{
    /**
     * Composer variables are declared static so that an event could update
     * a composer.json and set new options, making them immediately available
     * to forthcoming listeners.
     */
    protected static $options = array(
        'joomla-assets-install' => 'relative',
        'joomla-admin-username' => null,
        'joomla-admin-email' => null,
        'joomla-admin-password' => null
    );

    public static function installAssets(CommandEvent $event)
    {
        $console = new Console($event);
        $options = $console->getOptions(static::$options);

        $webDir = $options['symfony-web-dir'];

        $symlink = '--mode=relative';
        if ($options['joomla-assets-install'] == 'copy') {
            $symlink = '--mode=copy';
        } elseif ($options['joomla-assets-install'] == 'symlink') {
            $symlink = '--mode=symlink';
        }

        if (!$console->hasDirectory('symfony-web-dir', $webDir)) {
            return;
        }

        $arguments = [
            $symlink,
            $webDir
        ];

        $console->execute('xtain:joomla:assets:install', $arguments);
        $kernel = new Kernel($event);
        foreach ($kernel->getBundles() as $bundleName => $path) {
            $doctrine = $path . DIRECTORY_SEPARATOR . 'Resources' .
                                DIRECTORY_SEPARATOR . 'config' .
                                DIRECTORY_SEPARATOR . 'doctrine';

            $isEasyExtends = false;
            if (is_dir($doctrine)) {
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($doctrine));
                foreach ($iterator as $file) {
                    if (preg_match('/\.skeleton$/', $file->getFilename())) {
                        $isEasyExtends = true;
                        break;
                    }
                }
            }
        }
    }

    public static function install(CommandEvent $event)
    {
        $console = new Console($event);
        $options = $console->getOptions(static::$options);

        if ($event->getIO()->isInteractive()) {
            $options['joomla-admin-username'] = $event->getIO()->ask(
                sprintf(
                    '<question>%s</question> (<comment>%s</comment>): ',
                    'Joomla admin username',
                    $options['joomla-admin-username']
                ),
                $options['joomla-admin-username']
            );
        }

        if ($event->getIO()->isInteractive()) {
            $options['joomla-admin-email'] = $event->getIO()->ask(
                sprintf(
                    '<question>%s</question> (<comment>%s</comment>): ',
                    'Joomla admin email',
                    $options['joomla-admin-email']
                ),
                $options['joomla-admin-email']
            );
        }

        if ($event->getIO()->isInteractive()) {
            $options['joomla-admin-password'] = $event->getIO()->ask(
                sprintf(
                    '<question>%s</question> (<comment>%s</comment>): ',
                    'Joomla admin password',
                    $options['joomla-admin-password']
                ),
                $options['joomla-admin-password']
            );
        }

        $arguments = [
            '--username='.$options['joomla-admin-username'],
            '--email='.$options['joomla-admin-email'],
            '--password='.$options['joomla-admin-password']
        ];

        $console->execute('xtain:joomla:install', $arguments);
    }
}