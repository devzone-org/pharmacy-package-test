<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use SplTempFileObject;


class InterTransferIpdExport
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

    public function download()
    {

        $report = Sale::from('sales as s')
            ->join('admissions as a','a.id','=','s.admission_id')
            ->join('admission_job_details as ajd',function ($q){
                return $q->on('ajd.admission_id','=','a.id')
                    ->on('ajd.procedure_id','=','s.procedure_id');
            })
            ->join('employees as emp','emp.id','=','ajd.doctor_id')
            ->join('patients as p','p.id','=','a.patient_id')
            ->join('procedures as pro','pro.id','=','s.procedure_id')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sf','sf.sale_detail_id','=','sd.id')
            ->join('users as u','u.id','=','s.sale_by')

            ->whereNotNull('s.admission_id')
            ->whereNotNull('s.procedure_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->select(
                's.sale_at','a.admission_no','p.name as patient_name','emp.name as doctor_name','pro.name as procedure_name','u.name as issued_by',
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('sum(sf.refund_qty*sd.retail_price) as refunded_retail'),
                DB::raw('sum(sf.refund_qty*sd.supply_price) as refunded_cos'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->groupBy('s.id')
            ->get()
            ->toArray();

        $data = [];
        $loop = 0;
        foreach ($report as $r){
            $loop = $loop +1;

            $data[]= [
                $loop,
                date('d M Y',strtotime($r['sale_at'])),
                $r['admission_no'],
                $r['patient_name'],
                $r['procedure_name'],
                $r['doctor_name'],
                number_format($r['total_after_disc']-$r['refunded_retail'],2),
                number_format($r['cos']-$r['refunded_cos'],2),
                number_format(($r['total_after_disc']-$r['refunded_retail'])-($r['cos']-$r['refunded_cos']),2),
                number_format(((($r['total_after_disc']-$r['refunded_retail'])-($r['cos']-$r['refunded_cos']))/($r['total_after_disc']-$r['refunded_retail']))*100,2) . ' %',
                $r['issued_by'] .date('d M Y h:i A',strtotime($r['sale_at'])),

            ];

        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#', 'Date', 'Adm #	', 'Patient Name', 'Procedure', 'Doctor', 'Medicine Issued Amount', 'Medicine Issued COS', 'Gross Profit (PKR)', 'Gross Margin %', 'Issued By', 'Remarks']);

        $csv->insertAll($data);

        $csv->output('Inter Transfer Ipd Medicines ' . '.csv');
    }


    }