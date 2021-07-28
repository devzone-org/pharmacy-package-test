<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InterTransferIPDMedicines extends Component
{
    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-7 days'));
        $this->to = date('Y-m-d');
        $this->range = 'seven_days';
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.inter-transfer-IPD-medicines');
    }

    public function search()
    {
        $this->report = Sale::from('sales as s')
            ->join('admissions as a','a.id','=','s.admission_id')
            ->join('admission_job_details as ajd',function ($q){
                return $q->on('ajd.admission_id','=','a.id')
                    ->on('ajd.procedure_id','=','s.procedure_id');
            })
            ->join('employees as emp','emp.id','=','ajd.doctor_id')
            ->join('patients as p','p.id','=','a.patient_id')
            ->join('procedures as pro','pro.id','=','s.procedure_id')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sf','sf.sale_detail_id','=','sd.id')
            ->join('users as u','u.id','=','s.sale_by')

            ->whereNotNull('s.admission_id')
            ->whereNotNull('s.procedure_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->to);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })
            ->select(
                's.sale_at','a.admission_no','p.name as patient_name','emp.name as doctor_name','pro.name as procedure_name','u.name as issued_by',
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('sum(sf.refund_qty*sd.retail_price) as refunded_retail'),
                DB::raw('sum(sf.refund_qty*sd.supply_price) as refunded_cos'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->groupBy('s.id')
            ->get()
            ->toArray();
    }

    public function updatedRange($val)
    {
        if ($val == 'custom_range') {
            $this->date_range = true;

        } elseif ($val == 'seven_days') {
            $this->date_range = false;
            $this->from = date('Y-m-d', strtotime('-7 days'));
            $this->to = date('Y-m-d');
            $this->search();
        } elseif ($val == 'thirty_days') {
            $this->date_range = false;
            $this->from = date('Y-m-d', strtotime('-30 days'));
            $this->to = date('Y-m-d');
            $this->search();
        } elseif ($val == 'yesterday') {
            $this->date_range = false;
            $this->from = date('Y-m-d', strtotime('-1 days'));
            $this->to = date('Y-m-d', strtotime('-1 days'));
            $this->search();
        } elseif ($val == 'today') {
            $this->date_range = false;
            $this->from = date('Y-m-d');
            $this->to = date('Y-m-d');
            $this->search();
        }
    }
}