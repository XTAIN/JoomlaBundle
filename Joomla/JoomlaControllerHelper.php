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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use XTAIN\Bundle\JoomlaBundle\Component\Symfony\View\WrapFactory;
use XTAIN\Bundle\JoomlaBundle\Entity\Menu;

/**
 * Class JoomlaControllerHelper
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
class JoomlaControllerHelper
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var WrapFactory
     */
    protected $factory;

    /**
     * @param JoomlaInterface  $joomla
     * @param Request          $request
     * @param WrapFactory|null $factory
     */
    public function __construct(JoomlaInterface $joomla, Request $request, WrapFactory $factory = null)
    {
        $this->joomla = $joomla;
        $this->request = $request;
        $this->factory = $factory;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function buildResponse(Request $request)
    {
        $this->joomla->defineState($request);

        $this->joomla->render();

        if ($this->joomla->is404()) {
            $this->joomla->disableResponse();
        }

        if ($this->joomla->isFound()) {
            // nothing
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getResponse()
    {
        $this->buildResponse($this->request);
        $response = $this->joomla->getResponse();

        if ($this->joomla->hasResponse()) {
            return $response;
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param Response  $response
     * @param Menu|null $item
     *
     * @return Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function wrapResponse(Response $response, Menu $item = null)
    {
        $this->factory->setResponse($response);
        $server = clone $this->request->server;
        $query = clone $this->request->query;

        $query->set('option', 'com_symfony');
        $query->set('view', 'wrap');
        if ($item !== null) {
            $query->set('Itemid', $item->getId());
        }

        $uri = '/';
        $queryString = Request::normalizeQueryString(http_build_query($query->all(), null, '&'));

        if ($queryString !== '') {
            $uri .= '?' . $queryString;
        }
        $server->set('REQUEST_URI', $uri);

        $request = new Request(
            $query->all(),
            $this->request->request->all(),
            $this->request->attributes->all(),
            $this->request->cookies->all(),
            $this->request->files->all(),
            $server->all(),
            $this->request->getContent()
        );

        $this->buildResponse($request);
        $response = $this->joomla->getResponse();

        if ($this->joomla->hasResponse()) {
            return $response;
        }

        return $response;
    }
}