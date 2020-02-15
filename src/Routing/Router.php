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

    public function getCollection()
    {
        return $this->routesCollection;
    }

    public function addRoute($method, $uri, $action)
    {
        parent::addRoute($method, $uri, $action);
        $this->routesCollection->addRoute($method, $this->getFullUri($uri), $action);
    }

    protected function getFullUri($uri)
    {
        $attributes = [];

        if ($this->hasGroupStack()) {
            $attributes = $this->mergeWithLastGroup([]);
        }

        if (isset($attributes['prefix'])) {
            $uri = trim($attributes['prefix'], '/').'/'.trim($uri, '/');
        }

        return $uri;
    }
}
