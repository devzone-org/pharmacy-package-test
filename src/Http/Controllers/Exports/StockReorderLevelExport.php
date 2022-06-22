<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Product;
use SplTempFileObject;
use League\Csv\Writer;

class StockReorderLevelExport
{

    public function download()
    {
        $products = Product::from('products as p')
            ->join('product_inventories as pi', function ($q) {
                return $q->on('pi.product_id', '=', 'p.id');
            })
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->select(
                'p.id', 'p.name as item', 'p.type', 'p.reorder_level', 'p.reorder_qty', 'm.name as manufacturer',
            )
            ->groupBy('pi.product_id')
            ->orderBy('p.id', 'ASC')
            ->get();
        $report = [];
        foreach ($products as $key => $product) {
            $report[$key]['id'] = $product->id;
            $report[$key]['item'] = $product->item;
            $report[$key]['type'] = $product->type;
            $report[$key]['manufacturer'] = $product->manufacturer;
            $report[$key]['stock_in_hand'] = $product->inventories->sum('qty');
            $report[$key]['reorder_level'] = $product->reorder_level;
            $report[$key]['reorder_qty'] = $product->reorder_qty;
        }
        $data = [];
        $loop = 0;
        foreach ($report as $r) {
            $loop = $loop + 1;
            $type = '';
            if ($r['type'] == 's') {
                $type = 'Sound alike';
            } elseif ($r['type'] == 'l') {
                $type = 'Look alike';
            }

            $data[] = [
                $loop,
                $r['item'],
                !empty($r['manufacturer']) ? $r['manufacturer'] : '-',
                $type ?? '-',
                $r['stock_in_hand'],
                !empty($r['reorder_level']) ? $r['reorder_level'] : '-',
                !empty($r['reorder_qty']) ? $r['reorder_qty'] : '-'
            ];
        }
        $data[] = [
            '',
            '',
            '',
            '',
            number_format(collect($report)->sum('stock_in_hand')),
            '',
            '',


        ];

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['#', 'Item', 'Manufacturer',  'Type', 'Stock in Hand', 'Reorder Level', 'Reorder Qty']);

        $csv->insertAll($data);

        $csv->output('Stock Reorder Level ' . '.csv');
    }
}