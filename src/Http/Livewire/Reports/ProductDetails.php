<?php

namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductDetails extends Component
{
    use Searchable;
    public $product_id;
    public $product_name;
    public $report;

    public function mount(){
        $this->search();
    }

    public function search()
    {
        $this->report = Product::from('products as p')
            ->join('purchase_orders as po', 'po.product_id', '=', 'p.id')
            ->join('purchases as pur', 'pur.id', '=', 'po.purchase_id')
            ->Join('suppliers as s','s.id','=','pur.supplier_id')
            ->where('p.id',$this->product_id)
            ->select('p.name as product_name','po.*','s.name as s_name')
            ->groupBy('po.id')
            ->get()
            ->toArray();
    }

    public function render(){
        return view('pharmacy::livewire.reports.product-details');
    }

}