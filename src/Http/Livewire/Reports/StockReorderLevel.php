<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Devzone\Pharmacy\Models\Product;
use Livewire\Component;

class StockReorderLevel extends Component
{
    public $report = [];

    public function mount()
    {
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.stock-reorder-level');
    }

    public function search()
    {
        $products = Product::from('products as p')
            ->join('product_inventories as pi',function ($q){
                return $q->on('pi.product_id', '=', 'p.id');
            })
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->select(
                'p.id', 'p.name as item', 'p.type', 'p.reorder_level','p.reorder_qty','m.name as manufacturer',
            )
            ->groupBy('pi.product_id')
            ->orderBy('p.id', 'ASC')
            ->get();
        foreach ($products as $key => $product) {
            $this->report[$key]['id'] = $product->id;
            $this->report[$key]['item'] = $product->item;
            $this->report[$key]['type'] = $product->type;
            $this->report[$key]['manufacturer'] = $product->manufacturer;
            $this->report[$key]['stock_in_hand'] = $product->inventories->sum('qty');
            $this->report[$key]['reorder_level'] = $product->reorder_level;
            $this->report[$key]['reorder_qty'] = $product->reorder_qty;
        }
    }

}