<?php

namespace Atom\Routing\Test;

use Atom\Routing\Route;
use Atom\Routing\RouteGroup;
use Atom\Routing\RouteParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RouteParserTest extends TestCase
{
    /**
     * @group routerTest
     */
    public function testItGenerateUrlForSimpleRoute()
    {
        $route = Route::get("bar/", "", "bar");
        $parser = new RouteParser($route);
        $this->assertEquals("/bar", $parser->generateUrl());
    }

    /**
     * @group routerTest
     */
    public function testItGenerateUrlWithParameters()
    {
        $route = Route::get("bar/{id:\d+}-{slug:[a-z\-]+}", "", "bar");
        $parser = new RouteParser($route);
        $this->assertEquals("/bar/47-aze-aze", $parser->generateUrl(["id" => "47", "slug" => "aze-aze"]));
    }

    /**
     * @group routerTest
     */
    public function testItGenerateUrlWithRouteGroup()
    {
        $route = Route::get("bar/{id:\d+}-{slug:[a-z\-]+}", "bar", "");
        $routeGroup = new RouteGroup("/foo/");
        $route->setRouteGroup($routeGroup);
        $parser = new RouteParser($route);
        $this->assertEquals("/foo/bar/47-aze-aze", $parser->generateUrl(["id" => "47", "slug" => "aze-aze"]));
    }

    /**
     * @group routerTest
     */
    public function testItThrowIfThereIsMissingParametersWhenGeneratingRoute()
    {
        $route = Route::get("bar/{id:\d+}-{slug:[a-z\-]+}", "bar", "");
        $routeGroup = new RouteGroup("/foo/");
        $route->setRouteGroup($routeGroup);
        $parser = new RouteParser($route);
        $this->expectException(InvalidArgumentException::class);
        $parser->generateUrl();
    }
}
