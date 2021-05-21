<?php

namespace Devzone\Pharmacy;

use Illuminate\Support\ServiceProvider;

class PharmacyServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'devzone');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'devzone');
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
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pharmacy.php', 'pharmacy');

        // Register the service the package provides.
        $this->app->singleton('pharmacy', function ($app) {
            return new Pharmacy;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['pharmacy'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/pharmacy.php' => config_path('pharmacy.php'),
        ], 'pharmacy.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/devzone'),
        ], 'pharmacy.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/devzone'),
        ], 'pharmacy.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/devzone'),
        ], 'pharmacy.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
