<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Symfony\View;

use Symfony\Component\HttpFoundation\Response;
use SymfonyViewWrap;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Class WrapFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Symfony\View
 */
class WrapFactory implements DependencyFactoryInterface
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        SymfonyViewWrap::setResponse($this->response);
    }
}