<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Profiler;

use Symfony\Component\Stopwatch\Stopwatch;
use XTAIN\Bundle\JoomlaBundle\XTAINJoomlaBundle;

/**
 * Class Profiler
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Profiler
 */
class Profiler extends \JProxy_JProfiler
{
    /**
     * @var Stopwatch
     */
    protected static $stopwatch = null;

    /**
     * @param Stopwatch $stopwatch
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setStopwatch(Stopwatch $stopwatch)
    {
        self::$stopwatch = $stopwatch;
    }

    /**
     * @param string $label
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function mark($label)
    {
        $prefix = XTAINJoomlaBundle::STOPWATCH_PREFIX;
        $stopName = null;
        if (preg_match('/^(before|after)/', $label)) {
            $stopName = lcfirst(preg_replace('/^(before|after)/', '', $label));
        }
        if (self::$stopwatch !== null) {
            if ($stopName !== null) {
                if (self::$stopwatch->isStarted($prefix . $stopName)) {
                    self::$stopwatch->stop($prefix . $stopName);
                } else {
                    self::$stopwatch->start($prefix . $stopName, XTAINJoomlaBundle::STOPWATCH_CATEGORY_NAME);
                    if (preg_match('/^(after)/', $label)) {
                        self::$stopwatch->stop($prefix . $stopName, XTAINJoomlaBundle::STOPWATCH_CATEGORY_NAME);
                    }
                }
            } else {
                self::$stopwatch->start($prefix . $label, XTAINJoomlaBundle::STOPWATCH_CATEGORY_NAME);
            }
        }

        return parent::mark($label);
    }
}
