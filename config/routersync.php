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
];
