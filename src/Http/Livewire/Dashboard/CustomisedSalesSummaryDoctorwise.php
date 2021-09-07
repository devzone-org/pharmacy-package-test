<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Carbon\CarbonPeriod;
use Devzone\Pharmacy\Http\Traits\DashboardDate;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomisedSalesSummaryDoctorwise extends Component
{
    public $date;
    public $pre_to;
    public $to;
    public $from;
    public $type;
    public $label=[];
    public $label_plucked;
    public $display_date;

    public $data = [];
    public $result = [];
    public $dr_label;

    public function mount()
    {
        $this->type = 'date';
        $this->date = date('Y-m-d');

        $this->prepareDate();
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.customised-sales-summary-doctorwise');
    }

    public function search()
    {
        $this->reset(['result', 'data']);
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
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
                's.referred_by', 'e.name as doctor',
            )
            ->orderBy('total_after_refund', 'DESC')
            ->limit(5)
            ->get()
            ->toArray();


        $data = [];
        foreach ($sale as $s) {
            $data[] = [
                'doctor' => empty($s['doctor']) ? 'External' : $s['doctor'],
                'total_after_refund' => round($s['total_after_refund']),
                'total_profit' => round($s['total_profit']),
            ];
        }

        $dr_label = array_unique(collect($data)->pluck('doctor')->toArray());
        $this->dr_label = json_encode($dr_label);
        $this->result[] = [
            'name' => 'Sale',
            'data' => collect($data)->pluck('total_after_refund')->toArray()
        ];

        $this->result[] = [
            'name' => 'Gross Profit',
            'data' => collect($data)->pluck('total_profit')->toArray()
        ];
        $this->result = json_encode($this->result);

        $this->dispatchBrowserEvent('doctor-wise-sale', ['result' => $this->result, 'label' => $this->dr_label]);

    }





    public function prepareDate(){
        $this->pre_to=new Carbon($this->date);
        if ($this->type=='date'){
            $this->reset('label');
            $this->from=$this->pre_to->copy();
            $this->to=$this->pre_to->copy();
            $this->display_date=date('d M Y',strtotime($this->pre_to));
        }elseif ($this->type=='week'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->startOfWeek();
            $this->to=$this->pre_to->copy()->endOfWeek();
            $this->display_date=date('d M Y',strtotime($this->from)).' - '.date('d M Y',strtotime($this->pre_to));
        }elseif ($this->type=='month'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->firstOfMonth();
            $this->to=$this->pre_to->copy()->endofMonth();
            $this->display_date=date('M Y',strtotime($this->from));
        }

        $this->label_plucked=json_encode(collect($this->label)->pluck('label')->toArray());


    }
    public function changeType($type){
        $this->date = date('Y-m-d');
        $this->type=$type;
        $this->prepareDate();
    }

    public function changeDate($direction){
        if ($direction=='prev'){
            if ($this->type=='date'){
                $this->date=date('Y-m-d', strtotime('-1 day', strtotime($this->date)));
            }elseif ($this->type=='week'){
                $this->date=date('Y-m-d', strtotime('-1 week', strtotime($this->date)));
            }elseif ($this->type=='month'){
                $this->date=date('Y-m-d', strtotime('-1 month', strtotime($this->date)));
            }

        }elseif($direction=='next'){
            if ($this->type=='date'){
                $this->date=date('Y-m-d', strtotime('+1 day', strtotime($this->date)));
            }elseif ($this->type=='week'){
                $this->date=date('Y-m-d', strtotime('+1 week', strtotime($this->date)));
            }elseif ($this->type=='month'){
                $this->date=date('Y-m-d', strtotime('+1 month', strtotime($this->date)));
            }
        }
        $this->prepareDate();
    }
}
