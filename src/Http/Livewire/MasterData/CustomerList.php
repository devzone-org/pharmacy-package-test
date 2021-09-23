<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $name;
    public $phone;
    public $status;
    public $company;

    public function render()
    {
        $customers = Customer::when(!empty($this->name), function ($q) {
                return $q->where('name','like','%'.$this->name.'%');
            })
            ->when(!empty($this->phone), function ($q) {
                return $q->where('phone','like','%'.$this->phone.'%');
            })
            ->when(!empty($this->company), function ($q) {
                return $q->where('company','like','%'.$this->company.'%');
            })
            ->when(!empty($this->status), function ($q) {
                return $q->where('status',$this->status);
            })
            ->get();
        return view('pharmacy::livewire.master-data.customer-list', ['customers' => $customers]);
    }

    public function search()
    {

    }

    public function resetSearch()
    {
        $this->reset('name', 'phone', 'company', 'status');
    }
}