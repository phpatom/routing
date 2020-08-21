<?php


namespace Atom\Routing\Middleware;

use Atom\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AtomRoutingMiddleware implements MiddlewareInterface
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handler->pipe(RouteDispatcherMiddleware::class);
        $handler->pipe(AtomRouteHandlerMiddleware::class);
        return $handler->handle($request);
    }
}
