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

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use XTAIN\Bundle\JoomlaBundle\Component\Symfony\View\WrapFactory;

/**
 * Class JoomlaResponseListener
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
class JoomlaResponseListener implements JoomlaAwareInterface
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @var WrapFactory
     */
    protected $factory;

    /**
     * @param JoomlaInterface $joomla
     *
     * @return void
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null)
    {
        $this->joomla = $joomla;
    }

    /**
     * @param WrapFactory $factory
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setWrapFactory(WrapFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param FilterResponseEvent $event
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        if ($this->joomla->getState() === JoomlaInterface::STATE_RESPONSE) {
            return;
        }

        $response = $event->getResponse();
        $contentType = $response->headers->get('Content-Type');

        if ($contentType === null || $contentType === 'text/html') {
            $helper = new JoomlaControllerHelper(
                $this->joomla,
                $event->getRequest()
            );

            $this->factory->setResponse($response);

            $newResponse = $helper->wrapResponse($response);
            $response->setContent($newResponse->getContent());
        }
    }
}