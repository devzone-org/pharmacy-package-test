<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use SplTempFileObject;

class SaleDoctorWiseExport
{
    protected $doctor;
    protected $department;
    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();
        $this->doctor = $request->doctor_id;
        $this->department = $request->department;
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
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('departments as d', 'd.id', '=', 'e.department_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->doctor), function ($q) {
                return $q->where('s.referred_by', $this->doctor);
            })
            ->when(!empty($this->department), function ($q) {
                return $q->where('e.department_id', $this->department);
            })
            ->select(
                'e.name as doctor_name',
                'd.name as department_name',
                DB::raw('sum(sd.total) as total'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
                DB::raw('sum(sr.refund_qty) as refund_qty'),
                DB::raw('sum(sd.qty) as total_sale_qty'),
                DB::raw('sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) as unit'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) as total_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) as total_after_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) as total_profit'),
                's.referred_by', 'e.name as doctor',
            )
            ->groupBy('s.referred_by')
            ->get()
            ->toArray();

        $data = [];
        $loop = 0;
        foreach ($report as $r) {
            if (empty(round($r['total_after_refund'], 2))) {
                $gross_margin = 0;
            }else {
                $gross_margin = number_format((($r['total_after_refund'] - $r['cos']) / round($r['total_after_refund'], 2)) * 100, 2);
            }
            $loop= $loop + 1;
            $data[] = [
                'sr_no' => $loop,
                'doctor' => !empty($r['doctor_name']) ? $r['doctor_name'] : 'Walk in',
                'department' => !empty($r['department_name']) ? $r['department_name'] : '-' ,
                'sales' => number_format($r['total'], 2),
                'discount' => '(' . number_format($r['total'] - $r['total_after_disc'], 2) . ')',
                'sales_return' => number_format($r['total_refund'], 2),
                'net_sales' => number_format($r['total_after_refund'], 2) ,
                'cos' => number_format($r['cos'], 2),
                'gross_profit' => number_format($r['total_after_refund'] - $r['cos'], 2),
                'gross_margin' => $gross_margin . ' %',
                'no_of_sales' => $r['no_of_sale'],
            ];
        }

        $gross_margin=((collect($report)->sum('total_after_refund')-collect($report)->sum('cos'))/collect($report)->sum('total_after_refund'))*100;

        $data[] = [

            'sr_no' => '',
            'doctor' => '',
            'department' => '' ,
            'total_sales' => number_format(collect($report)->sum('total'),2),
            'total_discount' => number_format(collect($report)->sum('total')-collect($report)->sum('total_after_disc'),2),
            'total_sales_return' => number_format(collect($report)->sum('total_refund'),2),
            'total_net_sales' => number_format(collect($report)->sum('total_after_refund'),2) ,
            'total_cos' => number_format(collect($report)->sum('cos'),2),
            'total_gross_profit' => number_format(collect($report)->sum('total_after_refund')-collect($report)->sum('cos'),2),
            'total_gross_margin' => $gross_margin . ' %',
            'total_no_of_sales' => number_format(collect($report)->sum('no_of_sale')),
        ];

        $csv = Writer::createFromFileObject(new SplTempFileObject());


        $csv->insertOne(['Sr#','Doctor','Department','Sales (PKR)','Discount (PKR)','Sales Return (PKR)','Net Sales (PKR)(A)', 'COS (PKR)(B)', 'Gross Profit (PKR)(A-B)', 'Gross Margin (%)(A-B)/A', '# of Sales']);

        $csv->insertAll($data);

        $csv->output('Sale Doctor Wise Report ' . '.csv');


    }


}