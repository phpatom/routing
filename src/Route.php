<?php
namespace Atom\Routing;

use Atom\Contracts\Routing\AbstractRouteContract;
use Atom\Contracts\Routing\RouteContract;
use Atom\Contracts\Routing\RouteGroupContract;
use Fig\Http\Message\RequestMethodInterface;
use InvalidArgumentexception;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;

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
    private $middleware;

    /**
     * Route constructor.
     * @param $methods
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     */
    public function __construct($methods, String $pattern, String $name, String $middleware)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->middleware = $middleware;
        if (is_string($methods)) {
            $methods = [$methods];
        }
        $this->setMethods($methods);
    }
    public function __clone()
    {
        return new self($this->methods, $this->pattern, $this->name, $this->middleware);
    }

    public function getRouteGroup():?RouteGroupContract
    {
        return $this->routeGroup;
    }

    public function setRouteGroup(RouteGroupContract $routeGroup):AbstractRouteContract
    {
        $this->routeGroup = $routeGroup;
        return $this;
    }

    /**
     * @param array $methods
     * @return AbstractRouteContract
     */
    public function setMethods(Array $methods):AbstractRouteContract
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

    public function setPattern(String $pattern):AbstractRouteContract
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function setName(String $name):AbstractRouteContract
    {
        $this->name = $name;
        return $this;
    }

    public function getName():String
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getMethods():array
    {
        return $this->methods;
    }

    /**
     * @return String
     */
    public function getPattern():String
    {
        return $this->pattern;
    }

    
    public function setMiddleware($middleware):AbstractRouteContract
    {
        //TODO: mix la validation du route group et du route
        if (!($middleware instanceof MiddlewareInterface) || !(is_string($middleware))) {
            $this->middleware = $middleware;
            return $this;
        }
        throw new RuntimeException("Parameter middleware of RouteGroup must be a either a string 
	    or an instance of MiddlewareInterface");
    }

    public function getMiddleware():String
    {
        return $this->middleware;
    }

    /**
     * @param array $methods
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     * @return static
     */
    public static function create(Array $methods, String $pattern, String $name, String $middleware):self
    {
        return new self($methods, $pattern, $name, $middleware);
    }

    /**
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     * @return static
     */
    public static function get(String $pattern, String $name, String $middleware):self
    {
        return new self(['GET'], $pattern, $name, $middleware);
    }

    /**
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     * @return static
     */
    public static function post(String $pattern, String $name, String $middleware):self
    {
        return new self(['POST'], $pattern, $name, $middleware);
    }

    /**
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     * @return static
     */
    public static function put(String $pattern, String $name, String $middleware):self
    {
        return new self(['PUT'], $pattern, $name, $middleware);
    }

    /**
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     * @return static
     */
    public static function patch(String $pattern, String $name, String $middleware):self
    {
        return new self(['PATCH'], $pattern, $name, $middleware);
    }

    /**
     * @param String $pattern
     * @param String $name
     * @param String $middleware
     * @return static
     */
    public static function delete(String $pattern, String $name, String $middleware):self
    {
        return new self(['DELETE'], $pattern, $name, $middleware);
    }
}
