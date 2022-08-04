<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Carbon\CarbonPeriod;
use Devzone\Pharmacy\Models\Sale\OpenReturn;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class SaleSummaryExport
{

    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();

        $this->from = $request->from;
        $this->to = $request->to;
    }

    private function formatDate($date)
    {

        return \Carbon\Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');

    }

    public function download(){

        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->select(
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(s.on_account) as credit'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
                DB::raw('count(DISTINCT(s.patient_id)) as unique_customers'),
                DB::raw('count(DISTINCT(sd.product_id)) as no_of_items'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->groupBy('date')
            ->get();
//        $sale_return=SaleRefund::from('sale_refunds as sr')
//            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
//            ->join('sales as s', 's.id', '=', 'sr.sale_id')
//            ->when(!empty($this->to), function ($q) {
//                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
//            })
//            ->when(!empty($this->from), function ($q) {
//                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
//            })
//            ->select(DB::raw('DATE(s.sale_at) as date'),'sd.sale_id',DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
//                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos')
//            )
//            ->groupBy('sr.sale_detail_id')->get();
//
        $sale_return =  Sale::from('sales as s')
            ->join('sale_refund_details as sfd','sfd.refunded_id','=','s.id')
            ->join('sale_details as sd','sd.id','=','sfd.sale_detail_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->where('s.refunded_id', '>', 0)
            ->select(DB::raw('DATE(s.sale_at) as date'),DB::raw('sum(sd.retail_price_after_disc*sfd.refund_qty) as return_total'),DB::raw('sum(sd.supply_price*sfd.refund_qty) as return_cos'))
            ->groupBy('sfd.sale_detail_id')->get();


        $open_return = OpenReturn::from('open_returns as op')
            ->join('open_return_details as opd', 'opd.open_return_id', '=', 'op.id')
            ->join('product_inventories as pi', 'pi.product_id', '=', 'opd.product_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('op.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('op.created_at', '>=', $this->formatDate($this->from));
            })
            ->groupBy('op.created_at')
            ->select(DB::raw('DATE(op.created_at) as date'), DB::raw('sum(opd.total_after_deduction*opd.qty) as total_open_return'), DB::raw('sum(pi.supply_price*opd.qty) as open_return_cos'))
            ->get();



        $period = CarbonPeriod::create($this->from, $this->to);

        // Iterate over the period
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $report = [];
        foreach ($dates as $d) {

            $found = $sale->where('date', $d);
            if ($found->isNotEmpty()) {
                foreach ($sale as $s)
                    $report[] = [
                        'date' => $d,
                        'total' => $s->total,
                        'total_after_disc' => $s->total_after_disc,
                        'sale_return' => $s->sale_return,
                        'cos' => $s->cos,
                        'no_of_sale' => $s->no_of_sale,
                        'unique_customers' => $s->unique_customers,
                        'no_of_items' => $s->no_of_items,
                    ];

            } else {

                $report[] = [
                    'date' => $d,
                    'total' => 0,
                    'total_after_disc' => 0,
                    'sale_return' => 0,
                    'cos' => 0,
                    'no_of_sale' => 0,
                    'unique_customers' => 0,
                    'no_of_items' => 0,
                ];
            }

        }

        foreach ($report as $key => $rep) {
            if ($sale_return->isNotEmpty() || $open_return->isNotEmpty()) {
                $report[$key]['sale_return'] = $sale_return->where('date', $rep['date'])->sum('return_total') + $open_return->where('date', $rep['date'])->sum('total_open_return');
                $report[$key]['cos'] = $report[$key]['cos'] - $sale_return->where('date', $rep['date'])->sum('return_cos') - $open_return->where('date', $rep['date'])->sum('open_return_cos');
            } else {
                $report[$key]['sale_return'] = 0;
            }
        }

        $data = [];
        $loop = 0;
        foreach ($report as $r) {
            $loop = $loop + 1;
            $total_after_disc=$r['total_after_disc']-$r['sale_return'];
            $total_after_disc=empty($total_after_disc) ? 1 :$total_after_disc;
            $data[] = [
                'sr_no' => $loop,
                'sale_date' => date('D d M Y',strtotime($r['date'])),
                'sales' => number_format($r['total'],2),
                'discount' => '('. number_format($r['total']-$r['total_after_disc'],2) .')',
                'sales_return' => '('. number_format($r['sale_return'],2) .')',
                'net_sales' => number_format($r['total_after_disc']-$r['sale_return'],2),
                'cos' => number_format($r['cos'],2),
                'gross_profit' => number_format($r['total_after_disc']-$r['sale_return']-$r['cos'],2),
                'gross_margin' => number_format((($r['total_after_disc']-$r['sale_return']-$r['cos'])/$total_after_disc)*100,2) .'%',
                'no_of_sales' => $r['no_of_sale'],
                'unique_customers' => $r['unique_customers'],
                'avg_sales_value' => ($r['no_of_sale'] > 0) ? number_format($r['total_after_disc']/$r['no_of_sale'],2) : 0,
                'avg_items_per_sale' => ($r['no_of_items'] > 0) ? number_format($r['no_of_sale']/$r['no_of_items'],2) : 0,
            ];

        }
        $grand_total_after_disc= collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return');
        $grand_total_after_disc=empty($grand_total_after_disc) ? 1: $grand_total_after_disc;
        $gross_margin=((collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return')-collect($report)->sum('cos'))/$grand_total_after_disc)*100;

        $data[] =[
            'sr_no' => '',
            'sale_date' => '',
            'total_sales' => number_format(collect($report)->sum('total'),2),
            'total_discount' => '(' . number_format(collect($report)->sum('total')-collect($report)->sum('total_after_disc'),2) . ')',
            'total_sales_return' => '(' . number_format(collect($report)->sum('sale_return'),2) . ')',
            'total_net_sales' => number_format(collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return'),2),
            'total_cos' => number_format(collect($report)->sum('cos'),2),
            'total_gross_profit' => number_format(collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return')-collect($report)->sum('cos'),2),
            'total_gross_margin' => number_format($gross_margin,2) .'%',
            'total_no_of_sales' => number_format(collect($report)->sum('no_of_sale')),
            'total_unique_customers' =>number_format(collect($report)->sum('unique_customers')),
            'total_avg_sales_value' => number_format(collect($report)->sum('total_after_disc')/collect($report)->sum('no_of_sale'),2),
            'total_avg_items_per_sale' => number_format(collect($report)->sum('no_of_sale')/collect($report)->sum('no_of_items'),2),

        ];
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#', 'Sales Date', 'Sales (PKR)', 'Discount (PKR)','Sales Return (PKR)', 'Net Sales (PKR)', 'COS (PKR) ', 'Gross Profit (PKR)', 'Gross Margin', '# of Sales', 'Unique Customers', 'Avg Sales Value (PKR)','Avg Items per sale']);

        $csv->insertAll($data);

        $csv->output('Sale Summary Report ' . '.csv');



    }




}