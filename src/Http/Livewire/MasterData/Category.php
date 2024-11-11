<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Product;
use Livewire\Component;

class Category extends Component
{
    public $name = '';
    public $status = 't';
    public $discountable = 'f';
    public $max_disc;

    public $ename = '';
    public $estatus = '';
    public $ediscountable = 'f';
    public $emax_disc;
    public $primary_id;


    public $edit_modal = false;
    public $success;
    public $error;

    protected $rules = [
        'name' => 'required|string|unique:categories,name',
        'status' => 'required|in:t,f',
//        'discountable'=> 'required|in:t,f',
        'max_disc'=> 'required_if:discountable,t|numeric|between:1,100',
    ];

    protected $validationAttributes = [
        'name'=>'Name',
        'status'=>'Status',
        'max_disc'=>'Max Discount'
    ];


    public function render()
    {
        $categories = \Devzone\Pharmacy\Models\Category::orderBy('id', 'desc')->get();
        return view('pharmacy::livewire.master-data.category', ['categories' => $categories]);
    }

    public function create()
    {
        $this->validate([
            'name' => 'required|string|unique:categories,name',
            'status' => 'required|in:t,f',
            'max_disc' => $this->discountable === 't' ? 'required_if:discountable,t|numeric|between:1,100' : 'nullable',
        ]);

        \Devzone\Pharmacy\Models\Category::create([
            'name' => $this->name,
            'status' => $this->status,
            'discountable' => $this->discountable,
            'max_discount' => $this->max_disc?:null,
        ]);

        $this->success = 'Record has been added.';
        $this->reset(['name', 'status', 'discountable', 'max_disc']);
    }

    public function openEditModel($id)
    {
        $this->error = null;
        $this->edit_modal = true;
        $category = \Devzone\Pharmacy\Models\Category::find($id)->toArray();
        $this->ename = $category['name'];
        $this->estatus = $category['status'];
        $this->ediscountable = $category['discountable']?:'f';
        $this->emax_disc = $category['max_discount']?:'0';
        $this->primary_id = $category['id'];
    }


    public function updateManufacture()
    {

        if (empty($this->ename)) {
            $this->error = 'The name field is required';
            return;
        }

        if ($this->ediscountable == 't' && (!is_numeric($this->emax_disc) || $this->emax_disc < 1 || $this->emax_disc > 100)){
            $this->error = 'Max Discount must be a numeric between 1 and 100.';
            return;
        }

        if(\Devzone\Pharmacy\Models\Category::where('name',$this->ename)
                    ->where('id','!=',$this->primary_id)->exists()){
            $this->error = 'This name already exists.';
            return;
        }
        try {
            Product::where('category_id', $this->primary_id)->update([
                'discountable'=>$this->ediscountable,
                'max_discount'=>$this->emax_disc?:null,
            ]);


            \Devzone\Pharmacy\Models\Category::find($this->primary_id)->update([
                'name' => $this->ename,
                'status' => $this->estatus,
                'discountable' => $this->ediscountable,
                'max_discount' => $this->emax_disc?:null,
            ]);

            $this->reset(['edit_modal', 'estatus', 'ename', 'ediscountable', 'emax_disc', 'error']);
            $this->success = 'Record has been updated.';
        } catch (\Exception $ex){
            $this->addError('exception', $ex->getMessage());
        }

    }
}
