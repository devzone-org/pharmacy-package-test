<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Customer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Devzone\Ams\Helper\ChartOfAccount;

class CustomerAdd extends Component
{
    public $name;
    public $phone;
    public $email;
    public $company;
    public $credit_limit;
    public $address;
    public $success;

    protected $rules=[
        'name'=>'required',
        'phone'=>'required|regex:/^(\+92)(3)([0-9]{9})$/',
        'email'=>'nullable|email',
        'credit_limit'=>'required',
    ];
    public function render(){
        die;
        return view('pharmacy::livewire.master-data.customer-add');
    }
    public function create(){
        $this->reset('success');
        $this->resetErrorBag();
        $this->validate();
        DB::beginTransaction();
        try {
            $account_id = ChartOfAccount::instance()->createCustomerAccount('Receivable '.ucwords($this->name));
            Customer::create([
                'name'=>ucwords($this->name),
                'phone'=>$this->phone,
                'email'=>$this->email,
                'address'=>$this->address,
                'company'=>$this->company,
                'credit_limit'=>$this->credit_limit,
                'account_id'=>$account_id
            ]);
            DB::commit();
            $this->success='Customer created successfully';
            $this->reset('name','phone','email','address','company','credit_limit');
        }catch (\Exception $e){
            $this->addError('Exception',$e->getMessage());
            DB::rollBack();
        }

    }
}