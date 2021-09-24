<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Livewire\Component;

class PaymentList extends Component
{
    use Searchable;
    public $confirm_dialog=false;
    public $success;
    public function render(){
        return view('pharmacy::livewire.payments.customer.payment-list');
    }
}