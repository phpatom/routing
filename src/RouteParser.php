<?php

namespace Atom\Routing;

use Atom\Routing\Contracts\RouteContract;
use FastRoute\RouteParser\Std;
use InvalidArgumentException;

class RouteParser
{

    private $route;
    private $fastRouteParser;
    /**
     * @var null
     */
    private $host;

    public function __construct(RouteContract $route, $host = null)
    {
        $this->route = $route;
        $this->fastRouteParser = new Std();
        $this->host = $host;
    }

    public function generateUrl($data = []):?String
    {
        $pattern = $this->route->getPattern();
        $segments = [];
        $segmentName = '';

        $expressions = array_reverse($this->fastRouteParser->parse($pattern));
        foreach ($expressions as $expression) {
            foreach ($expression as $segment) {
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }
                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }
                $segments[] = $data[$segment[0]];
            }
            if (!empty($segments)) {
                break;
            }
        }
        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }
        $url = implode('', $segments);
        $routeGroup = $this->route->getRouteGroup();
        if ($routeGroup) {
            $url = self::sanitizePath($routeGroup->getPattern()).
                self::sanitizePath($url);
        }
        return $this->addHost(self::sanitizePath($url));
    }
    public static function sanitizePath(string $path, bool $addSlash = true)
    {
        if ($path=="/") {
            return $path;
        }
        if ($path[0] != "/" && $addSlash) {
            $path = "/".$path;
        }
        if ($path[-1] == "/") {
            $path = substr($path, 0, -1);
        }
        if ($path == "") {
            return "/";
        }
        return $path;
    }
    public static function removeTrailingSlash(string $path)
    {
        if ($path == "") {
            return $path;
        }
        if ($path == "/") {
            return "";
        }
        if ($path[-1] == "/") {
            return substr($path, 0, -1);
        }
        return $path;
    }

    private function addHost(string $path)
    {
        if (!$this->host) {
            return $path;
        }
        return self::removeTrailingSlash($this->host).$path;
    }
}
