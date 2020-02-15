<?php

namespace Luqta\RouterSync\Routing;

use Illuminate\Routing\Route as IlluminateRoute;

class Route
{
    protected $methods;
    protected $uri;
    protected $gateway_route;
    protected $is_private;
    

    public function __construct($methods, string $uri, bool $is_private = false)
    {
        $this->methods = is_string($methods)? [$methods]:$methods;
        $this->uri = $uri;
        $this->gateway_route = $uri;
        $this->is_private = $is_private;
    }

    public static function getInstanceFromIlluminateRoute(IlluminateRoute $illuminateRoute): Route
    {
        $action = $illuminateRoute->getAction();
        $instance = new static(
            $illuminateRoute->methods,
            $illuminateRoute->uri,
            $action['gateway_auth'] ?? false
        );
        if (isset($action['gateway_route'])) {
            $instance->setGatewayRoute($action['gateway_route']);
        }
        return $instance;
    }

    public function setGatewayRoute(string $uri)
    {
        $this->gateway_route = $uri;
    }

    public function toArray(): array
    {
        return [
            'methods' => $this->methods,
            'original_uri' => $this->uri,
            'uri' => $this->gateway_route,
            'private' => $this->is_private
        ];
    }
}
