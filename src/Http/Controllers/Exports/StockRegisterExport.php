<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Product;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class StockRegisterExport
{
    protected $product_id;
    protected $manufacture_id;
    protected $rack_id;
    protected $category_id;
    protected $zero_stock;
    protected $cos_rp;


    public function __construct()
    {
        $request = request();
        $this->product_id = $request->product_id;
        $this->manufacture_id = $request->manufacture_id;
        $this->rack_id = $request->rack_id;
        $this->category_id = $request->category_id;
        $this->zero_stock = $request->zero_stock;
        $this->cos_rp = $request->cos_rp;

    }

    private function formatDate($date)
    {
        return \Carbon\Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');

    }

    public function download()
    {

        $products = Product::from('products as p')
            ->leftJoin('product_inventories as pi', function ($q) {
                return $q->on('pi.product_id', '=', 'p.id');
            })
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('p.id', $this->product_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('p.manufacture_id', $this->manufacture_id);
            })
            ->when(!empty($this->rack_id), function ($q) {
                return $q->where('p.rack_id', $this->rack_id);
            })
            ->when(!empty($this->category_id), function ($q) {
                return $q->where('p.category_id', $this->category_id);
            })
            ->when($this->zero_stock == 't', function ($q) {
                return $q->where('pi.qty', '>=', '0');
            })
            ->when($this->zero_stock == 'f', function ($q) {
                return $q->where('pi.qty', '>', '0');
            })
            ->groupBy('pi.product_id')
            ->groupBy('pi.supply_price')
            ->groupBy('pi.retail_price')
            ->groupBy('pi.batch_no')
            ->orderBy('p.name', 'ASC')
            ->select(
                'p.id', 'p.name as item', 'm.name as manufacturer', 'c.name as category', 'r.name as rack',
                DB::raw('sum(pi.qty) as stock_in_hand'),
                'pi.supply_price as cos', 'pi.retail_price', 'pi.batch_no'
            )
            ->get();

        $report = [];

        foreach ($products->toArray() as $key => $p) {
            if ($this->cos_rp == 't' && $p['cos'] < $p['retail_price']) {
                continue;
            }
            $data = $p;
            $data['total_stock_value'] = $p['stock_in_hand'] * $p['cos'];
            $data['total_retail_value'] = $p['stock_in_hand'] * $p['retail_price'];
            $data['gross_margin_pkr'] = $data['total_retail_value'] - $data['total_stock_value'];
            if ($data['total_retail_value'] > 0) {
                $data['gross_margin_per'] = 100 - (($data['total_stock_value'] / $data['total_retail_value']) * 100);

            } else {
                $data['gross_margin_per'] = 0;

            }
            $report[] = $data;
        }

        $data = [];
        $loop = 0;
        foreach ($report as $r) {
            $loop = $loop + 1;
            $data[] = [
                $loop,
                $r['item'],
                $r['manufacturer'],
                $r['category'],
                $r['rack'],
                number_format($r['stock_in_hand']),
                number_format($r['cos'], 2),
                number_format($r['total_stock_value'], 2),
                number_format($r['retail_price'], 2),
                number_format($r['total_retail_value'], 2),
                '-',
                number_format($r['gross_margin_pkr'], 2),
                number_format($r['gross_margin_per'], 2) . '%',
                $r['batch_no']
            ];
        }

        $data[] = [

            '',
            '',
            '',
            '',
            '',
            number_format(collect($report)->sum('stock_in_hand')),
            '',
            number_format(collect($report)->sum('total_stock_value'), 2),
            '',
            number_format(collect($report)->sum('total_retail_value'), 2),
            '',
            number_format(collect($report)->sum('gross_margin_pkr'), 2),
            '',
            '',

        ];

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['#', 'Item', 'Manufacturer', 'Category	', 'Rack', 'Stock in Hand', 'COS', 'Total Stock Value', 'Retail Price', 'Total Retail Value	', 'Sales Tax', 'Gross Margin (PKR)', 'Gross Margin %', 'Batch No']);

        $csv->insertAll($data);

        $csv->output('Stock Register ' . '.csv');


    }

}