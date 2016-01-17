<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Session;

use ArrayIterator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Session
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Session
 */
class Session extends \JProxy_JSession
{
    /**
     * @var SessionInterface
     */
    protected static $session;

    /**
     * @param SessionInterface $session
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setSession(SessionInterface $session)
    {
        self::$session = $session;
    }

    public function __construct($store = 'none', array $options = array())
    {
        $this->_state = 'inactive';
    }

    /**
     * Returns a clone of the internal data pointer
     *
     * @return  \Joomla\Registry\Registry
     */
    public function getData()
    {
        if ($this->data === null) {
            return new \Joomla\Registry\Registry();
        }

        return clone $this->data;
    }

    /**
     * Get session name
     *
     * @return  string  The session name
     *
     * @since   11.1
     */
    public function getName()
    {
        if ($this->_state === 'destroyed')
        {
            return null;
        }

        return self::$session->getName();
    }

    /**
     * Get session id
     *
     * @return  string  The session name
     *
     * @since   11.1
     */
    public function getId()
    {
        if ($this->_state === 'destroyed')
        {
            return null;
        }

        return self::$session->getId();
    }

    /**
     * Get the session handlers
     *
     * @return  array  An array of available session handlers
     *
     * @since   11.1
     */
    public static function getStores()
    {
        return [];
    }

    /**
     * Retrieve an external iterator.
     *
     * @return  ArrayIterator  Return an ArrayIterator of $_SESSION.
     *
     * @since   12.2
     */
    public function getIterator()
    {
        return new ArrayIterator(self::$session->get('joomla', []));
    }


    /**
     * Set the session timers
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    protected function _setTimers()
    {
        if (!$this->has('session.timer.start')) {
            $start = time();

            $this->set('session.timer.start', $start);
            $this->set('session.timer.last', $start);
            $this->set('session.timer.now', $start);
        }

        $this->set('session.timer.now', time());
        $this->set('session.timer.last', $this->get('session.timer.now'));

        return true;
    }

    public function get($name, $default = null, $namespace = 'default')
    {
        $namespace = '__' . $namespace;
        $joomla = self::$session->get('joomla', []);

        if (isset($joomla[$namespace][$name])) {
            return $joomla[$namespace][$name];
        }

        return $default;
    }

    public function set($name, $value = null, $namespace = 'default')
    {
        $namespace = '__' . $namespace;
        $joomla = self::$session->get('joomla', []);

        if (!isset($joomla[$namespace])) {
            $joomla[$namespace] = [];
        }

        $old = null;
        if (isset($joomla[$namespace][$name])) {
            $old = $joomla[$namespace][$name];
        }

        $joomla[$namespace][$name] = $value;
        self::$session->set('joomla', $joomla);

        return $old;
    }

    public function has($name, $namespace = 'default')
    {
        $namespace = '__' . $namespace;
        $joomla = self::$session->get('joomla', []);

        return isset($joomla[$namespace][$name]);
    }

    public function clear($name, $namespace = 'default')
    {
        $namespace = '__' . $namespace;
        $joomla = self::$session->get('joomla', []);

        $value = null;

        if (isset($joomla[$namespace][$name])) {
            $value = $joomla[$namespace][$name];
            unset($joomla[$namespace][$name]);
        }

        self::$session->set('joomla', $joomla);
        return $value;
    }

    public function _start()
    {
        return self::$session->start();
    }

    public function destroy()
    {
        self::$session->invalidate();
        $this->_state = 'destroyed';
    }

    public function restart()
    {
        self::$session->invalidate();

        $this->_validate();
        $this->_setCounter();
    }

    public function fork()
    {
        return self::$session->migrate();
    }

    public function close()
    {
        return;
    }

    public static function checkToken($method = 'post')
    {
        return true;
    }
}