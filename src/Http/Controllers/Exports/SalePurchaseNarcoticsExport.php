<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;
use League\Csv\Writer;
use SplTempFileObject;

class SalePurchaseNarcoticsExport
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
            ->leftJoin('sales as s', 's.id', '=', 'il.sale_id')
            ->leftJoin('purchases as po', 'po.id', '=', 'il.order_id')
            ->leftJoin('purchase_receives as pr','pr.purchase_id','=','il.order_id')
            ->leftJoin('patients as pat', 'pat.id', '=', 's.patient_id')
            ->leftJoin('employees as em', 'em.id', '=', 's.referred_by')
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->where('p.control_medicine', 't')
            ->where('il.product_id', $this->product_id)
            ->whereIn('il.type', ['sale', 'purchase'])
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('il.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('il.created_at', '>=', $this->formatDate($this->from));
            })
            ->groupBy('il.id')
            ->select('il.type', 'p.name as product', 'il.created_at', 'em.name as doctor', 'em.is_doctor', 'pat.name as patient', 'il.increase', 'il.decrease', 'm.name as manufacture')
            ->orderBy('created_at', 'desc')
            ->get()->toArray();

        $data = [];
        $loop = 0;
        foreach ($report as $r){
            $loop= $loop+1;

            $data[]=[
                $loop,
                ucwords($r['type']),
                date('d M Y h:i A',strtotime($r['created_at'])),
                !empty($r['doctor']) ? $r['doctor'] : '-',
                !empty($r['patient']) ? $r['patient']: '-',
                $r['decrease'],
                $r['increase'],
                !empty($r['batch_no']) ? $r['batch_no'] : '-',
                !empty($r['manufacture']) ? $r['manufacture'] : '-'
            ];
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#','Type','Date', 'Doctor', 'Patient', 'Sale', 'Purchase','Batch #','Manufactured By']);

        $csv->insertAll($data);

        $csv->output('Sale Purchase Narcotic Drugs ' . '.csv');
    }

}