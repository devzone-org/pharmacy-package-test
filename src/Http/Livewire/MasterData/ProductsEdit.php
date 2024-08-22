<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\ProductInventory;
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
    public $retail_price_old;
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
    public $retail_price_notification = false;
    public $force_update = false;
    public $type;
    public $control_medicine;
    public $temperature;
    public $previous;
    public $next;
    public $discount_check;
    public $max_disc;
    public $all_categories;
    public $all_manufacturers;
    public $all_racks;

    protected $rules = [
        'name' => 'required|string',
        'status' => 'required|in:t,f',
        'packing' => 'required|integer',
        'retail_price' => 'nullable|numeric',
        'cost_of_price' => 'nullable|numeric',
        'manufacture_id' => 'nullable|integer',
        'category_id' => 'nullable|integer',
        'rack_id' => 'nullable|integer',
        'reorder_level' => 'nullable|integer',
        'reorder_qty' => 'nullable|integer',
        'max_disc' => 'nullable|numeric|between:0,100',
    ];

    public function mount($primary_id)
    {
        $this->primary_id = $primary_id;
        $this->all_categories = \Devzone\Pharmacy\Models\Category::where('status', 't')->get();
        $this->all_manufacturers = \Devzone\Pharmacy\Models\Manufacture::where('status', 't')->get();
        $this->all_racks = \Devzone\Pharmacy\Models\Rack::where('status', 't')->get();
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
            $this->retail_price_old = $products['retail_price'];
            $this->manufacture_name = $products['m_name'];
            $this->manufacture_id = $products['manufacture_id'];
            $this->rack_id = $products['rack_id'];
            $this->rack_name = $products['r_name'];
            $this->category_id = $products['category_id'];
            $this->category_name = $products['c_name'];
            $this->reorder_qty = $products['reorder_qty'];
            $this->reorder_level = $products['reorder_level'];
            $this->status = $products['status'];
            $this->type = $products['type'];
            $this->control_medicine = $products['control_medicine'];
            $this->narcotics = $products['narcotics'] == 't' ? true : false;
            $this->temperature=$products['temperature'];
            $this->discount_check = $products['discountable'];
            $this->max_disc = $products['max_discount'];

            // get previous user id
            $this->previous = Product::where('id', '<', $products['id'])->max('id');

            // get next user id
            $this->next = Product::where('id', '>', $products['id'])->min('id');

        }
    }

    public function render()
    {
        return view('pharmacy::livewire.master-data.products-edit');
    }

    public function preCreate()
    {
        if ($this->retail_price != $this->retail_price_old) {
            $this->retail_price_notification = true;
            $this->force_update = true;
        } else {
            $this->force_update = false;
            $this->create();
        }
    }

    public function create()
    {
        $this->validate();
        if (Product::where('id', '!=', $this->primary_id)->where('name', $this->name)->exists()) {
            $this->addError('name', 'This name is already exists.');
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
            'max_discount' => $this->max_disc ?: null,
            'discountable' => $this->discount_check,
            'rack_id' => $this->rack_id,
            'manufacture_id' => $this->manufacture_id,
            'category_id' => $this->category_id,
            'reorder_level' => $this->reorder_level,
            'reorder_qty' => $this->reorder_qty,
            'narcotics' => !empty($this->narcotics) ? 't' : 'f',
            'status' => $this->status,
            'control_medicine' => $this->control_medicine,
            'type' => $this->type,
            'temperature'=>$this->temperature??null,
        ]);
        if ($this->force_update) {
            $this->retail_price_old = $this->retail_price;
            if($this->packing>0){
                ProductInventory::where('product_id', $pro->id)->where('qty','>',0)->where('supply_price','<',$this->retail_price)->update([
                    'retail_price' => $this->retail_price / $this->packing
                ]);
            }

        }
        $this->success = 'Record has been updated.';
        $this->reset(['retail_price_notification', 'force_update']);

    }

    public function forceCreate()
    {
        $this->force_update = true;
        $this->create();
    }

}
