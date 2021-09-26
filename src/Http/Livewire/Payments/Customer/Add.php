<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\Sale;
use Livewire\Component;

class Add extends Component
{
    use Searchable;

    public $customer_id;
    public $customer_name;
    public $payments = [];

    protected $listeners = ['emitCustomerId'];

    public function render()
    {
        return view('pharmacy::livewire.payments.customer.add');
    }

    public function emitCustomerId()
    {
        $payments = Sale::from('sales as s')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->leftJoin('employees as e', function ($q) {
                return $q->on('e.id', '=', 's.referred_by')
                    ->where('e.is_doctor', 't');
            })
            ->where('s.customer_id', $this->customer_id)
            ->whereIn('s.is_paid', ['f', 'p'])
            ->select('s.*', 'p.name as patient', 'e.name as referred')
            ->get();
        if ($payments->isNotEmpty()) {
            $this->payments = $payments->toArray();
        } else {
            $this->addError('error', 'No pending payment found');
        }
    }
    public function create(){
        dd($this->payments);
    }
}