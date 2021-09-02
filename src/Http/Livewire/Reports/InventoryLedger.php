<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InventoryLedger extends Component
{
    use Searchable;

    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range = false;
    public $product_id;
    public $product_name;
    public $opening_inv;

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-7 days'));
        $this->to = date('Y-m-d');
        $this->range = 'seven_days';
//        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.inventory-ledger');
    }

    public function search()
    {
        if (!empty($this->product_id)) {
            $this->report = \Devzone\Pharmacy\Models\InventoryLedger::from('inventory_ledgers as il')
                ->join('products as p', 'p.id', '=', 'il.product_id')
                ->where('il.product_id', $this->product_id)
                ->when(!empty($this->to), function ($q) {
                    return $q->whereDate('il.created_at', '<=', $this->to);
                })
                ->when(!empty($this->from), function ($q) {
                    return $q->whereDate('il.created_at', '>=', $this->from);
                })
                ->select('p.name as item', 'p.type as product_type','il.*')
                ->get()->toArray();
            $open_details = \Devzone\Pharmacy\Models\InventoryLedger::whereDate('created_at','<',$this->from)
                ->where('product_id',$this->product_id)
                ->groupBy('product_id')
                ->select('product_id',DB::raw('sum(increase) as increase'),DB::raw('sum(decrease) as decrease'))
                ->first();
            $this->opening_inv = $open_details['increase']-$open_details['decrease'];
        }

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