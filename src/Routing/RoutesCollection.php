<?php 

namespace Luqta\RouterSync\Routing;


class RoutesCollection
{
    protected $routes;

    public function addRoute($method, $uri, $action)
    {
        $this->routes[] = new Route($method, $uri, $action);
    }

    public static function getInstanceFromLaravelCollection($collection)
    {
        $instance = new static();
        $routes = [];
        foreach($collection as $route) {
            $routes[] = Route::getInstanceFromIlluminateRoute($route);
        }
        $instance->routes = $routes;
        return $instance;
    }

    public function toArray(): array
    {
        $collection = [];
        foreach($this->routes as $route) {
            $collection[] = $route->toArray();
        }
        return $collection;
    }
}
