<?php

namespace Devzone\Pharmacy\Console;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class DumpMasterData extends Command
{
    protected $signature = 'pharmacy:master-data';

    protected $description = 'Dumping master data for pharmacy';

    public function handle()
    {
        $this->info('Dumping Master Data...');
        Permission::updateOrCreate(['name' => '12.last-3-month-stats'], ['guard_name' => 'web', 'description' => 'Last 3 months stats', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.sales-summary-stats'], ['guard_name' => 'web', 'description' => 'Sale summary stats', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.sales-summary-saleman-stats'], ['guard_name' => 'web', 'description' => 'Sale summary salesman wise', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.sales-summary-dr-wise-stats'], ['guard_name' => 'web', 'description' => 'Sale summary doctor wise', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.top-supplier-payable-stats'], ['guard_name' => 'web', 'description' => 'Top supplier payable', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.top5-sell-product-income'], ['guard_name' => 'web', 'description' => 'Top 5 products revenue wise', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.top5-sell-product-profit'], ['guard_name' => 'web', 'description' => 'Top 5 products profit wise', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.expired-products'], ['guard_name' => 'web', 'description' => 'Expired products', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.hourly-sale-stats'], ['guard_name' => 'web', 'description' => 'Hourly sale trends', 'portal' => 'pharmacy', 'section' => 'dashboard']);
        Permission::updateOrCreate(['name' => '12.sale-history'], ['guard_name' => 'web', 'description' => 'Sale History', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.add-sale'], ['guard_name' => 'web', 'description' => 'Add Sale', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.view-sale'], ['guard_name' => 'web', 'description' => 'View Sale Details', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.reprint-sale'], ['guard_name' => 'web', 'description' => 'Reprint Sale Invoice', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.refund-sale'], ['guard_name' => 'web', 'description' => 'Refund Sale', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.add-credit-sale'], ['guard_name' => 'web', 'description' => 'Add Credit Sale', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.ipd-medicine-issue'], ['guard_name' => 'web', 'description' => 'Manage Inter Transfer Medicine', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.customer-payments'], ['guard_name' => 'web', 'description' => 'Customer Payments', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.approve-customer-payments'], ['guard_name' => 'web', 'description' => 'Approve Customer Payments', 'portal' => 'pharmacy', 'section' => 'sale']);
        Permission::updateOrCreate(['name' => '12.purchase-orders'], ['guard_name' => 'web', 'description' => 'Purchase Orders List', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.purchase-order-create'], ['guard_name' => 'web', 'description' => 'Create Purchase Order', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.purchase-order-approve'], ['guard_name' => 'web', 'description' => 'Approve Purchase order', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.purchase-order-approve-receive'], ['guard_name' => 'web', 'description' => 'Approve receive order if inventory changed', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.view-purchase-order'], ['guard_name' => 'web', 'description' => 'View Specific Purchase order', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.receive-purchase-order'], ['guard_name' => 'web', 'description' => 'Receive Purchase Order', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.purchase-order-comparison'], ['guard_name' => 'web', 'description' => 'Specific Purchase Order Comparison ', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.supplier-payments'], ['guard_name' => 'web', 'description' => 'View Supplier Payments', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.create-supplier-payments'], ['guard_name' => 'web', 'description' => 'Create Supplier Payments', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.approve-supplier-payments'], ['guard_name' => 'web', 'description' => 'Approve Supplier Payments', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.supplier-returns'], ['guard_name' => 'web', 'description' => 'Supplier Returns', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.create-supplier-returns'], ['guard_name' => 'web', 'description' => 'Create Supplier Returns', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.approve-supplier-returns'], ['guard_name' => 'web', 'description' => 'Approve Supplier Returns', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.stock-adjustment'], ['guard_name' => 'web', 'description' => 'Stock Adjustment', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.sale-transaction-report'], ['guard_name' => 'web', 'description' => 'Sale Transaction Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.sale-return-transaction-report'], ['guard_name' => 'web', 'description' => 'Sale Return Transaction Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.sale-summary-report'], ['guard_name' => 'web', 'description' => 'Sale Summery Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.sale-doctor-wise-report'], ['guard_name' => 'web', 'description' => 'Sale Doctor Wise Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.sale-product-wise-report'], ['guard_name' => 'web', 'description' => 'Sale Product Wise Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.sale-hourly-graph-report'], ['guard_name' => 'web', 'description' => 'Sale Hourly Graph Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.purchase-summary-report'], ['guard_name' => 'web', 'description' => 'Purchase Summary report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.purchase-details-report'], ['guard_name' => 'web', 'description' => 'Purchase Detail Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.stock-register-report'], ['guard_name' => 'web', 'description' => 'Stock Register Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.stock-movement-report'], ['guard_name' => 'web', 'description' => 'Stock Movement Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.stock-reorder-level-report'], ['guard_name' => 'web', 'description' => 'Stock Reorder Level Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.stock-near-expiry-report'], ['guard_name' => 'web', 'description' => 'Stock near expiry Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.inter-transfer-medicine-report'], ['guard_name' => 'web', 'description' => 'Inter Transfer IPD Medicine Report', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.inventory-ledger'], ['guard_name' => 'web', 'description' => 'Inventory Ledger', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.narcotics-drugs'], ['guard_name' => 'web', 'description' => 'Narcotic Drugs', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.customer-receivables'], ['guard_name' => 'web', 'description' => 'Customer Receivables', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.expiry-adjustment'], ['guard_name' => 'web', 'description' => 'Expiry Adjustment', 'portal' => 'pharmacy', 'section' => 'purchases']);
        Permission::updateOrCreate(['name' => '12.sales-manufacture-wise'], ['guard_name' => 'web', 'description' => 'Sales Manufacture Wise', 'portal' => 'pharmacy', 'section' => 'reports']);
        Permission::updateOrCreate(['name' => '12.product-details'], ['guard_name' => 'web', 'description' => 'Product Details', 'portal' => 'pharmacy', 'section' => 'reports']);


    }
}