<?php

namespace Atom\Routing\Middleware;

use Atom\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AtomRouteHandlerMiddleware implements MiddlewareInterface
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matchedRoute = $this->router->getMatchedRoute($request);
        $route = $matchedRoute->getRoute();
        $routeGroup = $route->getRouteGroup();
        if ($routeGroup && $routeGroup->getMiddleware()) {
            $handler->load($routeGroup->getMiddleware());
        }
        $handler->load($route->getMiddleware());
        return $handler->handle($request);
    }
}
