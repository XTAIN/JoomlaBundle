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
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaControllerHelper;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class JoomlaController
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Controller
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
     * @return Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function site()
    {
        $token = $this->container->get('security.context')->getToken();

        $helper = new JoomlaControllerHelper(
            $this->joomla,
            $this->request
        );

        return $helper->getResponse();
    }

    /**
     * @return Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function administrator()
    {
        $helper = new JoomlaControllerHelper(
            $this->joomla,
            $this->request
        );

        return $helper->getResponse();
    }
}
