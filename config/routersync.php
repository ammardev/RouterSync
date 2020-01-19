<?php

return [
    /**
     * If true then it will import routes from
     * Json files.
     */
    'is_gateway' => false,

    /**
     * Keep it empty to store in the default storage path.
     */
    'export_path' => '',

    /**
     * The name of the exported file
     */
    'file_name' => 'routes.json',

    /**
     * Storage disk to be used
     */
    'disk' => 'local'
];
