<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Supplier;
use Livewire\Component;

class SupplierEdit extends Component
{


    public $name;
    public $phone;
    public $address;
    public $contact_name;
    public $contact_phone;

    public $status = 't';
    public $success = '';
    public $primary_id;


    protected $rules = [
        'name' => 'required|string',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'contact_name' => 'nullable|string',
        'contact_phone' => 'nullable|string',
        'status' => 'required|in:t,f'

    ];

    public function mount($primary_id)
    {
        $this->primary_id = $primary_id;
        $supplier = Supplier::find($primary_id);


        if (empty($supplier)) {
            return redirect()->to('/pharmacy/master-data/suppliers');
        } else {

            $this->name = $supplier['name'];
            $this->phone = $supplier['phone'];
            $this->address = $supplier['address'];
            $this->contact_name = $supplier['contact_name'];
            $this->contact_phone = $supplier['contact_phone'];
            $this->status = $supplier['status'];

        }
    }

    public function render()
    {
        return view('pharmacy::livewire.master-data.supplier-edit');
    }

    public function create()
    {
        $this->validate();
        Supplier::find($this->primary_id)->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'status' => $this->status
        ]);

        $this->success = 'Record has been updated.';

    }


}
