<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomisedSalesSummary extends Component
{
    public $to;
    public $from;
    public $type;

    public function mount()
    {
        $this->type = 'month';
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.customised-sales-summary');
    }

    public function search()
    {
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
//            ->when(!empty($this->to), function ($q) {
//                return $q->whereDate('s.sale_at', '<=', $this->to);
//            })
//            ->when(!empty($this->from), function ($q) {
//                return $q->whereDate('s.sale_at', '>=', $this->from);
//            })
            ->select(
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->when($this->type == 'month', function ($q) {
                return $q->groupBy('month')
                    ->orderBy('month');
            })
            ->when($this->type == 'week', function ($q) {
                return $q->groupBy('week')
                    ->orderBy('week');
            })
            ->when($this->type == 'date', function ($q) {
                return $q->groupBy('date')
                    ->orderBy('date');
            })
            ->get()
            ->toArray();
        $sale_return = SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->join('sales as s', 's.id', '=', 'sr.sale_id')
//            ->when(!empty($this->to), function ($q) {
//                return $q->whereDate('sr.updated_at', '<=', $this->to);
//            })
//            ->when(!empty($this->from), function ($q) {
//                return $q->whereDate('sr.updated_at', '>=', $this->from);
//            })
            ->select('sd.sale_id',
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos',
                )
            )
            ->when($this->type == 'month', function ($q) {
                return $q->groupBy('month')
                    ->orderBy('month');
            })
            ->when($this->type == 'week', function ($q) {
                return $q->groupBy('week')
                    ->orderBy('week');
            })
            ->when($this->type == 'date', function ($q) {
                return $q->groupBy('date')
                    ->orderBy('date');
            })
            ->get()
            ->toArray();

        foreach ($sale as $key=>$s){

        }
        dd($sale_return);
    }
}