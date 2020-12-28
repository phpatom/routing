<?php

namespace Atom\Routing;

use Atom\Routing\Contracts\AbstractRouteContract;
use Atom\Routing\Contracts\RouteContract;
use Atom\Routing\Contracts\RouteGroupContract;

class RouteGroup implements RouteGroupContract
{
    use CanRegisterRoute;

    /**
     * @var RouteContract[]
     */
    private array $routes = [];
    private $handler;
    private string $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param RouteContract $route
     * @return RouteGroupContract
     */
    public function add(RouteContract $route): RouteGroupContract
    {
        $route = clone $route;
        $route->setRouteGroup($this);
        $this->routes[] = $route;
        return $this;
    }

    /**
     * @return RouteContract[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return String
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param String $pattern
     * @return AbstractRouteContract
     */
    public function setPattern(string $pattern): AbstractRouteContract
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @param $handler
     * @return AbstractRouteContract
     */
    public function setHandler($handler): AbstractRouteContract
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    protected function registerRoute(Route $route)
    {
        $this->add($route);
    }
}
