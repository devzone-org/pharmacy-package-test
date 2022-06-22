<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class InventoryLedgerExport
{
    protected $product_id;
    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();
        $this->product_id = $request->product_id;
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

        $report = \Devzone\Pharmacy\Models\InventoryLedger::from('inventory_ledgers as il')
            ->join('products as p', 'p.id', '=', 'il.product_id')
            ->where('il.product_id', $this->product_id)
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('il.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('il.created_at', '>=', $this->formatDate($this->from));
            })
            ->select('p.name as item', 'p.type as product_type', 'il.*')
            ->get()->toArray();
        $open_details = \Devzone\Pharmacy\Models\InventoryLedger::whereDate('created_at', '<', $this->formatDate($this->from))
            ->where('product_id', $this->product_id)
            ->groupBy('product_id')
            ->select('product_id', DB::raw('sum(increase) as increase'), DB::raw('sum(decrease) as decrease'))
            ->first();

        $opening_inv = $open_details['increase'] - $open_details['decrease'];

        $data = [];
        $loop = 0;

        $data [] = [
            '',
            '',
            '',
            '',
            'Opening Inventory',
            $opening_inv,
        ];

        $closing = $opening_inv;
        foreach ($report as $r) {
            $loop = $loop + 1;

            if($r['increase']>0){
                $closing = $closing+$r['increase'];
            }else{
                $closing = $closing-$r['decrease'];
            }

            $data[] = [
                $loop,
                date('d M Y h:i A', strtotime($r['created_at'])),
                $r['description'],
                $r['decrease'],
                $r['increase'],
                $closing
            ];

        }
        $opening= $opening_inv;
        $data []=[
            '',
            '',
            '',
            '',
            'Closing Inventory',
            $closing
        ];

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#', 'Date', 'Description', 'Decrease', 'Increase', 'Closing Inventory']);

        $csv->insertAll($data);

        $csv->output('Inventory Ledger ' . '.csv');
    }
    }