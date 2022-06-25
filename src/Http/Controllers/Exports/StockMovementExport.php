<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\InventoryLedger;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class StockMovementExport
{

    protected $product_id;
    protected $manufacture_id;
    protected $rack_id;
    protected $category_id;
    protected $zero_stock;
    protected $cos_rp;
    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();
        $this->product_id = $request->product_id;
        $this->manufacture_id = $request->manufacture_id;
        $this->rack_id = $request->rack_id;
        $this->category_id = $request->category_id;
        $this->zero_stock = $request->zero_stock;
        $this->cos_rp = $request->cos_rp;
        $this->from = $request->from;
        $this->to = $request->to;
    }

    private function formatDate($date)
    {
        return \Carbon\Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');

    }

    public function download()
    {

        $products=InventoryLedger::from('inventory_ledgers as il')
            ->join('products as p','p.id','=','il.product_id')
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
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('il.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('il.created_at', '>=', $this->formatDate($this->from));
            })
            ->select('p.id as product_id', 'p.name as item', 'p.type as product_type', 'm.name as manufacturer', 'c.name as category', 'r.name as rack',
                'il.increase','il.decrease','il.type')
            ->get();
        $previous=InventoryLedger::whereDate('created_at','<', $this->formatDate($this->from))
            ->groupBy('product_id')
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('product_id', $this->product_id);
            })
            ->select('product_id',DB::raw('sum(decrease) as decrease'),DB::raw('sum(increase) as increase'))
            ->get();
        foreach ($products->groupBy('product_id') as $key => $product_grouped) {
            $product=$product_grouped->first();
            $report[$key]['id'] = $product->product_id;
            $report[$key]['item'] = $product->item;
            $report[$key]['manufacturer'] = $product->manufacturer;
            $report[$key]['category'] = $product->category;
            $report[$key]['rack'] = $product->rack;
            $report[$key]['type'] = $product->product_type;
            $report[$key]['sales'] = $product_grouped->where('type','sale')->sum('decrease');
            $report[$key]['sale_return'] = $product_grouped->where('type','sale-refund')->sum('increase');
            $report[$key]['purchases'] = $product_grouped->where('type','purchase')->sum('increase')+$product_grouped->where('type','purchase-bonus')->sum('increase');
            $report[$key]['purchase_return'] = $product_grouped->where('type','purchase-refund')->sum('decrease');
            $report[$key]['adjustment'] = $product_grouped->where('type','adjustment')->sum('increase')-$product_grouped->where('type','adjustment')->sum('decrease');
            $report[$key]['opening_stock'] = $previous->where('product_id',$product->product_id)->sum('increase')-$previous->where('product_id',$product->product_id)->sum('decrease');
            $closing=($report[$key]['opening_stock']-($report[$key]['sales']+$report[$key]['purchase_return']))+$report[$key]['sale_return']+$report[$key]['purchases']+($report[$key]['adjustment']);
            $report[$key]['closing_stock']=$closing;
        }

        $data = [];
        $loop = 0;
        foreach ($report as $r){
            $loop = $loop + 1;

            if($r['type']=='s'){
               $type = 'Sound alike';
            }  elseif($r['type']=='l') {
                $type = 'Look alike';

            }else{
                   $type = '-';
                }

            $data[] = [
                $loop,
                $r['item'],
                $r['manufacturer'],
                $r['category'],
                $r['rack'],
                $type,
                $r['opening_stock'],
                $r['sales'],
                $r['sale_return'],
                $r['purchases'],
                $r['purchase_return'],
                $r['adjustment'] < 0 ? $r['adjustment'] : abs($r['adjustment']),
                $r['closing_stock']
            ];
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['#', 'Item', 'Manufacturer', 'Category	', 'Rack', 'Type', 'Opening Stock', 'Sales', 'Sales Return', 'Purchases	', 'Purchase Return', 'Adjustment', 'Closing Stock']);

        $csv->insertAll($data);

        $csv->output('Stock Movement ' . '.csv');
    }

    }