<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class SalesProductWiseExport
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
        $report = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('sd.product_id', $this->product_id);
            })
            ->select(
                'p.name as product_name',
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
                DB::raw('sum(sr.refund_qty) as refund_qty'),
                DB::raw('sum(sd.qty) as qty'),
                DB::raw('sum(sd.qty) - sum(coalesce(sr.refund_qty,0)) as total_sale_qty'),
                DB::raw('sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) as unit'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) as total_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) as total_after_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) as total_profit'),
            )
            ->groupBy('sd.product_id')
            ->orderBy('qty', 'DESC')
            ->get()
            ->toArray();

        $data = [];
        $loop = 0;
        foreach ($report as $r) {
            $loop = $loop + 1;
            $total_after_dis = $r['total_after_refund'] == 0 ? 1 : $r['total_after_refund'];
            $data = [
                $loop,
                $r['product_name'],
                number_format($r['qty']),
                number_format($r['refund_qty']),
                number_format($r['total_sale_qty']),
                number_format($r['total'], 2),
                number_format($r['total'] - $r['total_after_disc'], 2),
                number_format($r['total_refund'], 2),
                number_format($r['total_after_refund'], 2),
                number_format($r['cos'], 2),
                number_format($r['total_after_refund'] - $r['cos'], 2),
                number_format((($r['total_after_refund'] - $r['cos']) / $total_after_dis) * 100, 2) . ' %',
            ];
        }

        $gross_margin = ((collect($report)->sum('total_after_refund') - collect($report)->sum('cos')) / collect($report)->sum('total_after_refund')) * 100;

        $data[] = [

            '',
            '',
            number_format(collect($report)->sum('qty')),
            number_format(collect($report)->sum('refund_qty')),
            number_format(collect($report)->sum('total_sale_qty')),
            number_format(collect($report)->sum('total'), 2),
            number_format(collect($report)->sum('total') - collect($report)->sum('total_after_disc'), 2),
            number_format(collect($report)->sum('total_refund'), 2),
            number_format(collect($report)->sum('total_after_refund'), 2),
            number_format(collect($report)->sum('cos'), 2),
            number_format(collect($report)->sum('total_after_refund') - collect($report)->sum('cos'), 2),
            number_format($gross_margin, 2) . ' %',

        ];


        $csv = Writer::createFromFileObject(new SplTempFileObject());


        $csv->insertOne(['Sr#', 'Products', 'Qty Sold (a)', 'Qty Returned (b)', 'Net Qty (a-b)', 'Sale (PKR)', 'Discount (PKR)', 'Sale Return (PKR)', 'Net Sale (PKR)(A)', 'COS (PKR)(B)', 'Gross Profit (PKR)(A-B)', 'Gross Margin (%)(A-B)/A']);

        $csv->insertAll($data);

        $csv->output('Sale Product Wise Report ' . '.csv');

    }

}