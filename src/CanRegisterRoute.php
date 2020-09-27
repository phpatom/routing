<?php


namespace Atom\Routing;

trait CanRegisterRoute
{
    abstract protected function registerRoute(Route $route);

    /**
     * @param array $methods
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return self
     */
    public function create(array $methods, string $pattern, $handler, ?string $name = null): self
    {
        $this->registerRoute(Route::create($methods, $pattern, $handler, $name));
        return $this;
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return self
     */
    public function get(string $pattern, $handler, ?string $name = null): self
    {
        $this->registerRoute(Route::get($pattern, $handler, $name));
        return $this;
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return self
     */
    public function post(string $pattern, $handler, ?string $name = null): self
    {
        $this->registerRoute(Route::post($pattern, $handler, $name));
        return $this;
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public function put(string $pattern, $handler, ?string $name = null): self
    {
        $this->registerRoute(Route::put($pattern, $handler, $name));
        return $this;
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return self
     */
    public function patch(string $pattern, $handler, ?string $name = null): self
    {
        $this->registerRoute(Route::patch($pattern, $handler, $name));
        return $this;
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return self
     */
    public function delete(string $pattern, $handler, ?string $name = null): self
    {
        $this->registerRoute(Route::delete($pattern, $handler, $name));
        return $this;
    }
}
