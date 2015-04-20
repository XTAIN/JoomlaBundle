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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Class SymfonyFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class SymfonyFactory implements DependencyFactoryInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * @param RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param HttpKernelInterface $kernel
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setKernel(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        \SymfonyViewSymfony::setKernel($this->kernel);
        \SymfonyViewSymfony::setRequestStack($this->requestStack);
        \SymfonyViewSymfony::setRouter($this->router);
    }
}