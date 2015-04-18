<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla;

/**
 * Class Factory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla
 */
class Factory extends \JProxy_JFactory
{
    /**
     * @param string $file
     * @param string $type
     * @param string $namespace
     *
     * @return \Joomla\Registry\Registry
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function getConfig($file = null, $type = 'PHP', $namespace = '')
    {
        $registry = parent::getConfig($file, $type, $namespace);
        if ($file === null) {
            $registry->set('debug', JDEBUG);
            $registry->set('dbtype', 'doctrine');
        }

        return $registry;
    }

    public static function getUser($id = null)
    {
        // force load of JUser so that the instanceof in parent method works correctly
        class_exists('JUser');

        return parent::getUser($id);
    }
}
