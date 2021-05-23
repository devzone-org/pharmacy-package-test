<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Rack;
use Livewire\Component;

class Racks extends Component
{
    public $add = [

        'name' => '',
        'status' => 't',
        'tier' => ''
    ];

    public $edit = [
        'id' => '',

        'name' => '',
        'status' => 't',
        'tier' => ''
    ];
    public $edit_modal = false;
    public $success;
    public $error;

    protected $rules = [
        'add.name' => 'required|string',
        'add.status' => 'required|in:t,f',
        'add.tier' => 'nullable|string',
    ];


    protected $validationAttributes = [
        'add.name' => 'name',
        'add.tier' => 'tier',
        'add.status' => 'status',

    ];


    public function render()
    {
        $racks = Rack::orderBy('id', 'desc')->get();
        return view('pharmacy::livewire.master-data.racks', ['racks' => $racks]);
    }

    public function create()
    {
        $this->validate();

        if (Rack::where('name', $this->add['name'])->where('tier', $this->add['tier'])
            ->exists()) {
            $this->addError('name', 'The name already exists against tier.');
            return;
        }
        Rack::create([
            'name' => $this->add['name'],

            'status' => $this->add['status'],
            'tier' => !empty($this->add['tier']) ? $this->add['tier'] : null
        ]);

        $this->success = 'Record has been added.';
        $this->reset(['add', 'error']);
    }

    public function openEditModel($id)
    {
        $this->reset(['success','error']);
        $this->edit_modal = true;
        $this->edit = Rack::find($id)->toArray();
    }


    public function updateManufacture()
    {

        if (empty($this->edit['name'])) {
            $this->error = 'The name field is required';
            return;
        }


        if (Rack::where('name', $this->edit['name'])->where('tier', $this->edit['tier'])
            ->where('id', '!=', $this->edit['id'])->exists()) {
            $this->error = 'The name already exists against tier.';
            return;
        }


        Rack::find($this->edit['id'])->update([
            'name' => $this->edit['name'],

            'status' => $this->edit['status'],
            'tier' => !empty($this->edit['tier']) ? $this->edit['tier'] : null
        ]);

        $this->success = 'Record has been updated.';
        $this->reset(['add', 'edit', 'edit_modal', 'error']);

    }
}
