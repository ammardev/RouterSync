<?php

namespace Luqta\RouterSync\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Routes as a JSON file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(config('routersync.is_gateway')) {
            $this->error('This project is an API gateway. You can\'t export its routes.');
            return;
        }

        $routesCollection = app('router')->getRoutes();
        $serviceName = strtolower(config('app.name'));
        $jsonOutput = [
            'basePath' => $serviceName,
            'api' => []
        ];

        foreach($routesCollection as $route) {
            $jsonOutput['api'][] = $route->uri;
        }

        $disk = Storage::createLocalDriver(['root' => config('routersync.export_path')]);
        $disk->put(config('routersync.file_name'), json_encode($jsonOutput));
        return;
    }
}
