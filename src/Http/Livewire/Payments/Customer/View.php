<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Models\Payments\CustomerPayment;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class View extends Component
{
    public $payment_id;
    public $payments = [];
    public $customer_name = '';
    public $description;
    public $receiving_date;
    public $customer_payment = [];


    public function mount($id)
    {
        $this->payment_id = $id;
        $payments = Sale::from('sales as s')
            ->join('customer_payment_details as cpd', 'cpd.sale_id', '=', 's.id')
            ->where('cpd.customer_payment_id', $id)
            ->select('s.*')
            ->get();

        $sale_ids = $payments->where('is_refund', 't')->pluck('id')->toArray();
//
        $refund_entries = \Devzone\Pharmacy\Models\Sale\SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->whereIn('sr.sale_id', $sale_ids)
            ->groupBy('sr.sale_id')
            ->select(\Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as total_refunded'),
                'sr.sale_id as refunded_id', 'sr.refunded_id as id')
            ->get();


        $payments = $payments->toArray();
        foreach ($payments as $key => $p) {
            $payments[$key]['selected'] = true;
            $refunded = $refund_entries->where('refunded_id', $p['id'])->first();
            $payments[$key]['refunded'] = !empty($refunded) ? $refunded->total_refunded : 0;
        }
        $this->payments = $payments;


        $this->customer_payment = CustomerPayment::from('customer_payments as cp')
            ->join('customers as c', 'c.id', '=', 'cp.customer_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'cp.receiving_in')
            ->join('users as a', 'a.id', '=', 'cp.added_by')
            ->leftJoin('users as ap', 'ap.id', '=', 'cp.approved_by')
            ->where('cp.id',$id)
            ->select('cp.*','cp.amount as total_receive', 'c.name as customer', 'coa.name as account_name', 'a.name as added_by_name', 'ap.name as approved_by_name')
            ->first()->toArray();

    }

    public function render()
    {
        return view('pharmacy::livewire.payments.customer.view');
    }
}