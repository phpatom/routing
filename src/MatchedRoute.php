<?php
namespace Atom\Routing;

use Atom\Contracts\Routing\RouteContract;

class MatchedRoute
{
    private $route;
    private $arguments = [];

    public function __construct(RouteContract $route, array $arguments)
    {
        $this->route = $route;
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return RouteContract
     */
    public function getRoute(): RouteContract
    {
        return $this->route;
    }
}
