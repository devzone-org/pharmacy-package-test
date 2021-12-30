<?php

namespace Devzone\Pharmacy;

use Devzone\Pharmacy\Console\DumpMasterData;
use Devzone\Pharmacy\Http\Livewire\Dashboard\CustomisedSalesReturns;
use Devzone\Pharmacy\Http\Livewire\Dashboard\CustomisedSalesSummary;
use Devzone\Pharmacy\Http\Livewire\Dashboard\CustomisedSalesSummaryDoctorwise;
use Devzone\Pharmacy\Http\Livewire\Dashboard\CustomisedSalesSummaryUserwise;
use Devzone\Pharmacy\Http\Livewire\Dashboard\Date;
use Devzone\Pharmacy\Http\Livewire\Dashboard\ExpiredProducts;
use Devzone\Pharmacy\Http\Livewire\Dashboard\HourlyTrends;
use Devzone\Pharmacy\Http\Livewire\Dashboard\MonthwiseSalesSummary;
use Devzone\Pharmacy\Http\Livewire\Dashboard\TopSellingProductsProfitwise;
use Devzone\Pharmacy\Http\Livewire\Dashboard\TopSellingProducts;
use Devzone\Pharmacy\Http\Livewire\Dashboard\TopSuppliersPayable;
use Devzone\Pharmacy\Http\Livewire\MasterData\Category;
use Devzone\Pharmacy\Http\Livewire\MasterData\CustomerAdd;
use Devzone\Pharmacy\Http\Livewire\MasterData\CustomerEdit;
use Devzone\Pharmacy\Http\Livewire\MasterData\CustomerList;
use Devzone\Pharmacy\Http\Livewire\MasterData\Manufacture;
use Devzone\Pharmacy\Http\Livewire\MasterData\Medicine;
use Devzone\Pharmacy\Http\Livewire\MasterData\ProductsAdd;
use Devzone\Pharmacy\Http\Livewire\MasterData\ProductsEdit;
use Devzone\Pharmacy\Http\Livewire\MasterData\ProductsList;
use Devzone\Pharmacy\Http\Livewire\MasterData\Racks;
use Devzone\Pharmacy\Http\Livewire\MasterData\SupplierAdd;
use Devzone\Pharmacy\Http\Livewire\MasterData\SupplierEdit;
use Devzone\Pharmacy\Http\Livewire\MasterData\SupplierList;
use Devzone\Pharmacy\Http\Livewire\MasterData\UserCreditLimits;
use Devzone\Pharmacy\Http\Livewire\Payments\Supplier\Add;
use Devzone\Pharmacy\Http\Livewire\Payments\Supplier\Edit;
use Devzone\Pharmacy\Http\Livewire\Payments\Supplier\PaymentList;
use Devzone\Pharmacy\Http\Livewire\Payments\Supplier\View;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseAdd;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseCompare;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseEdit;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseList;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseReceive;
use Devzone\Pharmacy\Http\Livewire\Purchases\PurchaseView;
use Devzone\Pharmacy\Http\Livewire\Purchases\StockAdjustment;
use Devzone\Pharmacy\Http\Livewire\Purchases\StockAdjustmentListing;
use Devzone\Pharmacy\Http\Livewire\Reports\InterTransferIPDMedicines;
use Devzone\Pharmacy\Http\Livewire\Reports\PurchasesDetails;
use Devzone\Pharmacy\Http\Livewire\Reports\PurchaseSummary;
use Devzone\Pharmacy\Http\Livewire\Reports\SaleDoctorwise;
use Devzone\Pharmacy\Http\Livewire\Reports\SaleHourlyGraph;
use Devzone\Pharmacy\Http\Livewire\Reports\SaleProductwise;
use Devzone\Pharmacy\Http\Livewire\Reports\SaleReturnTransaction;
use Devzone\Pharmacy\Http\Livewire\Reports\SaleSummary;
use Devzone\Pharmacy\Http\Livewire\Reports\SaleTransaction;
use Devzone\Pharmacy\Http\Livewire\Reports\StockInOut;
use Devzone\Pharmacy\Http\Livewire\Reports\StockNearExpiry;
use Devzone\Pharmacy\Http\Livewire\Reports\StockRegister;
use Devzone\Pharmacy\Http\Livewire\Reports\StockReorderLevel;
use Devzone\Pharmacy\Http\Livewire\Sales\Transaction;
use Devzone\Pharmacy\Models\InventoryLedger;
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

        Livewire::component('master-data.customer-add', CustomerAdd::class);
        Livewire::component('master-data.customer-edit', CustomerEdit::class);
        Livewire::component('master-data.customer-list', CustomerList::class);

        Livewire::component('master-data.user-credit-limits', UserCreditLimits::class);
        Livewire::component('payments.customer.payment-list', \Devzone\Pharmacy\Http\Livewire\Payments\Customer\PaymentList::class);
        Livewire::component('payments.customer.add', \Devzone\Pharmacy\Http\Livewire\Payments\Customer\Add::class);
        Livewire::component('payments.customer.view', \Devzone\Pharmacy\Http\Livewire\Payments\Customer\View::class);
        Livewire::component('payments.customer.edit', \Devzone\Pharmacy\Http\Livewire\Payments\Customer\Edit::class);

        Livewire::component('purchases.purchase-list', PurchaseList::class);
        Livewire::component('purchases.purchase-add', PurchaseAdd::class);
        Livewire::component('purchases.purchase-edit', PurchaseEdit::class);
        Livewire::component('purchases.purchase-view', PurchaseView::class);
        Livewire::component('purchases.purchase-receive', PurchaseReceive::class);
        Livewire::component('purchases.purchase-compare', PurchaseCompare::class);

        Livewire::component('payments.supplier.list', PaymentList::class);
        Livewire::component('payments.supplier.add', Add::class);
        Livewire::component('payments.supplier.edit', Edit::class);
        Livewire::component('payments.supplier.view', View::class);

        Livewire::component('refunds.supplier.list', \Devzone\Pharmacy\Http\Livewire\Refunds\Supplier\RefundList::class);
        Livewire::component('refunds.supplier.add', \Devzone\Pharmacy\Http\Livewire\Refunds\Supplier\Add::class);
        Livewire::component('refunds.supplier.edit', \Devzone\Pharmacy\Http\Livewire\Refunds\Supplier\Edit::class);
        Livewire::component('refunds.supplier.view', \Devzone\Pharmacy\Http\Livewire\Refunds\Supplier\View::class);

        Livewire::component('sales.add', \Devzone\Pharmacy\Http\Livewire\Sales\Add::class);
        Livewire::component('sales.history', \Devzone\Pharmacy\Http\Livewire\Sales\History::class);
        Livewire::component('sales.pending', \Devzone\Pharmacy\Http\Livewire\Sales\Pending::class);
        Livewire::component('sales.refund', \Devzone\Pharmacy\Http\Livewire\Sales\Refund::class);
        Livewire::component('sales.view', \Devzone\Pharmacy\Http\Livewire\Sales\View::class);
        Livewire::component('sales.transaction',Transaction::class);
        Livewire::component('report.sales-transaction',SaleTransaction::class);
        Livewire::component('report.sales-return-transaction',SaleReturnTransaction::class);
        Livewire::component('report.sale-summary',SaleSummary::class);
        Livewire::component('report.sale-doctorwise',SaleDoctorwise::class);
        Livewire::component('report.sale-productwise',SaleProductwise::class);
        Livewire::component('report.sale-hourly-graph',SaleHourlyGraph::class);
        Livewire::component('report.purchase-summary',PurchaseSummary::class);
        Livewire::component('report.purchases-details',PurchasesDetails::class);
        Livewire::component('report.stock-register',StockRegister::class);
        Livewire::component('report.stock-reorder-level',StockReorderLevel::class);
        Livewire::component('report.stock-near-expiry',StockNearExpiry::class);
        Livewire::component('report.stock-in-out',StockInOut::class);
        Livewire::component('report.inter-transfer-IPD-medicines',InterTransferIPDMedicines::class);
        Livewire::component('report.inventory-ledger',\Devzone\Pharmacy\Http\Livewire\Reports\InventoryLedger::class);
        Livewire::component('master-data.medicine',Medicine::class);
        Livewire::component('sales.admission-pharmacy',\Devzone\Pharmacy\Http\Livewire\Sales\AdmissionPharmacy::class);
        Livewire::component('dashboard.date',Date::class);
        Livewire::component('dashboard.customised-sales-summary',CustomisedSalesSummary::class);
        Livewire::component('dashboard.customised-sales-returns',CustomisedSalesReturns::class);
        Livewire::component('dashboard.customised-sales-summary-userwise',CustomisedSalesSummaryUserwise::class);
        Livewire::component('dashboard.customised-sales-summary-doctorwise',CustomisedSalesSummaryDoctorwise::class);
        Livewire::component('dashboard.top-selling-products',TopSellingProducts::class);
        Livewire::component('dashboard.expired-products',ExpiredProducts::class);
        Livewire::component('dashboard.monthwise-sales-summary',MonthwiseSalesSummary::class);
        Livewire::component('dashboard.top-supplier-payables',TopSuppliersPayable::class);

        Livewire::component('purchase.stock-adjustment', StockAdjustment::class);
        Livewire::component('purchase.stock-adjustment-listing', StockAdjustmentListing::class);
        Livewire::component('dashboard.hourly-trends',HourlyTrends::class);
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
            __DIR__ . '/../resources/assets' => public_path('pharma'),
        ], 'pharmacy.assets');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/devzone'),
        ], 'pharmacy.views');*/

        // Registering package commands.
         $this->commands([
             DumpMasterData::class
         ]);
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
