<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Livewire\Component;

class Manufacture extends Component
{
    public $add = [
        'name' => '',
        'contact' => '',
        'status' => 't',
        'address' => ''
    ];

    public $edit = [
        'id' => '',
        'name' => '',
        'contact' => '',
        'status' => 't',
        'address' => ''
    ];
    public $edit_modal =  false;
    public $success;
    public $error;

    protected $rules = [
        'add.name' => 'required|string|unique:manufactures,name',
        'add.contact' => 'nullable|string',
        'add.status' => 'required|in:t,f',
        'add.address' => 'nullable|string'
    ];


    protected $validationAttributes = [
        'add.name' => 'name',
        'add.contact' => 'contact',
        'add.status' => 'status',
        'add.address' => 'address',
    ];




    public function render()
    {
        $manufacture = \Devzone\Pharmacy\Models\Manufacture::orderBy('id','desc')->get();
        return view('pharmacy::livewire.master-data.manufacture',['manufacture'=>$manufacture]);
    }

    public function create()
    {
        $this->validate();

        \Devzone\Pharmacy\Models\Manufacture::create([
            'name' => $this->add['name'],
            'contact' => !empty($this->add['contact']) ? $this->add['contact'] : null,
            'status' => $this->add['status'],
            'address' => !empty($this->add['address']) ? $this->add['address'] : null
        ]);

        $this->success = 'Record has been added.';
        $this->reset(['add']);
    }

    public function openEditModel($id)
    {

        $this->edit_modal = true;
        $this->edit = \Devzone\Pharmacy\Models\Manufacture::find($id)->toArray();
    }


    public function updateManufacture(){

        if(empty($this->edit['name'])){
            $this->error = 'The name field is required';
            return;
        }



        if(\Devzone\Pharmacy\Models\Manufacture::where('name',$this->edit['name'])
            ->where('id','!=',$this->edit['id'])->exists()){
            $this->error = 'This name is already exists.';
            return;
        }


        \Devzone\Pharmacy\Models\Manufacture::find($this->edit['id'])->update([
            'name' => $this->edit['name'],
            'contact' => !empty($this->edit['contact']) ? $this->edit['contact'] : null,
            'status' => $this->edit['status'],
            'address' => !empty($this->edit['address']) ? $this->edit['address'] : null
        ]);

        $this->success = 'Record has been updated.';
        $this->reset(['add','edit','edit_modal','error']);

    }
}
