<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Sale\SaleRefundDetail;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class SaleReturnTransactionExport
{

    protected $salesman_id;
    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();
        $this->salesman_id = $request->salesman_id;
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

        $report = SaleRefundDetail::from('sale_refund_details as srd')
            ->join('sales as s', 's.id', '=', 'srd.sale_id')
            ->join('sales as rs', 'rs.id', '=', 'srd.refunded_id')
            ->leftJoin('patients as p', 'p.id', '=', 'rs.patient_id')
            ->join('products as pr', 'pr.id', '=', 'srd.product_id')
            ->join('sale_details as sd', 'sd.id', 'srd.sale_detail_id')
            ->leftJoin('users as sb', 'sb.id', '=', 's.sale_by')
            ->leftJoin('users as rb', 'rb.id', '=', 'rs.sale_by')
            ->select('srd.created_at as return_date', 's.sale_at as original_sale_date',
                'srd.refunded_id as invoice_no', 'p.name as patient_name', 'p.mr_no', 'pr.name as product_name',
                's.gross_total as original_invoice_total', 'srd.refund_qty', 'sb.name as sale_by', 'rb.name as return_by',
                DB::raw('(sd.retail_price_after_disc*srd.refund_qty) as refund_value'))
            ->when(!empty($this->salesman_id), function ($q) {
                return $q->where('rs.sale_by', $this->salesman_id);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('srd.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('srd.created_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->time_to), function ($q) {
                return $q->whereTime('s.sale_at', '<=', date('H:i:s', strtotime($this->time_to)));
            })
            ->when(!empty($this->time_from), function ($q) {
                return $q->whereTime('s.sale_at', '>=', date('H:i:s', strtotime($this->time_from)));
            })
            ->orderBy('srd.created_at', 'desc')
            ->get()->toArray();

        $data = [];
        $loop = 0;
        foreach ($report as $r) {
                $loop = $loop + 1;
                $data[] = [
                'sr_no' => $loop,
                'status' => 'Sales Return',
                'return_date' => date('d M, Y h:i A', strtotime($r['return_date'])),
                'original_sale_date' => date('d M, Y h:i A', strtotime($r['original_sale_date'])),
                'invoice_no' => $r['invoice_no'],
                'patient' => (!empty($r['patient_name']) ? $r['patient_name'] : 'Walk in') . ' ' . $r['mr_no'],
                'product_retained' => $r['product_name'],
                'invoice_total_sale_value' => 'PKR ' . number_format($r['original_invoice_total'],2),
                'qty_returned' => $r['refund_qty'],
                'qty_returned_value' => 'PKR ' . number_format($r['refund_value'],2),
                'sale_by' => $r['sale_by'],
                'return_by' => $r['return_by'],
            ];

        }

        $data[]=[

            'sr_no' => '',
            'status' => '',
            'return_date' => '',
            'original_sale_date' => '',
            'invoice_no' => '',
            'patient' => '',
            'product_retained' => '',
            'invoice_total_sale_value' => '',
            'total_qty_returned' => number_format(collect($report)->sum('refund_qty'),2) ,
            'total_qty_returned_value' => 'PKR ' . number_format(collect($report)->sum('refund_value'),2),
            'sale_by' => '',
            'return_by' => '',
        ];


        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#', 'Status', 'Return Date', 'Original Sale Date','Invoice #', 'Patient', 'Product Retained ', 'Invoice Total Sale Value', 'Qty Returned', 'Qty Returned Value', 'Sale By', 'Return By']);

        $csv->insertAll($data);

        $csv->output('Sale Return Transaction Report ' . '.csv');


    }


}