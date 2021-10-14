<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Customer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $name;
    public $phone;
    public $status;
    public $company;
    public $employee_id;
    public $employees = [];

    public function mount(){
        $this->employees = DB::table('employees')->get()->toArray();
        $this->employees = (json_decode(json_encode($this->employees), true));
    }
    public function render()
    {
        $customers = Customer::from('customers as c')
            ->join('employees as e','e.id','=','c.employee_id')
            ->when(!empty($this->name), function ($q) {
                return $q->where('c.name','like','%'.$this->name.'%');
            })
            ->when(!empty($this->employee_id), function ($q) {
                return $q->where('c.employee_id',$this->employee_id);
            })
            ->when(!empty($this->status), function ($q) {
                return $q->where('c.status',$this->status);
            })
            ->select('c.*','e.name as care_of')
            ->orderBy('c.id','desc')
            ->paginate(100);
        return view('pharmacy::livewire.master-data.customer-list', ['customers' => $customers]);
    }

    public function search()
    {

    }

    public function resetSearch()
    {
        $this->reset('name',   'status','employee_id');
    }
}