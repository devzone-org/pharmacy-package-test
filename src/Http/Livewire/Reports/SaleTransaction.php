<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleTransaction extends Component
{
    public $salemen=[];
    public $salesman_id;
    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range=false;

    public function mount()
    {
        $this->salemen=Sale::from('sales as s')
            ->join('users as u','u.id','=','s.sale_by')
            ->groupBy('s.sale_by')
            ->select('u.id','u.name')
            ->get()
            ->toArray();
        $this->from = date('Y-m-d', strtotime('-7 days'));
        $this->to = date('Y-m-d');
        $this->range='seven_days';
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.sales-transaction');
    }

    public function search()
    {
        $this->report = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->when(!empty($this->salesman_id),function ($q){
                return $q->where('s.sale_by',$this->salesman_id);
            })
            ->when(!empty($this->to),function ($q){
                return $q->whereDate('s.sale_at', '<=', $this->to);
            })
            ->when(!empty($this->from),function ($q){
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })
            ->select('s.sale_at', 's.id', 'p.name as patient_name', DB::raw('sum(sd.supply_price) as cos'),
                DB::raw('sum(sd.total) as total'),DB::raw('sum(sd.total_after_disc) as total_after_disc'),
                'u.name as sale_by')
            ->orderBy('s.id','desc')
            ->groupBy('sd.sale_id')->get()
            ->toArray();
        $sale_return=SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->join('sales as s', 's.id', '=', 'sr.sale_id')
            ->when(!empty($this->salesman_id), function ($q) {
                return $q->where('s.sale_by', $this->salesman_id);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('sr.updated_at', '<=', $this->to);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('sr.updated_at', '>=', $this->from);
            })
            ->select('sd.sale_id',DB::raw('sum(sd.total_after_disc) as return_total')
                )
            ->groupBy('sr.sale_detail_id')->get();
        foreach ($this->report as $key=>$rep){
            if ($sale_return->isNotEmpty()){
                $this->report[$key]['sale_return']=$sale_return->where('sale_id',$rep['id'])->sum('return_total');
            }
            else{
                $this->report[$key]['sale_return']=0;
            }
        }
    }
    public function resetSearch(){
        $this->reset('salesman_id');
    }
    public function updatedRange($val){
        if ($val=='custom_range'){
            $this->date_range=true;

        }elseif($val=='seven_days'){
            $this->date_range=false;
            $this->from = date('Y-m-d', strtotime('-7 days'));
            $this->to = date('Y-m-d');
            $this->search();
        }elseif($val=='thirty_days'){
            $this->date_range=false;
            $this->from = date('Y-m-d', strtotime('-30 days'));
            $this->to = date('Y-m-d');
            $this->search();
        }elseif($val=='yesterday'){
            $this->date_range=false;
            $this->from = date('Y-m-d', strtotime('-1 days'));
            $this->to = date('Y-m-d', strtotime('-1 days'));
            $this->search();
        }
        elseif($val=='today'){
            $this->date_range=false;
            $this->from = date('Y-m-d');
            $this->to = date('Y-m-d');
            $this->search();
        }
    }
}
