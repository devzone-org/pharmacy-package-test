<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Ams\Helper\ChartOfAccount;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplierAdd extends Component
{


    public $name;
    public $phone;
    public $address;
    public $contact_name;
    public $contact_phone;
    public $opening_balance = 0;
    public $status = 't';
    public $success = '';


    protected $rules = [
        'name' => 'required|string',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'contact_name' => 'nullable|string',
        'contact_phone' => 'nullable|string',
        'status' => 'required|in:t,f',
        'opening_balance' => 'nullable|numeric'
    ];

    public function render()
    {
        return view('pharmacy::livewire.master-data.supplier-add');
    }

    public function create()
    {
        $this->validate();
        try {


            DB::beginTransaction();
            $account_id = ChartOfAccount::instance()->setOpeningBalance($this->opening_balance)->createSupplierAccount($this->name);
            Supplier::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
                'contact_name' => $this->contact_name,
                'contact_phone' => $this->contact_phone,
                'account_id' => $account_id,
                'status' => $this->status
            ]);

            $this->success = 'Record has been added.';
            DB::commit();
            $this->reset(['name', 'phone', 'address', 'contact_name', 'contact_phone', 'status']);

        } catch (\Exception $e) {

            $this->addError('name', $e->getMessage());
            DB::rollBack();
        }
    }


}
