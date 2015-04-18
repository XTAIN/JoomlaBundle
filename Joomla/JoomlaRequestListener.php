<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * The class retrieve a request and ask joomla to build the content
 *
 * See http://symfony.com/doc/current/book/internals.html#handling-requests
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JoomlaRequestListener implements JoomlaAwareInterface
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @param JoomlaInterface $joomla
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null)
    {
        $this->joomla = $joomla;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_controller');

        $application = JoomlaInterface::SITE;

        switch ($route) {
            case 'joomla.controller:administrator':
                $application = JoomlaInterface::ADMINISTRATOR;
                break;
        }

        $this->joomla->setApplication($application);
        $this->joomla->initialize();
    }
}
