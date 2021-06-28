<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleTransaction extends Component
{
    public $from;
    public $to;
    public $report = [];

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-10 days'));
        $this->to = date('Y-m-d');
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
            ->whereDate('s.sale_at', '<=', $this->to)
            ->whereDate('s.sale_at', '>=', $this->from)
            ->select('s.sale_at', 's.id', 'p.name as patient_name', DB::raw('sum(sd.total) as total'), DB::raw('sum(sd.total_after_disc) as total_after_disc'), 'u.name as sale_by')
            ->orderBy('s.id','desc')
            ->groupBy('sd.sale_id')->get()
            ->toArray();
    }
}
