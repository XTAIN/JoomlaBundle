<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use XTAIN\Bundle\JoomlaBundle\XTAINJoomlaBundle;

/**
 * Class Dispatcher
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event
 */
class Dispatcher extends \JProxy_JEventDispatcher
{
    const PREFIX_BEFORE = 'joomla.before.';

    const PREFIX_AFTER = 'joomla.after.';

    /**
     * @var EventDispatcherInterface
     */
    protected static $eventDispatcher;

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
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        self::$eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $event
     * @param mixed  $args
     *
     * @return array
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function trigger($event, $args = null)
    {
        if ($args === null) {
            $args = [];
        }

        settype($args, 'array');

        $prefix = XTAINJoomlaBundle::STOPWATCH_PREFIX;
        $eventUnderscore = Inflector::tableize($event);

        $beforeEvent = new BeforeEvent($args);
        self::$eventDispatcher->dispatch(self::PREFIX_BEFORE . $eventUnderscore, $beforeEvent);

        if (self::$stopwatch !== null) {
            self::$stopwatch->start($prefix . '.event.' . $eventUnderscore);
        }

        $return = parent::trigger($event, $beforeEvent->getArguments());

        if (self::$stopwatch !== null) {
            self::$stopwatch->stop($prefix . '.event.' . $eventUnderscore);
        }

        $afterEvent = new AfterEvent($args, $return);
        self::$eventDispatcher->dispatch(self::PREFIX_AFTER . $eventUnderscore, $afterEvent);

        return $afterEvent->getResult();
    }
}
