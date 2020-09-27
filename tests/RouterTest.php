<?php

namespace Atom\Routing\Test;

use Atom\Routing\Contracts\RouteContract;
use Atom\Routing\Contracts\RouteGroupContract;
use Atom\Routing\Exceptions\MethodNotAllowedException;
use Atom\Routing\Exceptions\RouteNotFoundException;
use Atom\Routing\MatchedRoute;
use Atom\Routing\Route;
use Atom\Routing\Router;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouterTest extends TestCase
{
    private function createRouter()
    {
        return new Router();
    }

    /**
     * @param String $path
     * @param String $method
     * @param array $attributes
     * @return Stub|ServerRequestInterface
     */
    private function createRequest(string $path, string $method, array $attributes = [])
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method("getPath")->willReturn($path);
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method("getAttributes")->willReturn($attributes);
        $request->method("getAttribute")->willReturnCallback(function (string $key) use ($request) {
            return $request->getAttributes()[$key];
        });
        $request->method("getUri")->willReturn($uri);
        $request->method("getMethod")->willReturn($method);
        $request->method("withAttribute")->willReturnCallback(function ($key, $value) use ($path, $method) {
            return $this->createRequest($path, $method, [$key => $value]);
        });
        return $request;
    }

    /**
     * @group routerTest
     */
    public function testItStoreRoute()
    {
        $router = $this->createRouter();
        $route = $this->createStub(RouteContract::class);
        $routeGroup = $this->createStub(RouteGroupContract::class);
        $router->add($route)->add($routeGroup)->add($routeGroup)->add($route)->add($route);
        $this->assertEquals(3, count($router->getRoutes()));
        $this->assertEquals(2, count($router->getRouteGroups()));
        $this->assertEquals($route, $router->getRoutes()[0]);
    }

    /**
     * @group routerTest
     */
    public function testItCreateRouteGroup()
    {
        $router = $this->createRouter();
        $route = $this->createStub(RouteContract::class);
        $middleware = "myMiddleware";
        $middleware2 = "myMiddleware2";
        $router->group("foo", function (RouteGroupContract $group) use ($route) {
            $group->add($route);
        }, $middleware);
        $router->group("bar", function (RouteGroupContract $group) use ($route) {
            $group->add($route);
            $group->add($route);
        }, $middleware2);

        $this->assertEquals(2, count($router->getRouteGroups()));
        $this->assertEquals([], $router->getRoutes());
        $this->assertCount(1, $router->getRouteGroups()[0]->getRoutes());
        $this->assertCount(2, $router->getRouteGroups()[1]->getRoutes());
        $this->assertEquals($route, $router->getRouteGroups()[1]->getRoutes()[0]);
        $this->assertEquals($middleware2, $router->getRouteGroups()[1]->getHandler());
        $this->assertEquals($middleware, $router->getRouteGroups()[0]->getHandler());
        $this->assertEquals("foo", $router->getRouteGroups()[0]->getPattern());
        $this->assertEquals("bar", $router->getRouteGroups()[1]->getPattern());
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     */
    public function testItThrowIfNoRouteWasRegistered()
    {
        $request = $this->createRequest("/foo", "GET");
        $router = $this->createRouter();
        $this->expectException(RouteNotFoundException::class);
        $router->dispatch($request);
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     */
    public function testItThrowIfNoMatchedRouteWasFound()
    {
        $request = $this->createRequest("/foo", "GET");
        $router = $this->createRouter();
        $router->add(Route::get("/", "baz", ""));
        $router->add(Route::get("/baz", "bar", ""));
        $this->expectException(RouteNotFoundException::class);
        $router->dispatch($request);
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItMatchRoute()
    {
        $request = $this->createRequest("/baz", "GET");
        $router = $this->createRouter();
        $router->add(Route::get("/", "baz", ""));
        $route = Route::get("/baz", "bar", "");
        $router->add($route);
        $request = $router->dispatch($request);
        $this->assertEquals(MatchedRoute::of($request)->getRoute(), $route);
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItMatchRouteWithTrailingSlash()
    {
        $request = $this->createRequest("baz/", "GET");
        $router = $this->createRouter();
        $router->add(Route::get("/", "baz", ""));
        $route = Route::get("/baz", "bar", "");
        $router->add($route);
        $request = $router->dispatch($request);
        $this->assertEquals(MatchedRoute::of($request)->getRoute(), $route);
    }


    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItMatchRouteWithValidParameters()
    {
        $request = $this->createRequest("baz/id/1-aze-aze/", "GET");
        $router = $this->createRouter();
        $router->add(Route::get("/", "baz", ""));
        $route = Route::get("/baz/id/{id:\d+}-{name:[a-z\-]+}", "bar", "");
        $router->add($route);
        $request = $router->dispatch($request);
        $matchedRoute = MatchedRoute::of($request);
        $this->assertEquals($matchedRoute->getRoute(), $route);
        $this->assertEquals("aze-aze", $matchedRoute->getParameters()["name"]);
        $this->assertEquals("1", $matchedRoute->getParameters()["id"]);
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItDoesntMatchRouteWithInvalidParameters()
    {
        $router = $this->createRouter();
        $request = $this->createRequest("baz/id/1-aze-a1e/", "GET");
        $route = Route::get("/baz/id/{id:\d+}-{name:[a-z\-]+}", "bar", "");
        $router->add($route);
        $this->expectException(RouteNotFoundException::class);
        $router->dispatch($request);
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItDoesntMatchRouteWithInvalidMethod()
    {
        $router = $this->createRouter();
        $request = $this->createRequest("baz/id/1-aze-aze/", "GET");
        $route = Route::create(["PUT", "POST"], "/baz/id/{id:\d+}-{name:[a-z\-]+}", "bar", "");
        $router->add($route);
        $this->expectException(MethodNotAllowedException::class);
        $router->dispatch($request);
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItMatchRouteInsideRouteGroup()
    {
        $router = $this->createRouter();
        $request = $this->createRequest("baz/id/1-aze-aze/", "GET");
        $route = Route::get("/id/{id:\d+}-{name:[a-z\-]+}", "bar", "");
        $route2 = Route::get("/bar", "baz", "");
        $router->group("/baz", function (RouteGroupContract $group) use ($route, $route2) {
            $group->add($route);
            $group->add($route2);
        });
        $router->group("foo/", function (RouteGroupContract $group) use ($route, $route2) {
            $group->add($route);
            $group->add($route2);
        });
        $request = $router->dispatch($request);
        $matchedRoute = MatchedRoute::of($request);
        $this->assertEquals($matchedRoute->getRoute()->getName(), $route->getName());
        $this->assertEquals("/baz", $matchedRoute->getRoute()->getRouteGroup()->getPattern());
        $request = $this->createRequest("foo/bar", "GET");
        $request = $router->dispatch($request);
        $matchedRoute = MatchedRoute::of($request);
        $this->assertEquals($matchedRoute->getRoute()->getName(), $route2->getName());
        $this->assertEquals("foo/", $matchedRoute->getRoute()->getRouteGroup()->getPattern());
    }

    /**
     * @group routerTest
     * @throws MethodNotAllowedException
     * @throws RouteNotFoundException
     */
    public function testItThrowWithNoRouteWasFoundUsingRouteGroup()
    {
        $router = $this->createRouter();
        $router->group("foo", function (RouteGroupContract $r) {
            $r->add(Route::get("bar/{id:\d+}", "bar", ""));
        });
        $request = $this->createRequest("bar/doe", "GET");
        $this->expectException(RouteNotFoundException::class);
        $router->dispatch($request);
    }

    /**
     * @group routerTest
     * @throws RouteNotFoundException
     */
    public function testItGenerateUrlUsingRouteName()
    {
        $router = $this->createRouter();
        $router->add(Route::get("/post/{id:\d+}-{slug}", "", "post.single"));
        $router->group("admin", function (RouteGroupContract $group) {
            $group->add(Route::get("/user/{id}", "", "user"));
            $group->add(Route::get("/user", "", "users"));
        });
        $this->assertEquals("/admin/user", $router->generateUrl("users"));
        $this->assertEquals("/admin/user/42", $router->generateUrl("user", ["id" => 42]));
        $this->assertEquals("/post/42-aze-aze", $router->generateUrl("post.single", ["id" => 42, "slug" => "aze-aze"]));
    }
}
