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
use XTAIN\Bundle\JoomlaBundle\Routing\PathMatcherInterface;

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
     * @var PathMatcherInterface
     */
    protected $pathMatcher;

    /**
     * @var string[]
     */
    protected $pattern = [];

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
     * @param string $pattern
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addWrapPattern($pattern)
    {
        $this->pattern[] = $pattern;
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
     * @param PathMatcherInterface $pathMatcher
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setPathMatcher(PathMatcherInterface $pathMatcher)
    {
        $this->pathMatcher = $pathMatcher;
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

        $menu = $this->pathMatcher->findMenuPointForRequest($event->getRequest());

        if ($menu === null) {
            $match = false;

            foreach ($this->pattern as $pattern) {
                if (preg_match('#' . $pattern . '#i', $event->getRequest()->getPathInfo())) {
                    $match = true;
                    break;
                }
            }

            if (!$match) {
                return;
            }
        }

        $response = $event->getResponse();
        $contentType = $response->headers->get('Content-Type');

        if ($contentType === null || $contentType === 'text/html') {
            $helper = new JoomlaControllerHelper(
                $this->joomla,
                $event->getRequest(),
                $this->factory
            );

            $newResponse = $helper->wrapResponse($response, $menu);
            $response->setContent($newResponse->getContent());
        }
    }
}