<?php

namespace Atom\Routing;

use Atom\Routing\Contracts\AbstractRouteContract;
use Atom\Routing\Contracts\RouteContract;
use Atom\Routing\Contracts\RouteGroupContract;
use Fig\Http\Message\RequestMethodInterface;
use InvalidArgumentexception;

class Route implements RouteContract
{
    private $allowedMethods = [
        RequestMethodInterface::METHOD_HEAD,
        RequestMethodInterface::METHOD_GET,
        RequestMethodInterface::METHOD_POST,
        RequestMethodInterface::METHOD_PUT,
        RequestMethodInterface::METHOD_PATCH,
        RequestMethodInterface::METHOD_DELETE,
        RequestMethodInterface::METHOD_PURGE,
        RequestMethodInterface::METHOD_OPTIONS,
        RequestMethodInterface::METHOD_TRACE,
        RequestMethodInterface::METHOD_CONNECT,
    ];

    private $routeGroup;
    private $pattern;
    private $name;
    /**
     * @var string[]
     */
    private $methods;
    private $handler;

    /**
     * Route constructor.
     * @param $methods
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     */
    public function __construct($methods, string $pattern, $handler, ?string $name = null)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        if (is_string($methods)) {
            $methods = [$methods];
        }
        $this->setMethods($methods);
    }

    public function __clone()
    {
        return new self($this->methods, $this->pattern, $this->handler, $this->name);
    }

    public function getRouteGroup(): ?RouteGroupContract
    {
        return $this->routeGroup;
    }

    public function setRouteGroup(RouteGroupContract $routeGroup): AbstractRouteContract
    {
        $this->routeGroup = $routeGroup;
        return $this;
    }

    /**
     * @param array $methods
     * @return AbstractRouteContract
     */
    public function setMethods(array $methods): AbstractRouteContract
    {
        foreach ($methods as $method) {
            if (!is_string($method)) {
                throw new InvalidArgumentexception("Argument passed to the function setMethods 
                must be an Array of String");
            }
            if (!in_array($method, $this->allowedMethods)) {
                throw new InvalidArgumentexception("The methods [$method] is not Allowed!");
            }
        }
        $this->methods = $methods;
        return $this;
    }

    public function setPattern(string $pattern): AbstractRouteContract
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function setName(string $name): AbstractRouteContract
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return String
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }


    public function setHandler($handler): AbstractRouteContract
    {
        $this->handler = $handler;
        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param array $methods
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public static function create(array $methods, string $pattern, $handler, ?string $name = null): self
    {
        return new self($methods, $pattern, $name, $handler);
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public static function get(string $pattern, $handler, ?string $name = null): self
    {
        return new self(['GET'], $pattern, $handler, $name);
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public static function post(string $pattern, $handler, ?string $name = null): self
    {
        return new self(['POST'], $pattern, $handler, $name);
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public static function put(string $pattern, $handler, ?string $name = null): self
    {
        return new self(['PUT'], $pattern, $handler, $name);
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public static function patch(string $pattern, $handler, ?string $name = null): self
    {
        return new self(['PATCH'], $pattern, $handler, $name);
    }

    /**
     * @param String $pattern
     * @param $handler
     * @param string|null $name
     * @return static
     */
    public static function delete(string $pattern, $handler, ?string $name = null): self
    {
        return new self(['DELETE'], $pattern, $handler, $name);
    }
}
