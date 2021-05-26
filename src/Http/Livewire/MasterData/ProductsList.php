<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsList extends Component
{
    use WithPagination;

    public $success = '';


    public function render()
    {
        $products = Product::from('products as p')
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->select('p.*', 'm.name as m_name', 'c.name as c_name', 'r.name as r_name', 'r.tier')
            ->orderBy('p.id','desc')
            ->paginate(20);

        return view('pharmacy::livewire.master-data.products-list', ['products' => $products]);
    }


}
