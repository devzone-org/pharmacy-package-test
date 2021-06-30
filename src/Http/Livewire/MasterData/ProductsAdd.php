<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Livewire\Component;

class ProductsAdd extends Component
{
    use Searchable;

    public $name;
    public $salt;
    public $barcode;
    public $packing;
    public $cost_of_price;
    public $retail_price;
    public $manufacture_name;
    public $manufacture_id;
    public $rack_name;
    public $rack_id;
    public $category_name;
    public $category_id;
    public $reorder_level;
    public $reorder_qty;
    public $narcotics = false;
    public $status = 'f';
    public $success = '';
    public $control_medicine;


    protected $rules = [
        'name' => 'required|string|unique:products,name',
        'status' => 'required|in:t,f',
        'packing' => 'required|integer',
        'retail_price' => 'nullable|numeric',
        'cost_of_price' => 'nullable|numeric',
        'manufacture_id' => 'nullable|integer',
        'category_id' => 'nullable|integer',
        'rack_id' => 'nullable|integer',
        'reorder_level' => 'nullable|integer',
        'reorder_qty' => 'nullable|integer',
    ];


    public function render()
    {
        return view('pharmacy::livewire.master-data.products-add');
    }

    public function create()
    {
        $this->validate();

        Product::create([
            'name' => $this->name,
            'salt' => $this->salt,
            'barcode' => $this->barcode,
            'packing' => $this->packing,
            'cost_of_price' => !empty($this->cost_of_price) ? $this->cost_of_price : 0,
            'retail_price' => !empty($this->retail_price) ? $this->retail_price : 0,
            'rack_id' => $this->rack_id,
            'manufacture_id' => $this->manufacture_id,
            'category_id' => $this->category_id,
            'reorder_level' => $this->reorder_level,
            'reorder_qty' => $this->reorder_qty,
            'narcotics' => !empty($this->narcotics) ? 't' : 'f',
            'status' => $this->status,
            'control_medicine' => $this->control_medicine
        ]);

        $this->success = 'Record has been added.';
        $this->reset(['status', 'narcotics', 'reorder_qty', 'reorder_level', 'category_id', 'category_name'
            , 'manufacture_id', 'manufacture_name', 'rack_id', 'rack_name', 'retail_price', 'cost_of_price', 'packing',
            'barcode', 'salt', 'name']);
    }


}
