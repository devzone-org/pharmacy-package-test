<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Livewire\Component;

class Add extends Component
{
    use Searchable;
    public $customer_id;
    public $customer_name;

    protected $listeners=['emitCustomerId'];
    public function render(){
        return view('pharmacy::livewire.payments.customer.add');
    }
    public function emitCustomerId(){

    }
}