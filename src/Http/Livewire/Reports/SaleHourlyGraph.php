<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleHourlyGraph extends Component
{
    public $date;
    public $report = [];
    public function mount(){
        $this->date=date('Y-m-d');
        $this->search();
    }
    public function render(){
        return view('pharmacy::livewire.reports.sale-hourly-graph');
    }
    public function search()
    {
        $this->report = Sale::from('sales as s')
            ->when(!empty($this->date), function ($q) {
                return $q->whereDate('s.sale_at', $this->date);
            })
            ->select(
                DB::raw('hour(s.sale_at) as hour'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
            )
            ->groupBy('hour')
            ->get()
            ->toArray();
        dd($this->report);
    }
}