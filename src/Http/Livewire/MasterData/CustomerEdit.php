<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Customer;
use Illuminate\Support\Facades\DB;
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

    public $employee_id;
    public $employees = [];


    protected $rules = [
        'name' => 'required',
        'credit_limit' => 'required',
        'employee_id' => 'required',
    ];

    public function mount($id)
    {
        $this->customer_id = $id;
        $customer = Customer::where('id', $this->customer_id)->first()->toArray();
        $this->name = $customer['name'];
        $this->employee_id = $customer['employee_id'];
        $this->credit_limit = (int)$customer['credit_limit'];
        $this->employees = DB::table('employees')->get()->toArray();
        $this->employees = (json_decode(json_encode($this->employees), true));
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
            'employee_id' => $this->employee_id,
            'credit_limit' => $this->credit_limit,
            'status' => $this->status
        ]);
        $this->success = 'Record updated successfully';
    }
}