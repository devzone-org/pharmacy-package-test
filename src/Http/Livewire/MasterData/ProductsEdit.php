<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Livewire\Component;

class ProductsEdit extends Component
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
    public $primary_id;


    protected $rules = [
        'name' => 'required|string',
        'status' => 'required|in:t,f',
        'packing' => 'nullable|integer',
        'retail_price' => 'nullable|numeric',
        'cost_of_price' => 'nullable|numeric',
        'manufacture_id' => 'nullable|integer',
        'category_id' => 'nullable|integer',
        'rack_id' => 'nullable|integer',
        'reorder_level' => 'nullable|integer',
        'reorder_qty' => 'nullable|integer',
    ];

    public function mount($primary_id)
    {
        $this->primary_id = $primary_id;
        $products = Product::from('products as p')
            ->where('p.id', $primary_id)
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->select('p.*', 'm.name as m_name', 'c.name as c_name', 'r.name as r_name', 'r.tier')
            ->get();

        if ($products->isEmpty()) {
            return redirect()->to('/pharmacy/master-data/products');
        } else {
            $products = $products->first()->toArray();
            $this->name = $products['name'];
            $this->salt = $products['salt'];
            $this->barcode = $products['barcode'];
            $this->packing = $products['packing'];
            $this->cost_of_price = $products['cost_of_price'];
            $this->retail_price = $products['retail_price'];
            $this->manufacture_name = $products['m_name'];
            $this->manufacture_id = $products['manufacture_id'];
            $this->rack_id = $products['rack_id'];
            $this->rack_name = $products['r_name'];
            $this->category_id = $products['category_id'];
            $this->category_name = $products['c_name'];
            $this->reorder_qty = $products['reorder_qty'];
            $this->reorder_level = $products['reorder_level'];
            $this->status = $products['status'];
            $this->narcotics = $products['narcotics'] == 't' ? true : false;
        }
    }

    public function render()
    {
        return view('pharmacy::livewire.master-data.products-edit');
    }

    public function create()
    {
        $this->validate();
        if(Product::where('id','!=',$this->primary_id)->where('name',$this->name)->exists()){
            $this->addError('name','This name is already exists.');
            return false;
        }
        $pro = Product::find($this->primary_id);
        $pro->update([
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
            'status' => $this->status
        ]);

        $this->success = 'Record has been updated.';

    }


}