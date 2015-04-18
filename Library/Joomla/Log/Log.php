<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Log;

use JProxy_JLog;

/**
 * Class Log
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Log
 */
class Log extends \JProxy_JLog
{
    /**
     * @var bool
     */
    protected static $frozen = false;

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function freeze()
    {
        self::$frozen = true;
    }

    public static function addLogger(array $options, $priorities = self::ALL, $categories = [], $exclude = false)
    {
        if (self::$frozen) {
            return;
        }
        parent::addLogger($options, $priorities, $categories, $exclude);
    }
}
