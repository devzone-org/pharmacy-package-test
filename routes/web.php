<?php


use Devzone\Pharmacy\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('pharmacy::dashboard');
});
Route::get('master-data', function () {
    return view('pharmacy::master_data.home');
});
Route::get('master-data/manufactures', function () {
    return view('pharmacy::master_data.manufactures');
});
Route::get('master-data/categories', function () {
    return view('pharmacy::master_data.categories');
});
Route::get('master-data/racks', function () {
    return view('pharmacy::master_data.racks');
});
Route::get('master-data/products', function () {
    return view('pharmacy::master_data.products-list');
});
Route::get('master-data/products/add', function () {
    return view('pharmacy::master_data.products-add');
});
Route::get('master-data/products/edit/{id}', function ($id) {
    return view('pharmacy::master_data.products-edit', compact('id'));
});
Route::get('master-data/suppliers', function () {
    return view('pharmacy::master_data.supplier-list');
});
Route::get('master-data/suppliers/add', function () {
    return view('pharmacy::master_data.supplier-add');
});
Route::get('master-data/suppliers/edit/{id}', function ($id) {
    return view('pharmacy::master_data.supplier-edit', compact('id'));
});

Route::get('purchases', function () {
    return view('pharmacy::purchases.purchase-list');
});
Route::get('purchases/add', function () {
    return view('pharmacy::purchases.purchase-add');
});
Route::get('purchases/view/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-view', compact('id'));
});
Route::get('purchases/edit/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-edit', compact('id'));
});

Route::get('purchases/receive/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-receive', compact('id'));
});

Route::get('purchases/compare/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-compare', compact('id'));
});

Route::get('purchases/payments', function () {
    return view('pharmacy::payments.supplier.list');
});

Route::get('purchases/payments/add', function () {
    return view('pharmacy::payments.supplier.add');
});

Route::get('purchases/payments/edit/{id}', function ($id) {
    return view('pharmacy::payments.supplier.edit', compact('id'));
});

Route::get('purchases/payments/view/{id}', function ($id) {
    return view('pharmacy::payments.supplier.view', compact('id'));
});

Route::get('purchases/refund', function () {
    return view('pharmacy::refunds.supplier.list');
});

Route::get('purchases/refund/add', function () {
    return view('pharmacy::refunds.supplier.add');
});

Route::get('purchases/refund/edit/{id}', function ($id) {
    return view('pharmacy::refunds.supplier.edit', compact('id'));
});

Route::get('purchases/refund/view/{id}', function ($id) {
    return view('pharmacy::refunds.supplier.view', compact('id'));
});

Route::get('purchases/stock-adjustment/add', function () {
    return view('pharmacy::purchases.stock-adjustment');
});

Route::get('purchases/stock-adjustment', function () {
    return view('pharmacy::purchases.stock-adjustment-listing');
});
Route::get('sales', function () {
    return view('pharmacy::sales.history');
});
Route::get('sales/add', function () {
    return view('pharmacy::sales.add');
});
Route::get('sales/refund/{id}', function ($id) {
    return view('pharmacy::sales.refund', compact('id'));
});
Route::get('sales/view/{id}', function ($id) {
    return view('pharmacy::sales.view', compact('id'));
});
Route::get('sales/admissions', function () {
    return view('pharmacy::sales.admission-pharmacy');
});

Route::get('report/sale-transaction', function () {
    return view('pharmacy::reports.sale-transaction');
});

Route::get('report/sale-return-transaction', function () {
    return view('pharmacy::reports.sale-return-transaction');
});

Route::get('report/sale-summary', function () {
    return view('pharmacy::reports.sale-summary');
});
Route::get('report/sale-doctorwise', function () {
    return view('pharmacy::reports.sale-doctorwise');
});
Route::get('report/sale-productwise', function () {
    return view('pharmacy::reports.sale-productwise');
});
Route::get('report/sale-hourly-graph', function () {
    return view('pharmacy::reports.sale-hourly-graph');
});
Route::get('report/purchase-summary', function () {
    return view('pharmacy::reports.purchase-summary');
});
Route::get('report/purchases-details', function () {
    return view('pharmacy::reports.purchases-details');
});
Route::get('report/stock-register', function () {
    return view('pharmacy::reports.stock-register');
});
Route::get('report/stock-reorder-level', function () {
    return view('pharmacy::reports.stock-reorder-level');
});
Route::get('report/stock-near-expiry', function () {
    return view('pharmacy::reports.stock-near-expiry');
});
Route::get('report/stock-in-out', function () {
    return view('pharmacy::reports.Stock-in-out');
});
Route::get('report/inter-transfer-IPD-medicines', function () {
    return view('pharmacy::reports.inter-transfer-IPD-medicines');
});

Route::get('print/sale/{id}', [PrintController::class, 'print']);
