<?php

namespace Devzone\Pharmacy\Http\Controllers\Exports;

use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use SplTempFileObject;
use League\Csv\Writer;


class SaleTransactionExport
{
    protected $doctor_id;
    protected $salesman_id;
    protected $from;
    protected $to;

    public function __construct()
    {
        $request = request();
        $this->doctor_id = $request->doctor_id;
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
        $report = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->when(!empty($this->salesman_id), function ($q) {
                return $q->where('s.sale_by', $this->salesman_id);
            })
            ->when(!empty($this->doctor_id), function ($q) {
                if ($this->doctor_id == 'walk') {
                    return $q->whereNull('s.referred_by');
                } else {
                    return $q->where('s.referred_by', $this->doctor_id);
                }

            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->select('s.sale_at', 'e.name as doctor', 's.is_credit', 's.is_paid', 's.id', 'p.name as patient_name', DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('sum(sd.total) as total'), DB::raw('sum(sd.total_after_disc) as total_after_disc'),
                'u.name as sale_by')
            ->orderBy('s.id', 'desc')
            ->groupBy('sd.sale_id')->get()
            ->toArray();
        $sale_return = SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->join('sales as s', 's.id', '=', 'sr.sale_id')
            ->when(!empty($this->salesman_id), function ($q) {
                return $q->where('s.sale_by', $this->salesman_id);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('sr.updated_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('sr.updated_at', '>=', $this->formatDate($this->from));
            })
            ->select('sd.sale_id', DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos')
            )
            ->groupBy('sr.sale_detail_id')->get();

        $data = [];
        foreach ($report as $key => $rep) {
            if ($sale_return->isNotEmpty()) {
                $report[$key]['sale_return'] = $sale_return->where('sale_id', $rep['id'])->sum('return_total');
                $report[$key]['cos'] = $report[$key]['cos'] - $sale_return->where('sale_id', $rep['id'])->sum('return_cos');
            } else {
                $report[$key]['sale_return'] = 0;
            }
        }
        $loop = 0;
        foreach ($report as $key => $rep) {
            $total_after_disc = $rep['total_after_disc'] - $rep['sale_return'];
            $total_after_disc = empty($total_after_disc) ? 1 : $total_after_disc;

            $loop = $loop + 1;
            $data[] = [
                'sr_no' => $loop,
                'status' => $rep['is_paid'] == 't' ? 'Paid' : 'UnPaid',
                'sale_date' => $rep['sale_at'],
                'invoice_no' => $rep['id'],
                'doctor' => $rep['doctor'] ?? 'Walk In',
                'patient_name' => $rep['patient_name'] ?? '',
                'sale' => number_format($rep['total'], 2),
                'discount' => number_format($rep['total'] - $rep['total_after_disc'], 2),
                'sale_return' => number_format($rep['sale_return'], 2),
                'net_sale' => number_format($rep['total_after_disc'] - $rep['sale_return'], 2),
                'cash' => number_format($rep['total_after_disc'] - $rep['sale_return'], 2),
                'credit' => number_format($rep['total_after_disc'] - $rep['sale_return'], 2),
                'cos' => number_format($rep['cos'], 2),
                'gross_profit' => number_format($rep['total_after_disc'] - $rep['sale_return'] - $rep['cos'], 2),
                'gross_margin' => number_format((($rep['total_after_disc'] - $rep['sale_return'] - $rep['cos']) / $total_after_disc) * 100, 2),
                'sold_by' => number_format((($rep['total_after_disc'] - $rep['sale_return'] - $rep['cos']) / $total_after_disc) * 100, 2),
            ];
        }


        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['Sr#', 'Status', 'Sale Date', 'Invoice #', 'Doctor', 'Patient', 'Sale (PKR)', 'Discount (PKR)', 'Sale Return (PKR)', 'Net Sale (PKR)(A)', 'Cash', 'Credit', 'COS (PKR)(B)', 'Gross Profit (PKR)(A-B)', 'Gross Margin (A-B)/A', 'Sold By']);

        $csv->insertAll($data);

        $csv->output('Sale Transaction Report ' . '.csv');


    }


}