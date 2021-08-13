<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Carbon\CarbonPeriod;
use Devzone\Pharmacy\Http\Traits\DashboardDate;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomisedSalesSummary extends Component
{
    use DashboardDate;
    public $data=[];
    public function mount()
    {
        $this->prepareDate();
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
            ->whereBetween('s.sale_at', [$this->from, $this->to])
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
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->select(
                'sd.sale_id',
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos')
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
            if ($this->type=='month'){
                $first=collect($sale_return)->where('month',$s['month'])->first();
                $sale[$key]['return_total']=$first['return_total'];
                $sale[$key]['return_cos']=$first['return_cos'];
            }elseif ($this->type=='week'){
                $first=collect($sale_return)->where('week',$s['week'])->first();
                $sale[$key]['return_total']=$first['return_total'];
                $sale[$key]['return_cos']=$first['return_cos'];
            }elseif ($this->type=='date'){
                $first=collect($sale_return)->where('date',$s['date'])->first();
                $sale[$key]['return_total']=$first['return_total'];
                $sale[$key]['return_cos']=$first['return_cos'];
            }
        }
        $this->data=$sale;
    }
}