<?php

namespace Devzone\Pharmacy;

use Devzone\Pharmacy\Http\Livewire\MasterData\Category;
use Devzone\Pharmacy\Http\Livewire\MasterData\Manufacture;
use Devzone\Pharmacy\Http\Livewire\MasterData\ProductsAdd;
use Devzone\Pharmacy\Http\Livewire\MasterData\ProductsEdit;
use Devzone\Pharmacy\Http\Livewire\MasterData\ProductsList;
use Devzone\Pharmacy\Http\Livewire\MasterData\Racks;
use Devzone\Pharmacy\Http\Livewire\MasterData\SupplierAdd;
use Devzone\Pharmacy\Http\Livewire\MasterData\SupplierEdit;
use Devzone\Pharmacy\Http\Livewire\MasterData\SupplierList;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseAdd;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseList;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pharmacy');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerRoutes();

        $this->registerLivewireComponent();
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {

            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('pharmacy.prefix'),
            'middleware' => config('pharmacy.middleware'),
        ];
    }

    private function registerLivewireComponent()
    {
        Livewire::component('master-data.manufacture', Manufacture::class);
        Livewire::component('master-data.category', Category::class);
        Livewire::component('master-data.racks', Racks::class);
        Livewire::component('master-data.products-add', ProductsAdd::class);
        Livewire::component('master-data.products-edit', ProductsEdit::class);
        Livewire::component('master-data.products-list', ProductsList::class);

        Livewire::component('master-data.supplier-add', SupplierAdd::class);
        Livewire::component('master-data.supplier-edit', SupplierEdit::class);
        Livewire::component('master-data.supplier-list', SupplierList::class);


        Livewire::component('purchases.purchase-list', PurchaseList::class);
        Livewire::component('purchases.purchase-add', PurchaseAdd::class);
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
            __DIR__ . '/../config/pharmacy.php' => config_path('pharmacy.php'),
        ], 'pharmacy.config');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/pharmacy'),
        ], 'pharmacy.views');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('pharmacy'),
        ], 'pharmacy.assets');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/devzone'),
        ], 'pharmacy.views');*/

        // Registering package commands.
        // $this->commands([]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pharmacy.php', 'pharmacy');

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
}
