<?php


namespace Atom\Routing\Test;

use Atom\Router\Test\BaseTest;
use Atom\Routing\CanRegisterRoute;
use Atom\Routing\Route;
use PHPUnit\Framework\MockObject\MockObject;

class CanRegisterRouteTest extends BaseTest
{

    /**
     * @return MockObject | CanRegisterRoute
     */
    public function make()
    {
        return $this->getMockForTrait(CanRegisterRoute::class);
    }

    public function testGetRoute()
    {
        $mock = $this->make();
        $mock->expects($this->once())->method("registerRoute")->with(
            Route::get($path = "/foo", $handler = "handler", $name = "name")
        );
        $mock->get($path, $handler, $name);
    }

    public function testPostRoute()
    {
        $mock = $this->make();
        $mock->expects($this->once())->method("registerRoute")->with(
            Route::post($path = "/foo", $handler = "handler", $name = "name")
        );
        $mock->post($path, $handler, $name);
    }

    public function tesPutRoute()
    {
        $mock = $this->make();
        $mock->expects($this->once())->method("registerRoute")->with(
            Route::put($path = "/foo", $handler = "handler", $name = "name")
        );
        $mock->put($path, $handler, $name);
    }

    public function testPatchRoute()
    {
        $mock = $this->make();
        $mock->expects($this->once())->method("registerRoute")->with(
            Route::patch($path = "/foo", $handler = "handler", $name = "name")
        );
        $mock->patch($path, $handler, $name);
    }

    public function testCreateRoute()
    {
        $mock = $this->make();
        $mock->expects($this->once())->method("registerRoute")->with(
            Route::create($methods = ["POST", "GET", "PUT"], $path = "/foo", $handler = "handler", $name = "name")
        );
        $mock->create($methods, $path, $handler, $name);
    }

    public function testDeleteRoute()
    {
        $mock = $this->make();
        $mock->expects($this->once())->method("registerRoute")->with(
            Route::delete($path = "/foo", $handler = "handler", $name = "name")
        );
        $mock->delete($path, $handler, $name);
    }
}
