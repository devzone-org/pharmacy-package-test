<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Customer;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use SplTempFileObject;


class CustomerReceivablesExport
{

    protected $patient_id;

    public function __construct()
    {
        $request = request();
        $this->patient_id = $request->patient_id;
    }

    public function download()
    {
        $report = Customer::from('customers as cus')
            ->join('patients as pat', 'pat.customer_id', '=', 'cus.id')
            ->join('ledgers as ld', 'ld.account_id', '=', 'cus.account_id')
            ->leftjoin('employees as emp', 'emp.id', '=', 'cus.employee_id')
            ->when(!empty($this->patient_id), function ($q) {
                return $q->where('pat.id', $this->patient_id);
            })
            ->groupBy('cus.name')
            ->select('cus.name', 'cus.credit_limit', 'emp.name as care_of', DB::raw('(SUM(ld.debit)-SUM(ld.credit)) as total_receivable'))
            ->get()->toArray();

        $data = [];
        $loop =0;
        foreach ($report as $r){
            $loop =$loop + 1;
            $data[] = [
                $loop,
                $r['name'],
                $r['care_of'],
                number_format($r['credit_limit'], 2),
                number_format($r['total_receivable'], 2),

            ];
        }

        $data[]=[
            '',
            '',
            'Grand Total',
            number_format(collect($report)->sum('credit_limit'), 2),
            number_format(collect($report)->sum('total_receivable'), 2),
        ];

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#','Customer','In Care Of', 'Credit Limit', 'Total Receivable']);

        $csv->insertAll($data);

        $csv->output('Customer Receivables ' . '.csv');
    }


}