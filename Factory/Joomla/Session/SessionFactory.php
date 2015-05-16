<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Session;

use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\FactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SessionFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Session
 */
class SessionFactory implements FactoryInterface, DependencyFactoryInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Session::setSession($this->session);
    }

    /**
     * @return object
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance()
    {
        return \JFactory::getSession();
    }
}