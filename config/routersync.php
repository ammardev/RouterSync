<?php

return [
    /**
     * If true then it will import routes from
     * Json files.
     */
    'is_gateway' => false,

    /**
     * The path of the export command result file.
     */
    'export_path' => base_path().'/../services_routes/',

    /**
     * The name of the exported file.
     */
    'file_name' => strtolower(config('app.name')),

    /**
     * The controller namespace. If you want to extend the controller and use
     * your own controller you can change this namespace.
     */
    'controller_name' => 'Luqta\RouterSync\Controllers\GatewayController',

    /**
     * You can set a custom global route prefix for all routes
     */
    'routes_prefix' => ''
];
