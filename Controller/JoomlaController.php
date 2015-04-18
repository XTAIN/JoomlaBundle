<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XTAIN\Bundle\JoomlaBundle\Joomla\Joomla;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaAwareInterface;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JoomlaController extends Controller implements JoomlaAwareInterface
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
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null)
    {
        $this->joomla = $joomla;
    }

    /**
     * @param Request $request
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function buildResponse()
    {
        $this->joomla->defineState($this->request);

        $this->joomla->render();

        if ($this->joomla->is404()) {
            $this->joomla->disableResponse();
        }

        if ($this->joomla->isFound()) {
            // nothing
        }
    }

    public function site()
    {
        $token = $this->container->get('security.context')->getToken();

        $this->buildResponse();
        $response = $this->joomla->getResponse();

        if ($this->joomla->hasResponse()) {
            return $response;
            /*
            $newResponse = new Response($response->getContent());
            $response->setContent("");
            $newResponse->setStatusCode($response->getStatusCode());
            $newResponse->headers->replace($response->headers->allPreserveCase());
            return $newResponse;
                $this->render("XTAINJoomlaBundle:Joomla:index.html.twig", array(
                'content' => $content
            ), $response);
            */
        }

        throw new NotFoundHttpException();
    }

    public function administrator()
    {
        $this->buildResponse();
        $response = $this->joomla->getResponse();

        if ($this->joomla->hasResponse()) {
            return $response;
        }

        throw new NotFoundHttpException();
    }
}
