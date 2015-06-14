<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Installation;

/**
 * Interface ExtensionInstallerInterface
 * @package XTAIN\Bundle\JoomlaBundle\Installation
 */
interface ExtensionInstallerInterface
{
    /**
     * @var string
     */
    const CLIENT_SITE = 0;

    /**
     * @var string
     */
    const CLIENT_ADMINISTRATOR = 0;

    /**
     * @param string $name
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isExtensionInstalled($name);


    /**
     * @param string $name
     * @param string $type
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function registerExtension($name, $type = 'component', $client = self::CLIENT_SITE);
}