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
        $routesCollection = app('router')->getRoutes();
        $jsonOutput = [
            'basePath' => strtolower(config('app.name')),
            'api' => []
        ];

        foreach($routesCollection as $route) {
            $jsonOutput['api'][] = $route->uri;
        }
        
        Storage::disk('local')->put('routes.json', json_encode($jsonOutput));
        return;
    }
}
