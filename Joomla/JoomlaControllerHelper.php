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
     * @param JoomlaInterface $joomla
     * @param Request         $request
     */
    public function __construct(JoomlaInterface $joomla, Request $request)
    {
        $this->joomla = $joomla;
        $this->request = $request;
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
     * @param Response $response
     *
     * @return Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function wrapResponse(Response $response)
    {
        $server = $this->request->server;
        $query = $this->request->query;

        $server->set('REQUEST_URI', 'index.php');
        $query->set('option', 'com_symfony');
        $query->set('view', 'wrap');

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