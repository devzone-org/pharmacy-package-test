<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Supplier;
use Livewire\Component;

class SupplierProductsList extends Component
{
    public $success = '';
    public $name;
    public $salt;
    public $manufacturer_id;
    public $supplier_id;
    public $category_id;
    public $show_data;

    public $manufacturers;
    public $suppliers;
    public $categories;
    public $all_products;

    public function mount()
    {
        $this->suppliers = Supplier::where('status', 't')->get();
        $this->manufacturers = \Devzone\Pharmacy\Models\Manufacture::where('status', 't')->get();
        $this->categories = \Devzone\Pharmacy\Models\Category::where('status', 't')->get();
    }

//->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
//->leftJoin('suppliers as sup', 'sup.id', '=', 'p.supplier_id')
//->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
//->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
//->select('p.*', 'm.name as m_name', 'sup.name as sup_name', 'c.name as c_name', 'r.name as r_name', 'r.tier')
    
    public function search(){
        $this->all_products = Product::from('products as p')
            ->select('p.id','p.name', 'p.salt', 'p.packing', 'p.cost_of_price', 'p.retail_price', 'p.manufacture_id', 'p.supplier_id', 'p.category_id', 'p.narcotics', 'p.status')
            ->when(!empty($this->name),function($q){
                return $q->where('p.name','LIKE','%'.$this->name.'%');
            })
            ->when(!empty($this->salt),function($q){
                return $q->where('p.salt','LIKE','%'.$this->salt.'%');
            })
            ->when(!empty($this->supplier_id),function($q){
                return $q->where('p.supplier_id', $this->supplier_id);
            })
            ->when(!empty($this->manufacturer_id),function($q){
                return $q->where('p.manufacture_id', $this->manufacturer_id);
            })
            ->when(!empty($this->category_id),function($q){
                return $q->where('p.category_id', $this->category_id);
            })
            ->when(!empty($this->show_data),function($q){
                return $q->where('p.narcotics', $this->show_data);
            })
            ->limit(100)
            ->get()
            ->toArray();

    }

    public function resetSearch()
    {
        $this->reset(['name','salt', 'supplier_id', 'manufacturer_id']);
    }

    public function verifyProduct($key)
    {
        if (!empty($this->all_products[$key]['manufacture_id']) && !empty($this->all_products[$key]['supplier_id']) && !empty($this->all_products[$key]['category_id'])){
            $this->all_products[$key]['narcotics'] = 't';
            $set = Product::find($this->all_products[$key]['id'])->update(['narcotics' => 't']);
        }
    }

    public function updatedAllProducts($value, $name)
    {

        $tmp = array_combine(['p_index', 'target_id'], explode('.', $name));
        if ($tmp['target_id'] == 'supplier_id'){
            Product::find($this->all_products[$tmp['p_index']]['id'])->update(['supplier_id'=> $value, 'narcotics'=> 'f']);
            $this->all_products[$tmp['p_index']]['narcotics'] = 'f';
        }
        if ($tmp['target_id'] == 'manufacture_id'){
            Product::find($this->all_products[$tmp['p_index']]['id'])->update(['manufacture_id'=> $value, 'narcotics'=> 'f']);
            $this->all_products[$tmp['p_index']]['narcotics'] = 'f';
        }
        if ($tmp['target_id'] == 'category_id'){
            Product::find($this->all_products[$tmp['p_index']]['id'])->update(['category_id'=> $value, 'narcotics'=> 'f']);
            $this->all_products[$tmp['p_index']]['narcotics'] = 'f';
        }

    }

    public function render()
    {
        return view('pharmacy::livewire.master-data.supplier-products-list');
    }
}