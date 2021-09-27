<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Models\Payments\CustomerPayment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class View extends Component
{
    public $payment_id;
    public $payments=[];
    public function mount($id){
        $this->payment_id=$id;
        $this->payments = CustomerPayment::from('customer_payments as cp')
            ->join('customers as c', 'c.id', 'cp.customer_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'cp.receiving_in')
            ->join('customer_payment_details as cpd', 'cpd.customer_payment_id', '=', 'cp.id')
            ->join('sales as s', 's.id', '=', 'cpd.sale_id')
            ->join('users as us', 'us.id', '=', 'cp.added_by')
            ->leftJoin('users as a', 'a.id', '=', 'cp.approved_by')
            ->where('cp.id',$this->payment_id)
            ->select(
                'c.name as customer_name','coa.name as account_name','us.name as added_by','a.name as approved_by',
                'cp.id', 'cp.description',  'cp.receiving_date',
                DB::raw('(s.gross_total - s.receive_amount) as total_receivable'),
                'cp.created_at', 'cp.approved_at', 'cpd.sale_id')
            ->get()->toArray();
    }
    public function render(){
        return view('pharmacy::livewire.payments.customer.view');
    }
}