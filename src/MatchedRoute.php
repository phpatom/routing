<?php

namespace Atom\Routing;

use Atom\Routing\Contracts\RouteContract;
use Psr\Http\Message\ServerRequestInterface;

class MatchedRoute
{
    /**
     * @var RouteContract
     */
    private $route;
    /**
     * @var array
     */
    private $parameters = [];
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $path;

    public function __construct(RouteContract $route, array $parameters, string $method, string $path)
    {
        $this->route = $route;
        $this->parameters = $parameters;
        $this->method = $method;
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function get(string $key)
    {
        return $this->getParameters()[$key];
    }

    /**
     * @return RouteContract
     */
    public function getRoute(): RouteContract
    {
        return $this->route;
    }

    public static function of(ServerRequestInterface $request): ?MatchedRoute
    {
        return $request->getAttribute(Router::MATCHED_ROUTE_ATTRIBUTE_KEY);
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    public function isNamed(string $name): bool
    {
        return $this->getRoute()->getName() === $name;
    }

    public function isOfMethod(string $method): bool
    {
        return $this->getMethod() === $method;
    }

    /**
     * @param string $method
     * @param string $path
     * @return bool
     */
    public function is(string $method, string $path): bool
    {
        return $this->getPath() === $path && $this->isOfMethod($method);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
