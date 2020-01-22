<?php 

namespace Luqta\RouterSync\Routing;

use Laravel\Lumen\Routing\Router as LumenRouter;

class Router extends LumenRouter
{
    protected $routesCollection;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->routesCollection = new RoutesCollection();
    }

    public function getCollection() {
        return $this->routesCollection;
    }

    public function addRoute($method, $uri, $action)
    {
        parent::addRoute($method, $uri, $action);
        $this->routesCollection->addRoute($method, $uri, $action);
    }
}
