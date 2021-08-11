<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopSellingProducts extends Component
{
    public $date;
    public $type;
    public $to;
    public $from;
    public $report_type;
    public function mount($report_type)
    {
        $this->report_type=$report_type;
        $this->date='2021-08-11';
        $this->type = 'month';
        $this->to=new Carbon($this->date);
        if ($this->type=='date'){
            $this->from=$this->to->copy();
        }elseif ($this->type=='week'){
            $this->from=$this->to->copy()->startOfWeek();
        }elseif ($this->type=='month'){
            $this->from=$this->to->copy()->firstOfMonth();
        }
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.top-selling-products-revenuewise');
    }

    public function search()
    {
        $sale = SaleDetail::from('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->join('products as p','p.id','=','sd.product_id')
            ->whereBetween('sd.created_at', [$this->from, $this->to])
            ->groupBy('sd.product_id')
            ->select(
                'p.name as product','sd.product_id',
                'sd.created_at',
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('count(sd.product_id) as no_of_products'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
                DB::raw('sum(sd.total_after_disc-(sd.qty*sd.supply_price)) as total_profit'),
            )
            ->when($this->report_type=='revenue',function ($q){
               return $q->orderBy('total_after_disc','DESC');
            })
            ->when($this->report_type=='profit',function ($q){
                return $q->orderBy('total_profit','DESC');
            })
            ->limit(10)
            ->get()
            ->toArray();
        dump($sale);
    }
}