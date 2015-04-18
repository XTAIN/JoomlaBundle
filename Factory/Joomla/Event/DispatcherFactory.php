<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Event;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event\Dispatcher;

/**
 * Class DispatcherFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Event
 */
class DispatcherFactory implements DispatcherFactoryInterface, ContainerAwareInterface
{
    /**
     * @var string
     */
    protected $eventDispatcher;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        if ($this->container->has('debug.stopwatch')) {
            Dispatcher::setStopwatch($this->container->get('debug.stopwatch'));
        }

        Dispatcher::setEventDispatcher($this->eventDispatcher);
    }
}
