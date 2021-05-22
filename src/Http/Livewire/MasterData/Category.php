<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Livewire\Component;

class Category extends Component
{
    public $name = '';
    public $status = 't';
    public $ename = '';
    public $estatus = '';
    public $primary_id;


    public $edit_modal = false;
    public $success;
    public $error;

    protected $rules = [
        'name' => 'required|string|unique:categories,name',
        'status' => 'required|in:t,f'
    ];


    public function render()
    {
        $categories = \Devzone\Pharmacy\Models\Category::orderBy('id', 'desc')->get();
        return view('pharmacy::livewire.master-data.category', ['categories' => $categories]);
    }

    public function create()
    {
        $this->validate();

        \Devzone\Pharmacy\Models\Category::create([
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $this->success = 'Record has been added.';
        $this->reset(['name', 'status']);
    }

    public function openEditModel($id)
    {

        $this->edit_modal = true;
        $category = \Devzone\Pharmacy\Models\Category::find($id)->toArray();
        $this->ename = $category['name'];
        $this->estatus = $category['status'];
        $this->primary_id = $category['id'];
    }


    public function updateManufacture()
    {

        if (empty($this->ename)) {
            $this->error = 'The name field is required';
            return;
        }

        if(\Devzone\Pharmacy\Models\Category::where('name',$this->ename)
                    ->where('id','!=',$this->primary_id)->exists()){
            $this->error = 'This name is already exists.';
            return;
        }

        \Devzone\Pharmacy\Models\Category::find($this->primary_id)->update([
            'name' => $this->ename,
            'status' => $this->estatus
        ]);

        $this->success = 'Record has been updated.';
        $this->reset(['estatus', 'ename', 'edit_modal','error']);

    }
}
