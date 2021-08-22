<?php


namespace Devzone\Pharmacy\Http\Traits;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

trait DashboardDate
{
    public $date;
    public $pre_to;
    public $to;
    public $from;
    public $type;
    public $label=[];
    public $label_plucked;
    public $display_date;


    public function prepareDate(){
        $this->pre_to=new Carbon($this->date);
        if ($this->type=='date'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->subDays(5);
            $result = CarbonPeriod::create($this->from, '1 day', $this->pre_to);
            foreach ($result as $key=>$dt) {
                $this->label[$key]['label']=$dt->format("d, M Y");
                $this->label[$key]['format']=$dt->format("Y-m-d");
            }
            $this->display_date=date('d M Y',strtotime($this->pre_to));
        }elseif ($this->type=='week'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->subWeek(5)->startOfWeek();
            $result = CarbonPeriod::create($this->from, '7 day', $this->pre_to);
            foreach ($result as $key=>$dt) {
                $last_date=$dt->copy()->endOfWeek();
                $this->label[$key]['label']=$dt->format("d M").' - '.$last_date->format("d M");
                $this->label[$key]['format']=$dt->weekOfYear;
            }
            $this->display_date=date('d M Y',strtotime($this->from)).' - '.date('d M Y',strtotime($this->pre_to));
        }elseif ($this->type=='month'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->subMonth(5)->firstOfMonth();
            $result = CarbonPeriod::create($this->from, '1 month', $this->pre_to);
            foreach ($result as $key=>$dt) {
                $this->label[$key]['label']=$dt->format("M, Y");
                $this->label[$key]['format']=$dt->month;
            }
            $this->display_date=date('M Y',strtotime($this->from)).' - '.date('M Y',strtotime($this->pre_to));
        }
        $this->to=$this->pre_to->copy()->endOfDay();
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
