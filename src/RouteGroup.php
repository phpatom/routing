<?php
namespace Atom\Routing;

use Atom\Contracts\Routing\AbstractRouteContract;
use Atom\Contracts\Routing\RouteContract;
use Atom\Contracts\Routing\RouteGroupContract;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;

class RouteGroup implements RouteGroupContract
{
    /**
     * @var RouteContract[]
     */
    private $routes = [];
    private $middleware;
    private $pattern;

    public function __construct(String $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param RouteContract $route
     * @return RouteGroupContract
     */
    public function add(RouteContract $route):RouteGroupContract
    {
        $route = clone $route;
        $route->setRouteGroup($this);
        $this->routes[] = $route;
        return $this;
    }

    /**
     * @return RouteContract[]
     */
    public function getRoutes():array
    {
        return $this->routes;
    }

    /**
     * @return String
     */
    public function getPattern():String
    {
        return $this->pattern;
    }

    /**
     * @param String $pattern
     * @return AbstractRouteContract
     */
    public function setPattern(String $pattern):AbstractRouteContract
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @param String|MiddlewareInterface $middleware
     * @return AbstractRouteContract
     */
    public function setMiddleware($middleware):AbstractRouteContract
    {
        if (!($middleware instanceof MiddlewareInterface) || !(is_string($middleware))) {
            $this->middleware = $middleware;
            return $this;
        }
        throw new RuntimeException("Parameter middleware of RouteGroup must be a either a string 
	    or an instance of MiddlewareInterface");
    }

    /**
     * @return mixed
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}
