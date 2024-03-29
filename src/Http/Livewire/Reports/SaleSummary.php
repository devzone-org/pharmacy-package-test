<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Carbon\Carbon;
use Devzone\Pharmacy\Models\Sale\OpenReturn;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleSummary extends Component
{
    public $range;
    public $from;
    public $to;
    public $time_from = '00:00';
    public $time_to = '23:59';
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('d M Y', strtotime('-7 days'));
        $this->to = date('d M Y');
        $this->range = 'seven_days';
        $this->search();
    }

    public function search()
    {
        $this->report = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->when(!empty($this->to), function ($q) {
                return $q->where('s.sale_at', '<=', $this->formatDate($this->to) . ' ' . $this->time_to . ':59');
            })
            ->when(!empty($this->from), function ($q) {
                return $q->where('s.sale_at', '>=', $this->formatDate($this->from) . ' ' . $this->time_from . ':00');
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
            ->get()
            ->toArray();
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
        $sale_return = Sale::from('sales as s')
            ->join('sale_refund_details as sfd', 'sfd.refunded_id', '=', 's.id')
            ->join('sale_details as sd', 'sd.id', '=', 'sfd.sale_detail_id')
            ->when(!empty($this->to), function ($q) {
                return $q->where('s.sale_at', '<=', $this->formatDate($this->to) . ' ' . $this->time_to . ':59');
            })
            ->when(!empty($this->from), function ($q) {
                return $q->where('s.sale_at', '>=', $this->formatDate($this->from) . ' ' . $this->time_from . ':00');
            })
            ->where('s.refunded_id', '>', 0)
            ->select(DB::raw('DATE(s.sale_at) as date'), DB::raw('sum(sd.retail_price_after_disc*sfd.refund_qty) as return_total'), DB::raw('sum(sd.supply_price*sfd.refund_qty) as return_cos'))
            ->groupBy('sfd.sale_detail_id')->get();

        $open = OpenReturn::when(!empty($this->to), function ($q) {
            return $q->where('created_at', '<=', $this->formatDate($this->to) . ' ' . $this->time_to . ':59');
        })
            ->when(!empty($this->from), function ($q) {
                return $q->where('created_at', '>=', $this->formatDate($this->from) . ' ' . $this->time_from . ':00');
            })->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total_after_deduction) as op_return'), DB::raw('sum(cost_of_price) as op_cos'))
            ->groupBy('date')
            ->get();

        foreach ($this->report as $key => $rep) {
            $this->report[$key]['open_return'] = $open->where('date', $rep['date'])->sum('op_return');
            $this->report[$key]['open_return_cos'] = $open->where('date', $rep['date'])->sum('op_cos');
            if ($sale_return->isNotEmpty()) {
                $this->report[$key]['sale_return'] = $sale_return->where('date', $rep['date'])->sum('return_total');
                $this->report[$key]['cos'] = $this->report[$key]['cos'] - $sale_return->where('date', $rep['date'])->sum('return_cos');
            } else {
                $this->report[$key]['sale_return'] = 0;
            }
        }
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.sale-summary');
    }

    public function updatedRange($val)
    {
        if ($val == 'custom_range') {
            $this->date_range = true;

        } elseif ($val == 'seven_days') {
            $this->date_range = false;
            $this->from = date('d M Y', strtotime('-7 days'));
            $this->to = date('d M Y');
            $this->search();
        } elseif ($val == 'thirty_days') {
            $this->date_range = false;
            $this->from = date('d M Y', strtotime('-30 days'));
            $this->to = date('d M Y');
            $this->search();
        } elseif ($val == 'yesterday') {
            $this->date_range = false;
            $this->from = date('d M Y', strtotime('-1 days'));
            $this->to = date('d M Y', strtotime('-1 days'));
            $this->search();
        } elseif ($val == 'today') {
            $this->date_range = false;
            $this->from = date('d M Y');
            $this->to = date('d M Y');
            $this->search();
        }
    }
}