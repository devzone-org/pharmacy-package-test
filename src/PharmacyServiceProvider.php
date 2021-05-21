<?php

namespace Devzone\Pharmacy;

use Illuminate\Support\Facades\Route;
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
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'pharmacy');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pharmacy');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->registerRoutes();

        $this->registerLivewireComponent();
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
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/pharmacy'),
        ], 'pharmacy.views');

        // Publishing assets.
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('pharmacy'),
        ], 'pharmacy.assets');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/devzone'),
        ], 'pharmacy.views');*/

        // Registering package commands.
        // $this->commands([]);
    }


    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('ams.prefix'),
            'middleware' => config('ams.middleware'),
        ];
    }


    private function registerLivewireComponent(){
        //Livewire::component('chart-of-accounts.listing',Listing::class);
    }
}
