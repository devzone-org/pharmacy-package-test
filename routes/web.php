<?php


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
    return view('pharmacy::master_data.products-edit',compact('id'));
});
