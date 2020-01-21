<?php

namespace Luqta\RouterSync;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Luqta\RouterSync\Commands\ExportRoutes;

class RouterSyncServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        
        if(config('routersync.is_gateway')) {
            $this->registerRoutes();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/routersync.php', 'routersync');

        $this->app->singleton('routersync', function ($app) {
            return new RouterSync;
        });
    }

    public function provides()
    {
        return ['routersync'];
    }
    
    protected function bootForConsole()
    {
        $this->publishes([
            __DIR__.'/../config/routersync.php' => config_path('routersync.php'),
        ], 'routersync.config');

        $this->commands([
            ExportRoutes::class
        ]);
    }

    private function registerRoutes() 
    {
        $disk = Storage::createLocalDriver(['root' => config('routersync.export_path')]);
        $filesNames = $disk->files();
        foreach($filesNames as $fileName) {
            $fileContents = json_decode($disk->get($fileName), true);
            foreach($fileContents['api'] as $route) {
                foreach($route['methods'] as $method) {
                    if($method == 'HEAD') {
                        continue;
                    }
                    $this->app->router->{$method}($fileContents['basePath'] . '/' . $route['uri'], [
                        'uses' => 'Luqta\RouterSync\Controllers\GatewayController@' . strtolower($method)
                    ]);
                }
            }
        }

    }
}
