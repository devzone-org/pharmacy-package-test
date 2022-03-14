<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Http\Traits\WithSorting;
use Devzone\Pharmacy\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsList extends Component
{
    use WithPagination,WithSorting;

    public $success = '';
    public $name;
    public $salt;


    public function render()
    {
        $products = Product::from('products as p')
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->leftJoin('product_inventories as pi','pi.product_id','=','p.id')
            ->select('p.*', 'm.name as m_name', 'c.name as c_name', 'r.name as r_name', 'r.tier','pi.qty')
            ->when(!empty($this->name),function($q){
                return $q->where('p.name','LIKE','%'.$this->name.'%');
            })
            ->when(!empty($this->salt),function($q){
                return $q->where('p.salt','LIKE','%'.$this->salt.'%');
            })
            ->when(!empty($this->sortBy),function($q){
                $q->orderBy($this->sortBy,$this->sortDirection);
            })
            ->paginate(20);

        return view('pharmacy::livewire.master-data.products-list', ['products' => $products]);
    }


    public function search(){
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->reset(['name','salt']);
        $this->resetPage();
    }


}
