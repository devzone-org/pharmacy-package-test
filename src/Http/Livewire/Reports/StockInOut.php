<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockInOut extends Component
{
    use Searchable;
    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range = false;
    public $product_id;
    public $product_name;
    public $manufacture_id;
    public $manufacture_name;
    public $rack_id;
    public $rack_name;
    public $category_id;
    public $category_name;
    public $zero_stock;

    public function mount()
    {
        $this->date_range=false;
        $this->from = date('Y-m-d', strtotime('-7 days'));
        $this->to = date('Y-m-d');
        $this->range = 'seven_days';
        $this->zero_stock = 'f';
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.stock-in-out');
    }

    public function search()
    {
        try {

            $from = Carbon::parse($this->from);
            $to = Carbon::parse($this->to);

            $diff = $to->diffInDays($from);


            if ($diff > 60) {
                throw new \Exception('Custom range cannot be selected for more than 2 months.');
            }

            $this->reset('report');
            $products = InventoryLedger::from('inventory_ledgers as il')
                ->join('products as p', 'p.id', '=', 'il.product_id')
                ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                ->when(!empty($this->product_id), function ($q) {
                    return $q->where('p.id', $this->product_id);
                })
                ->when(!empty($this->manufacture_id), function ($q) {
                    return $q->where('p.manufacture_id', $this->manufacture_id);
                })
                ->when(!empty($this->rack_id), function ($q) {
                    return $q->where('p.rack_id', $this->rack_id);
                })
                ->when(!empty($this->category_id), function ($q) {
                    return $q->where('p.category_id', $this->category_id);
                })
                ->when(!empty($this->to), function ($q) {
                    return $q->whereDate('il.created_at', '<=', $this->to);
                })
                ->when(!empty($this->from), function ($q) {
                    return $q->whereDate('il.created_at', '>=', $this->from);
                })
                ->select('p.id as product_id', 'p.name as item', 'p.type as product_type', 'm.name as manufacturer', 'c.name as category', 'r.name as rack',
                    'il.increase', 'il.decrease', 'il.type')
                ->get();
            $previous = InventoryLedger::whereDate('created_at', '<', $this->from)
                ->groupBy('product_id')
                ->when(!empty($this->product_id), function ($q) {
                    return $q->where('product_id', $this->product_id);
                })
                ->select('product_id', DB::raw('sum(decrease) as decrease'), DB::raw('sum(increase) as increase'))
                ->get();

            foreach ($products->groupBy('product_id') as $key => $product_grouped) {
                $product = $product_grouped->first();
                $this->report[$key]['id'] = $product->product_id;
                $this->report[$key]['item'] = $product->item;
                $this->report[$key]['manufacturer'] = $product->manufacturer;
                $this->report[$key]['category'] = $product->category;
                $this->report[$key]['rack'] = $product->rack;
                $this->report[$key]['type'] = $product->product_type;
                $this->report[$key]['sales'] = $product_grouped->where('type', 'sale')->sum('decrease');
                $this->report[$key]['sale_return'] = $product_grouped->where('type', 'sale-refund')->sum('increase');
                $this->report[$key]['purchases'] = $product_grouped->where('type', 'purchase')->sum('increase') + $product_grouped->where('type', 'purchase-bonus')->sum('increase');
                $this->report[$key]['purchase_return'] = $product_grouped->where('type', 'purchase-refund')->sum('decrease');
                $this->report[$key]['adjustment'] = $product_grouped->where('type', 'adjustment')->sum('increase') - $product_grouped->where('type', 'adjustment')->sum('decrease');
                $this->report[$key]['opening_stock'] = $previous->where('product_id', $product->product_id)->sum('increase') - $previous->where('product_id', $product->product_id)->sum('decrease');
                $closing = ($this->report[$key]['opening_stock'] - ($this->report[$key]['sales'] + $this->report[$key]['purchase_return'])) + $this->report[$key]['sale_return'] + $this->report[$key]['purchases'] + ($this->report[$key]['adjustment']);
                $this->report[$key]['closing_stock'] = $closing;
            }
//        $this->report=collect($this->report)->sortByDesc('opening_stock');
        }
        catch (\Exception $e) {
                $this->addError('error', $e->getMessage());
            }
    }

    public function resetSearch()
    {
        $this->reset('product_id', 'product_name', 'rack_id', 'rack_name', 'category_id', 'category_name', 'manufacture_id', 'manufacture_name');
        $this->search();
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
            $this->to = date('Y-m-d');
            $this->search();
        } elseif ($val == 'today') {
            $this->date_range = false;
            $this->from = date('Y-m-d');
            $this->to = date('Y-m-d');
            $this->search();
        }
    }
}