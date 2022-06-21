<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Purchase;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class PurchaseSummaryExport
{

    protected $supplier_id;
    protected $manufacture_id;
    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();
        $this->supplier_id = $request->supplier_id;
        $this->manufacture_id = $request->manufacture_id;
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
        $report = Purchase::from('purchases as p')
            ->leftJoin('purchase_receives as po', 'po.purchase_id', '=', 'p.id')
            ->leftJoin('products as pro', 'pro.id', '=', 'po.product_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->leftJoin('manufactures as m', 'm.id', '=', 'pro.manufacture_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->leftJoin('users as us', 'us.id', '=', 'p.approved_by')
            ->when(!empty($this->supplier_id), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('m.id', $this->manufacture_id);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at', '>=', $this->formatDate($this->from));
            })
            ->groupBy('po.purchase_id')
            ->orderBy('p.id', 'ASC')
            ->select(
                's.name as supplier_name',
                'p.created_at as placement_date',
                'u.name as created_by',
                'us.name as approved_by',
                'p.id as po_no', 'p.delivery_date as receiving_date', 'p.grn_no', 'p.supplier_invoice', 'p.is_paid',
                'p.advance_tax', 'm.name as manufacture_name',
                DB::raw('sum(po.total_cost) as po_value'),
                DB::raw('sum(po.qty*po.cost_of_price) as cos'),
            )
            ->get()
            ->toArray();


        $data = [];
        $loop = 0;
        $total_tax=0 ;
        foreach ($report as $r) {
            $loop = $loop + 1;
            $tax = 0;
            if (!empty($r['advance_tax'])) {
                $tax = $r['po_value'] / 100 * $r['advance_tax'];
            }
            $total_tax = $total_tax + $tax;

            $data[] = [
                $loop,
                $r['po_no'],
                !empty($r['placement_date']) ? date('d M Y', strtotime($r['placement_date'])) : '-',
                !empty($r['receiving_date']) ? date('d M Y', strtotime($r['receiving_date'])) : '-',
                $r['supplier_name'],
                $r['created_by'],
                $r['approved_by'],
                $r['grn_no'],
                $r['supplier_invoice'],
                $r['is_paid'] == 't' ? 'Paid' : 'UnPaid',
                number_format($r['cos'], 2),
                number_format($r['cos'] - $r['po_value'], 2),
                number_format($r['po_value'],2),
                number_format($tax, 2),
                number_format($r['po_value'] + $tax, 2)
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
            '',
            '',
            number_format(collect($report)->sum('cos'), 2),
            number_format(collect($report)->sum('cos') - collect($report)->sum('po_value'), 2),
            number_format(collect($report)->sum('po_value'), 2),
            number_format($total_tax, 2),
            number_format($total_tax + collect($report)->sum('po_value'), 2),

        ];

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#', 'PO #', 'Order Placement Date', 'Order Receiving Date', 'Supplier Name', 'PO Created By', 'PO Approved By', 'GRN #', 'Supplier Invoice #', 'Invoice Payment Status', 'Total COS', 'Discount','After Discount','Tax','Grand Total']);

        $csv->insertAll($data);

        $csv->output('Purchase Summary Report ' . '.csv');

    }
}