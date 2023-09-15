<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Product;
use Illuminate\Http\Request;
use SplTempFileObject;
use League\Csv\Writer;


class ProductDetailsExport
{

    protected $product_id;

    public function __construct()
    {
        $request = request();
        $this->product_id = $request->product_id;
    }

    public function download()
    {
        $report = Product::from('products as p')
            ->join('purchase_orders as po', 'po.product_id', '=', 'p.id')
            ->join('purchases as pur', 'pur.id', '=', 'po.purchase_id')
            ->join('suppliers as s', 's.id', '=', 'pur.supplier_id')
            ->where('p.id', $this->product_id)
            ->select('p.name as product_name', 'po.*', 's.name as s_name')
            ->groupBy('po.id')
            ->get()
            ->toArray();

        $data = [];
        $loop = 0;
        foreach ($report as $r) {
            $loop = $loop + 1;
            $data[] = [
                $loop,
                $r['product_name'],
                $r['s_name'],
                $r['id'],
                number_format($r['qty']),
                number_format($r['cost_of_price'], 2),
                number_format($r['retail_price'], 2),
                number_format($r['total_cost'], 2),
                date('d M Y h:i:s', strtotime($r['created_at']))
            ];
        }

        $data[] = [

            '',
            '',
            '',
            '',
            number_format(collect($report)->sum('qty')),
            '',
            '',
            '',
            '',

        ];


        $csv = Writer::createFromFileObject(new SplTempFileObject());


        $csv->insertOne(['Sr#', 'Product', 'Supplier', 'PO #', 'Qty', 'COP (PKR)', 'Retail Price (PKR)', 'Total Cost', 'Date']);

        $csv->insertAll($data);

        $csv->output('Product Details Report ' . '.csv');

    }



}