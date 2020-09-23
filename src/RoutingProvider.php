<?php


namespace Atom\Routing;

use Atom\Contracts\AppContract;
use Atom\Contracts\Routing\RouterContract;
use Atom\Contracts\ServiceProviderContract;

class RoutingProvider implements ServiceProviderContract
{
    /**
     * @var string
     */
    private $host;

    public function __construct(string $host = null)
    {
        $this->host = $host;
    }

    /**
     * @param AppContract $app
     */
    public function register(AppContract $app)
    {
        $router = new Router($this->host);
        $app->container()->bind(RouterContract::class, $router);
        $app->container()->bind(Router::class, $router);
    }
}
