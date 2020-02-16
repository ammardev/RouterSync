<?php

namespace Luqta\RouterSync\Routing;

class RoutesCollection
{
    protected $routes;

    public function addRoute($method, $uri, $action)
    {
        $route = new Route($method, $uri, $action['gateway_auth'] ?? false);
        if (isset($action['gateway_route'])) {
            $route->setGatewayRoute($action['gateway_route']);
        }
        $this->routes[] = $route;
    }

    public static function getInstanceFromLaravelCollection($collection)
    {
        $instance = new static();
        $routes = [];
        foreach ($collection as $route) {
            $routes[] = Route::getInstanceFromIlluminateRoute($route);
        }
        $instance->routes = $routes;

        return $instance;
    }

    public function toArray(): array
    {
        $collection = [];
        foreach ($this->routes as $route) {
            $collection[] = $route->toArray();
        }

        return $collection;
    }
}
