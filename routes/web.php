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

Route::get('print/sale/{id}', [PrintController::class, 'print']);
