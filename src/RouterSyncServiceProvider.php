<?php

namespace Luqta\RouterSync;

use Illuminate\Support\ServiceProvider;
use Luqta\RouterSync\Commands\ExportRoutes;

class RouterSyncServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'luqta');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'luqta');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/routersync.php', 'routersync');

        // Register the service the package provides.
        $this->app->singleton('routersync', function ($app) {
            return new RouterSync;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['routersync'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/routersync.php' => config_path('routersync.php'),
        ], 'routersync.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/luqta'),
        ], 'routersync.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/luqta'),
        ], 'routersync.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/luqta'),
        ], 'routersync.views');*/

        // Registering package commands.
        $this->commands([
            ExportRoutes::class
        ]);
    }
}
