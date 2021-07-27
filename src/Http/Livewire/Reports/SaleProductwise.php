<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use App\Models\Hospital\Department;
use App\Models\Hospital\Employees\Employee;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleProductwise extends Component
{
    use Searchable;
    public $range;
    public $from;
    public $to;
    public $product_id;
    public $product_name;
    public $report = [];
    public $products = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-7 days'));
        $this->to = date('Y-m-d');
        $this->range = 'seven_days';
        $this->search();
    }
    public function render(){
        return view('pharmacy::livewire.reports.sale-productwise');
    }
    public function search()
    {
        $this->report = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->join('products as p','p.id','=','sd.product_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->to);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })
            ->when(!empty($this->product_id),function ($q){
                return $q->where('sd.product_id',$this->product_id);
            })
            ->select(
                'p.name as product_name',
                DB::raw('sum(sd.qty) as qty'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->groupBy('sd.product_id')
            ->orderBy('qty','DESC')
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