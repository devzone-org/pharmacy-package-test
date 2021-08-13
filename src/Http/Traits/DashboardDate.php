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

    public function prepareDate(){
        $this->date='2021-08-10';
        $this->type = 'date';
        $this->pre_to=new Carbon($this->date);
        if ($this->type=='date'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->subDays(5);
            $result = CarbonPeriod::create($this->from, '1 day', $this->pre_to);
            foreach ($result as $key=>$dt) {
                $this->label[$key]['label']=$dt->format("d, M");
                $this->label[$key]['format']=$dt->format("Y-m-d");
            }
        }elseif ($this->type=='week'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->subWeek(5)->startOfWeek();
            $result = CarbonPeriod::create($this->from, '7 day', $this->pre_to);
            foreach ($result as $key=>$dt) {
                $last_date=$dt->copy()->endOfWeek();
                $this->label[$key]['label']=$dt->format("d M").' - '.$last_date->format("d M");
                $this->label[$key]['format']=$dt->weekOfYear;
            }
        }elseif ($this->type=='month'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->subMonth(5)->firstOfMonth();
            $result = CarbonPeriod::create($this->from, '1 month', $this->pre_to);
            foreach ($result as $key=>$dt) {
                $this->label[$key]['label']=$dt->format("M, Y");
                $this->label[$key]['format']=$dt->month;
            }
        }
        $this->to=$this->pre_to->copy()->endOfDay();
    }
}