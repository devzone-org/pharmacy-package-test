<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Carbon\Carbon;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use League\Csv\Writer;
use SplTempFileObject;


class StockNearExpiryExport
{
    protected $product_id;
    protected $manufacture_id;
    protected $supplier_id;
    protected $rack_id;
    protected $category_id;
    protected $type;
    protected $expiry_date;


    public function __construct()
    {
        $request = request();
        $this->product_id = $request->product_id;
        $this->manufacture_id = $request->manufacture_id;
        $this->rack_id = $request->rack_id;
        $this->category_id = $request->category_id;
        $this->supplier_id = $request->supplier_id;
        $this->type = $request->type;
        $this->expiry_date = $request->expiry_date;

    }

    private function formatDate($date)
    {
        return \Carbon\Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');

    }

    public function download()
    {
        $products = Product::from('products as p')
            ->join('product_inventories as pi', function ($q) {
                return $q->on('pi.product_id', '=', 'p.id')
                    ->where('pi.qty', '>', '0')
                    ->where('pi.expiry', '<=', $this->formatDate($this->expiry_date));
            })
            ->join('purchases as pur', 'pur.id', '=', 'pi.po_id')
            ->join('suppliers as s', 's.id', '=', 'pur.supplier_id')
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->when(!empty($this->expiry_date), function ($q){
                return $q->where('pi.expiry', '<=' , $this->formatDate($this->expiry_date));
            })
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
            ->when(!empty($this->supplier_id), function ($q) {
                return $q->where('pur.supplier_id', $this->supplier_id);
            })
            ->when(!empty($this->type), function ($q) {
                return $q->where('p.type', $this->type);
            })
            ->orderBy('p.id', 'ASC')
            ->select(
                'p.id', 'pi.id as pi_id', 'p.name as item', 'p.type', 'm.name as manufacturer', 'c.name as category', 'pi.qty', 'pi.po_id', 'pi.expiry', 'r.name as rack',
                's.name as supplier_name',
            )
            ->get();
        $report = [];
        foreach ($products as $key => $product) {
            $report[$key]['id'] = $product->id;
            $report[$key]['item'] = $product->item;
            $report[$key]['type'] = $product->type;
            $report[$key]['manufacturer'] = $product->manufacturer;
            $report[$key]['category'] = $product->category;
            $report[$key]['supplier'] = $product->supplier_name;
            $report[$key]['rack'] = $product->rack;
            $report[$key]['expiry'] = $product->expiry;
            $report[$key]['po_id'] = $product->po_id;
            $report[$key]['stock_in_hand'] = $product->qty;
            $last_sold = SaleDetail::from('sale_details as sd')
                ->join('sales as s', 's.id', '=', 'sd.sale_id')
                ->where('sd.product_inventory_id', $product->pi_id)
                ->select('s.sale_at')
                ->orderBy('sd.id', 'DESC')
                ->first();
            $report[$key]['last_sold'] = null;
            if (!empty($last_sold)) {
                $report[$key]['last_sold'] = $last_sold->sale_at;
            }

            $report[$key]['expired'] = false;
            if ($product->expiry <= date('Y-m-d')) {
                $report[$key]['expired'] = true;
            } elseif ($product->expiry > date('Y-m-d')) {
                $from = Carbon::parse(date('Y-m-d'));
                $to = Carbon::parse($product->expiry);
                $diff = $from->diff($to);
                $report[$key]['expiring_in'] = $diff->format("%m months, %d days");
            }
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
                $r['po_id'],
                !empty($r['manufacturer']) ? $r['manufacturer'] : '-',
                !empty($r['category']) ? $r['category'] : '-',
                !empty($r['rack']) ? $r['rack'] : '-',
                $r['supplier'],
                $type ?? '-',
                $r['stock_in_hand'],
                !empty($r['expiry']) ? date('d M Y', strtotime($r['expiry'])) : 'Not Defined',
                $r['expired'] ? 'Already Expired' : $r['expiring_in'],
                !empty($r['last_sold']) ? date('d M Y', strtotime($r['last_sold'])) : '-'
            ];
        }

        $data[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            number_format(collect($report)->sum('stock_in_hand')),
            '',
            '',
            '',
        ];
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['#', 'Item', 'PO #', 'Manufacturer', 'Category', 'Rack', 'Supplier', 'Type', 'Stock in Quantity', 'Expiry Date', 'Expiring In', 'Last Sold']);

        $csv->insertAll($data);

        $csv->output('Stock Near Expiry ' . '.csv');

    }
}