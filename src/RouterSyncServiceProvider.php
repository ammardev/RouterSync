<?php

namespace Luqta\RouterSync;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Luqta\RouterSync\Commands\ExportRoutes;
use Luqta\RouterSync\Routing\Router;

class RouterSyncServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app instanceof \Laravel\Lumen\Application) {
            $this->app->configure('routersync');
        }
        $this->mergeConfigFrom(__DIR__.'/../config/routersync.php', 'routersync');
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        if (config('routersync.is_gateway')) {
            $this->registerRoutes();
        }
    }

    public function register()
    {
        $this->app->singleton('routersync', function ($app) {
            return new RouterSync;
        });

        if ($this->app instanceof \Laravel\Lumen\Application) {
            $this->app->router = new Router($this->app);
        }
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
            ExportRoutes::class,
        ]);
    }

    private function registerRoutes()
    {
        $this->app->router->group([
            'prefix' => config('routersync.routes_prefix')
        ], function () {
            $disk = Storage::createLocalDriver(['root' => config('routersync.export_path')]);
            $filesNames = $disk->files();
            foreach ($filesNames as $fileName) {
                $fileContents = json_decode($disk->get($fileName), true);
                foreach ($fileContents['api'] as $route) {
                    foreach ($route['methods'] as $method) {
                        if ($method == 'HEAD') {
                            continue;
                        }
                        $routeDefinition = [
                            'uses' => config('routersync.controller_name') . '@requestMicroservice',
                            'original_uri' => $fileContents['basePath'].'/'.trim($route['original_uri'], '/'),
                        ];

                        if ($route['private']) {
                            $routeDefinition['middleware'] = 'auth';
                        }

                        $this->app->router->{$method}($fileContents['basePath'].'/'.trim($route['uri'], '/'), $routeDefinition);
                    }
                }
            }
        });
    }
}
