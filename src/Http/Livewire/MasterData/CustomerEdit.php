<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Customer;
use Livewire\Component;

class CustomerEdit extends Component
{
    public $customer_id;
    public $name;
    public $phone;
    public $email;
    public $company;
    public $credit_limit;
    public $address;
    public $status;
    public $success;

    protected $rules = [
        'name' => 'required',
        'phone' => 'required|regex:/^(\+92)(3)([0-9]{9})$/',
        'email' => 'nullable|email',
        'credit_limit' => 'required',
    ];

    public function mount($id)
    {
        $this->customer_id = $id;
        $customer = Customer::where('id', $this->customer_id)->first()->toArray();
        $this->name = $customer['name'];
        $this->phone = $customer['phone'];
        $this->email = $customer['email'];
        $this->company = $customer['company'];
        $this->credit_limit = (int)$customer['credit_limit'];
        $this->address = $customer['address'];
        $this->status = $customer['status'];
    }

    public function render()
    {
        return view('pharmacy::livewire.master-data.customer-edit');
    }

    public function create()
    {
        $this->reset('success');
        $this->resetErrorBag();
        $this->validate();
        Customer::where('id', $this->customer_id)->update([
            'name' => ucwords($this->name),
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'company' => $this->company,
            'credit_limit' => $this->credit_limit,
            'status' => $this->status
        ]);
        $this->success = 'Customer updated successfully';
    }
}