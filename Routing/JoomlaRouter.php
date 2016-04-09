<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use XTAIN\Bundle\JoomlaBundle\Controller\JoomlaController;
use XTAIN\Bundle\JoomlaBundle\Entity\MenuRepositoryInterface;

/**
 * Class JoomlaRouter
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Routing
 */
class JoomlaRouter implements RouterInterface, RequestMatcherInterface
{
    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var UrlPatcher
     */
    protected $urlPatcher;

    /**
     * @var RouterInterface[]
     */
    protected $router = [];

    /**
     * @var PathMatcher
     */
    protected $pathMatcher;

    /**
     * @param MenuRepositoryInterface $menuRepository
     */
    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->pathMatcher = new PathMatcher($this, $menuRepository);
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        foreach ($this->router as $router) {
            $router->setContext($context);
        }
    }

    public function setUrlPatcher(UrlPatcher $urlPatcher)
    {
        $this->urlPatcher = $urlPatcher;

        foreach ($this->router as $router) {
            if (method_exists($router, 'getGenerator')) {
                /** @var UrlGenerator $generator */
                $generator = $router->getGenerator();
                if (method_exists($generator, "setPatcher")) {
                    $generator->setPatcher($urlPatcher);
                }
            }
        }
    }

    /**
     * @param RouterInterface $router
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addRouter(RouterInterface $router)
    {
        $this->router[] = $router;
        $router->setContext($this->context);
    }

    /**
     * Gets the request context.
     *
     * @return RequestContext The context
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Tries to match a URL path with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param string  $pathinfo The path info to be parsed (raw format, i.e. not urldecoded)
     * @param Request $request  Current request object
     * @param bool    $fallback Fallback to Joomla
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function match($pathinfo, Request $request = null, $fallback = true)
    {
        if (empty($this->router)) {
            throw new \LogicException('no routers in chain');
        }

        $match = null;
        $exception = null;
        foreach ($this->router as $router) {
            try {
                if ($router instanceof RequestMatcherInterface) {
                    if (null === $request) {
                        $request = Request::create($pathinfo);
                    }

                    $match = $router->matchRequest($request);
                    $this->logger->info(
                        sprintf(
                            'Routing - Request with path "%s" matches route "%s" in router "%s".',
                            $pathinfo,
                            $match['_route'],
                            get_class($router)
                        )
                    );
                    break;
                }

                $match = $router->match($pathinfo, $request);
                $this->logger->info(
                    sprintf(
                        'Routing - Path "%s" matches route "%s" in router "%s".',
                        $pathinfo,
                        $match['_route'],
                        get_class($router)
                    )
                );
                break;
            } catch (ResourceNotFoundException $e) {
                $exception = $e;
            } catch (MethodNotAllowedException $e) {
                $exception = $e;
            }
        }

        if ($fallback && $match === null && $exception instanceof ResourceNotFoundException) {
            $match = [
                '_route'      => 'joomla_site',
                '_controller' => 'joomla.controller:site'
            ];
        }

        if ($match !== null) {
            $routeName = $match['_route'];

            $route = $this->getRouteCollection()->get($routeName);

            if ($route === null) {
                $route = new Route($pathinfo);
                $this->getRouteCollection()->add($routeName, $route);
            }

            return $match;
        }

        $info = $request ? sprintf('this request %s', $request) : sprintf('url %s', $pathinfo);

        if ($exception instanceof ResourceNotFoundException) {
            throw new ResourceNotFoundException(
                sprintf('None of the routers in the chain matched %s', $info),
                0,
                $exception
            );
        } else {
            throw new MethodNotAllowedException('All of the routers in the chain not allow this method', 0, $exception);
        }
    }

    /**
     * @param string      $name
     * @param array       $parameters
     * @param bool|string $referenceType
     * @param bool        $joomlaRoute
     *
     * @return string
     * @throws RouteNotFoundException              If the named route doesn't exist
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH, $joomlaRoute = true)
    {
        if (null === $name || $name == '') {
            throw new RouteNotFoundException('Could not generate route from empty name.');
        }

        if ($name == 'joomla') {
            return \JRoute::_('index.php?' . http_build_query($parameters), false);
        }

        $baseLink = null;
        $matchingRoutePath = null;

        if ($joomlaRoute) {
            list($baseLink, $matchingRoutePath) = $this->pathMatcher->getBasePath($name, $referenceType);
        }

        $route = null;
        foreach ($this->router as $router) {
            if ($router === null) {
                continue;
            }

            if (method_exists($router, 'getGenerator')) {
                /** @var UrlGenerator $generator */
                $generator = $router->getGenerator();
                if (method_exists($generator, "setPatcher")) {
                    $generator->setPatcher($this->urlPatcher);
                }
            }

            try {
                $route = $router->generate($name, $parameters, $referenceType);
                $this->logger->info(sprintf('Routing - Route "%s" found in router "%s".', $name, get_class($router)));
                break;
            } catch (RouteNotFoundException $e) {
            }
        }

        if (empty($route)) {
            $routeNotFoundException =
                new RouteNotFoundException(
                    sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name),
                    0,
                    $e
                );
            $this->logger->critical('Routing - ' . $routeNotFoundException->getMessage());
            throw $routeNotFoundException;
        }

        if ($baseLink !== null) {
            $route = preg_replace('#' . preg_quote($matchingRoutePath) . '#', '', $route);

            $route = rtrim($baseLink, '/') . '/' . ltrim($route, '/');
        }

        return $route;
    }

    /**
     * @return RouteCollection
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getRouteCollection()
    {
        if (!$this->routeCollection instanceof RouteCollection) {
            $this->routeCollection = new RouteCollection();
            foreach ($this->router as $router) {
                $this->routeCollection->addCollection($router->getRouteCollection());
            }
        }

        return $this->routeCollection;
    }

    /**
     * Tries to match a request with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param Request $request The request to match
     *
     * @return array An array of parameters
     * @throws ResourceNotFoundException If no matching resource could be found
     * @throws MethodNotAllowedException If a matching resource was found but the request method is not allowed
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function matchRequest(Request $request)
    {
        return $this->match($request->getPathInfo(), $request);
    }
}
