<?php
namespace Atom\Routing;

use Atom\Routing\Contracts\AbstractRouteContract;
use Atom\Routing\Contracts\RouteContract;
use Atom\Routing\Contracts\RouteGroupContract;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;

class RouteGroup implements RouteGroupContract
{
    /**
     * @var RouteContract[]
     */
    private $routes = [];
    private $handler;
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
     * @param String|MiddlewareInterface $handler
     * @return AbstractRouteContract
     */
    public function setHandler($handler):AbstractRouteContract
    {
        if (!($handler instanceof MiddlewareInterface) || !(is_string($handler))) {
            $this->handler = $handler;
            return $this;
        }
        throw new RuntimeException("Parameter middleware of RouteGroup must be a either a string 
	    or an instance of MiddlewareInterface");
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
