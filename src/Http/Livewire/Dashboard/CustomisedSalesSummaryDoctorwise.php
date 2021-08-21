<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Carbon\CarbonPeriod;
use Devzone\Pharmacy\Http\Traits\DashboardDate;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomisedSalesSummaryDoctorwise extends Component
{
    use DashboardDate;
    public $data=[];
    public function mount()
    {
        $this->type='week';
//        $this->date=date('Y-m-d');
        $this->date='2021-08-10';
        $this->prepareDate();
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.customised-sales-summary-doctorwise');
    }

    public function search()
    {
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr','sr.sale_detail_id','=','sd.id')
            ->leftJoin('employees as e','e.id','=','s.referred_by')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->groupBy('s.referred_by')
            ->select(
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum(sd.total_after_disc) as total'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sales'),
                DB::raw('sum(sr.refund_qty) as refund_qty'),
                DB::raw('sum(sd.qty) as total_sale_qty'),
                DB::raw('sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) as unit'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) as total_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) as total_after_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) as total_profit'),
                's.referred_by','e.name as doctor',
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
            ->orderBy('total_after_refund','DESC')
            ->get()
            ->toArray();
        foreach(collect($sale)->groupBy($this->type) as $s){
            foreach($s->take(5) as $f){
                $this->data[]=$f;
            }
        }
    }
}