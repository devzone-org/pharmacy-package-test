<?php

use Devzone\Pharmacy\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

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
Route::get('master-data/products/supplier', function () {
    return view('pharmacy::master_data.supplier-products-list');
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

Route::get('master-data/customers/add', function () {
    return view('pharmacy::master_data.customer-add');
});
Route::get('master-data/customers/edit/{id}', function ($id) {
    return view('pharmacy::master_data.customer-edit', compact('id'));
});
Route::get('master-data/customers', function () {
    return view('pharmacy::master_data.customers-list');
});
Route::get('master-data/user-credit-limits', function () {
    return view('pharmacy::master_data.user-credit-limits');
});

Route::group(['middleware' => 'permission:12.customer-payments'], function () {

    Route::get('customer/payments', function () {
        return view('pharmacy::payments.customer.customer-payments');
    });
    Route::get('customer/payments/add', function () {
        return view('pharmacy::payments.customer.add');
    });
    Route::get('customer/payments/view/{id}', function ($id) {
        return view('pharmacy::payments.customer.view', compact('id'));
    });
    //Route::get('customer/payments/edit/{id}', function ($id) {
    //    return view('pharmacy::payments.customer.edit',compact('id'));
    //});

});
Route::group(['middleware' => 'permission:12.purchase-order-create'], function () {
    Route::get('purchases/edit/{id}', function ($id) {
        return view('pharmacy::purchases.purchase-edit', compact('id'));
    });
    Route::get('purchases/add', function () {
        return view('pharmacy::purchases.purchase-add');
    });
  Route::get('purchases/edit/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-edit', compact('id'));
});
});
Route::get('purchases/receive/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-receive', compact('id'));
})->middleware('permission:12.receive-purchase-order');
Route::get('purchases/purchase-order/view/pdf', [\Devzone\Pharmacy\Http\Controllers\PdfController::class , 'PoDownloadPDF']);



Route::get('purchases/view/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-view', compact('id'));
 
})->middleware('permission:12.view-purchase-order');
Route::group(['middleware' => 'permission:12.purchase-orders'], function () {
    Route::get('purchases', function () {
        return view('pharmacy::purchases.purchase-list');
    });
});
Route::get('purchases/compare/{id}', function ($id) {
    return view('pharmacy::purchases.purchase-compare', compact('id'));
})->middleware('permission:12.purchase-order-comparison');

Route::get('purchases/payments', function () {
    return view('pharmacy::payments.supplier.list');
})->middleware('permission:12.supplier-payments');

Route::get('purchases/payments/add', function () {
    return view('pharmacy::payments.supplier.add');
})->middleware('permission:12.create-supplier-payments');

Route::get('purchases/payments/edit/{id}', function ($id) {
    return view('pharmacy::payments.supplier.edit', compact('id'));
})->middleware('permission:12.create-supplier-payments');

Route::get('purchases/payments/view/{id}', function ($id) {
    return view('pharmacy::payments.supplier.view', compact('id'));
})->middleware('permission:12.supplier-payments');

Route::get('purchases/refund', function () {
    return view('pharmacy::refunds.supplier.list');
})->middleware('permission:12.supplier-returns');
Route::get('purchases/refund/add', function () {
    return view('pharmacy::refunds.supplier.add');
})->middleware('permission:12.create-supplier-returns');
Route::get('purchases/refund/edit/{id}', function ($id) {
    return view('pharmacy::refunds.supplier.edit', compact('id'));
})->middleware('permission:12.create-supplier-returns');

Route::get('purchases/refund/view/{id}', function ($id) {
    return view('pharmacy::refunds.supplier.view', compact('id'));
})->middleware('permission:12.supplier-returns');

Route::group(['middleware' => 'permission:12.stock-adjustment'], function () {
    Route::get('purchases/stock-adjustment/add', function () {
        return view('pharmacy::purchases.stock-adjustment');
    });
    Route::get('purchases/stock-adjustment', function () {
        return view('pharmacy::purchases.stock-adjustment-listing');
    });
});

Route::get('sales', function () {
    return view('pharmacy::sales.history');
})->middleware('permission:12.sale-history');

Route::get('pending-sales', function () {
    return view('pharmacy::sales.pending-sales');
});

Route::get('sales/add', function () {
    return view('pharmacy::sales.add');
})->middleware('permission:12.add-sale');

Route::get('sales/refund/{id}', function ($id) {
    return view('pharmacy::sales.refund', compact('id'));
})->middleware('permission:12.refund-sale');

Route::get('sales/view/{id}', function ($id) {
    return view('pharmacy::sales.view', compact('id'));
});
Route::get('sales/transaction/view/{id}', function ($id) {
    return view('pharmacy::sales.transaction', compact('id'));
})->middleware('permission:12.view-sale');
Route::get('sales/admissions', function () {
    return view('pharmacy::sales.admission-pharmacy');
})->middleware('permission:12.ipd-medicine-issue');

Route::get('report/sale-transaction', function () {
    return view('pharmacy::reports.sale-transaction');
})->middleware('permission:12.sale-transaction-report');

Route::get('report/sale-return-transaction', function () {
    return view('pharmacy::reports.sale-return-transaction');
})->middleware('permission:12.sale-return-transaction-report');

Route::get('report/sale-summary', function () {
    return view('pharmacy::reports.sale-summary');
})->middleware('permission:12.sale-summary-report');

Route::get('report/sale-doctorwise', function () {
    return view('pharmacy::reports.sale-doctorwise');
})->middleware('permission:12.sale-doctor-wise-report');

Route::get('report/sale-productwise', function () {
    return view('pharmacy::reports.sale-productwise');
})->middleware('permission:12.sale-product-wise-report');

Route::get('report/sale-hourly-graph', function () {
    return view('pharmacy::reports.sale-hourly-graph');
})->middleware('permission:12.sale-hourly-graph-report');


Route::get('report/purchase-summary', function () {
    return view('pharmacy::reports.purchase-summary');
})->middleware('permission:12.purchase-summary-report');

Route::get('report/purchases-details', function () {
    return view('pharmacy::reports.purchases-details');
})->middleware('permission:12.purchase-details-report');

Route::get('report/stock-register', function () {
    return view('pharmacy::reports.stock-register');
})->middleware('permission:12.stock-register-report');

Route::get('report/stock-reorder-level', function () {
    return view('pharmacy::reports.stock-reorder-level');
})->middleware('permission:12.stock-reorder-level-report');

Route::get('report/stock-near-expiry', function () {
    return view('pharmacy::reports.stock-near-expiry');
})->middleware('permission:12.stock-near-expiry-report');
Route::get('report/stock-in-out', function () {
    return view('pharmacy::reports.Stock-in-out');
})->middleware('permission:12.stock-movement-report');

Route::get('report/inter-transfer-IPD-medicines', function () {
    return view('pharmacy::reports.inter-transfer-IPD-medicines');
})->middleware('permission:12.inter-transfer-medicine-report');
Route::get('report/inventory-ledger', function () {
    return view('pharmacy::reports.inventory-ledger');
})->middleware('permission:12.inventory-ledger');

Route::get('print/sale/{id}', [PrintController::class, 'print']);


Route::get('update-retail', function () {

    $sales = \Devzone\Pharmacy\Models\Sale\SaleDetail::get();
    foreach ($sales as $s) {
        \Devzone\Pharmacy\Models\Sale\SaleDetail::where('id', $s->id)->update([
            'retail_price_after_disc' => $s->total_after_disc / $s->qty
        ]);
    }
});
Route::get('opening-stock', function () {

    \Devzone\Pharmacy\Models\InventoryLedger::truncate();
    $inventory = \Devzone\Pharmacy\Models\ProductInventory::groupBy('product_id')
        ->select('product_id', \Illuminate\Support\Facades\DB::raw('sum(qty) as qty'))
        ->get()->toArray();
    foreach ($inventory as $inv) {
        if ($inv['qty'] > 0) {
            \Devzone\Pharmacy\Models\InventoryLedger::create([
                'product_id' => $inv['product_id'],
                'description' => 'inventory updated',
                'increase' => $inv['qty'],
                'decrease' => 0,
                'type' => 'purchase'
            ]);
        }

    }
});


