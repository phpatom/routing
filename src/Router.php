<?php

namespace Atom\Routing;

use Atom\Routing\Contracts\AbstractRouteContract;
use Atom\Routing\Contracts\RouteContract;
use Atom\Routing\Contracts\RouteGroupContract;
use Atom\Routing\Contracts\RouterContract;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Atom\Routing\Exceptions\MethodNotAllowedException;
use Atom\Routing\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use function FastRoute\simpleDispatcher;

class Router implements RouterContract
{
    /**
     * @var RouterContract | null
     */
    public static $instance;
    /**
     * @var Route[]
     */
    private $routes = [];
    /**
     * @var RouteGroupContract[]
     */
    private $routeGroups = [];
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    const MATCHED_ROUTE_ATTRIBUTE_KEY = '_route';
    /**
     * @var null
     */
    private $host;

    public function __construct($host = null)
    {
        self::$instance = $this;
        $this->host = $host;
    }

    /**
     * @param $name
     * @param array $data
     * @return mixed
     * @throws RouteNotFoundException
     */
    public static function pathFor($name, $data = [])
    {
        return self::$instance->generateUrl($name, $data);
    }

    /**
     * @param AbstractRouteContract $route
     * @return RouterContract
     */
    public function add(AbstractRouteContract $route): RouterContract
    {
        if ($route instanceof RouteContract) {
            $this->routes[] = $route;
        } else {
            $this->routeGroups[] = $route;
        }
        return $this;
    }

    /**
     * @param $prefix
     * @param callable $callable
     * @param null $handler
     * @return $this
     */
    public function group($prefix, callable $callable, $handler = null)
    {
        $routeGroup = new RouteGroup($prefix);
        $callable($routeGroup);
        if (!is_null($handler)) {
            $routeGroup->setHandler($handler);
        }
        $this->routeGroups[] = $routeGroup;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $this->initDispatcher();
        $path = RouteParser::sanitizePath($request->getUri()->getPath());
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $path);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new RouteNotFoundException('Route not found');
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = implode($routeInfo[1], ', ');
                throw new MethodNotAllowedException("Method not allowed for the current route. 
                Methods Allowed are [{$allowedMethods}]");
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $request = $request->withAttribute(
                    self::MATCHED_ROUTE_ATTRIBUTE_KEY,
                    new MatchedRoute($handler, $vars, $request->getMethod(), $request->getUri()->getPath())
                );
                if ($this->host == null) {
                    $this->host = $request->getUri()->getHost();
                }
                return $request;
        }
        throw new RouteNotFoundException('Route not found');
    }

    /**
     * @param string $name
     * @param array $data
     * @return string
     * @throws RouteNotFoundException
     */
    public function generateUrl(string $name, array $data = []): string
    {
        $this->initDispatcher();
        $route = $this->getRouteByName($name);
        if (is_null($route)) {
            throw new RouteNotFoundException("The route [$name] was not found!");
        }
        return (new RouteParser($route, $this->host))->generateUrl($data);
    }

    /**
     * @param String $name
     * @return Route
     */
    public function getRouteByName(string $name): ?RouteContract
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }
        foreach ($this->routeGroups as $group) {
            foreach ($group->getRoutes() as $route) {
                if ($route->getName() === $name) {
                    return $route;
                }
            }
        }
        return null;
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function asset($path)
    {
        if (!$this->host) {
            return $path;
        }
        return RouteParser::removeTrailingSlash($this->host) . RouteParser::sanitizePath($path);
    }

    /**
     * @return RouteGroupContract[]
     */
    public function getRouteGroups()
    {
        return $this->routeGroups;
    }

    /**
     * @param RouteCollector $r
     */
    private function dispatchRoutes(RouteCollector $r)
    {
        foreach ($this->routes as $route) {
            $r->addRoute(
                $route->getMethods(),
                $route->getPattern(),
                $route
            );
        }
    }

    /**
     * @param RouteCollector $r
     */
    private function dispatchRoutesGroups(RouteCollector $r)
    {
        foreach ($this->routeGroups as $routeGroup) {
            $r->addGroup(
                RouteParser::sanitizePath(
                    $routeGroup->getPattern()
                ),
                function (RouteCollector $r) use ($routeGroup) {
                    foreach ($routeGroup->getRoutes() as $route) {
                        $r->addRoute(
                            $route->getMethods(),
                            RouteParser::removeTrailingSlash($route->getPattern()),
                            $route
                        );
                    }
                }
            );
        }
    }

    /**
     * @return Dispatcher
     */
    private function initDispatcher()
    {
        if (!is_null($this->dispatcher)) {
            return $this->dispatcher;
        }
        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $this->dispatchRoutes($r);
            $this->dispatchRoutesGroups($r);
        });
        return $this->dispatcher;
    }

    public function getMatchedRoute(ServerRequestInterface $request): MatchedRoute
    {
        return $request->getAttribute(self::MATCHED_ROUTE_ATTRIBUTE_KEY);
    }
}
