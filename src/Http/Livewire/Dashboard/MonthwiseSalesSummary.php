<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonthwiseSalesSummary extends Component
{
    public $data=[];
    public $date;
    public $to;
    public $from;
    public function mount()
    {
        $this->to=Carbon::now();
        $this->from=$this->to->copy()->subMonth(2)->firstOfMonth();
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.monthwise-sales-summary');
    }

    public function search()
    {
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr','sr.sale_detail_id','=','sd.id')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->select(
                's.sale_at',
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('sum(sd.total_after_disc) as total'),
                DB::raw('sum(sr.refund_qty) as refund'),
                DB::raw('sum(sd.qty) as total_sale_qty'),
                DB::raw('sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) as unit'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) as total_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) as total_after_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) as total_profit')
            )
            ->groupBy('month')
            ->orderBy('month','DESC')
            ->get()
            ->toArray();
        $purchase=Purchase::from('purchases as pur')
            ->join('purchase_receives as pr','pr.purchase_id','=','pur.id')
            ->whereBetween('pr.created_at', [$this->from, $this->to])
            ->select(
                'pr.created_at',
                DB::raw('MONTH(pr.created_at) as month'),
                DB::raw('sum(pr.total_cost) as total'),
            )
            ->groupBy('month')
            ->orderBy('month','DESC')
            ->get()
            ->toArray();
        foreach ($sale as $key=>$s){
            $pur=collect($purchase)->where('month',$s['month'])->first();
            $sale[$key]['purchase']=!empty($pur) ? $pur['total'] : 0;
        }
        $this->data=$sale;
    }
}