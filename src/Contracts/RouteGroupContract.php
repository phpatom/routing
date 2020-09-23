<?php

namespace Atom\Routing\Contracts;

interface RouteGroupContract extends AbstractRouteContract
{
    public function add(RouteContract $route):?RouteGroupContract;
    /**
     * @return RouteContract[]
     */
    public function getRoutes():array;
}