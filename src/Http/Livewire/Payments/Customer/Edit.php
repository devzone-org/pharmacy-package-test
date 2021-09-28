<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\Payments\CustomerPayment;
use Devzone\Pharmacy\Models\Payments\CustomerPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentRefundDetail;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public $payment_id;
    public $customer_id;
    public $customer_name;
    public $payments = [];
    public $description;
    public $receiving_date;
    public $closing_balance;
    public $selected_payment = [];

    protected $validationAttributes = [
        'customer_id' => 'Customer'
    ];

    public function mount($id)
    {
        $this->payment_id = $id;
        $payment = CustomerPayment::find($this->payment_id);
        $payment_details = CustomerPaymentDetail::where('customer_payment_id', $this->payment_id)->get();
        $customer = Customer::find($payment->customer_id);

        $this->customer_id = $customer->id;
        $this->customer_name = $customer->name;
        $this->selected_payment = $payment_details->pluck('sale_id')->toArray();
        $this->description = $payment->description;
        $this->receiving_date = $payment->receiving_date;
        $this->emitCustomerId();
    }

    public function render()
    {
        return view('pharmacy::livewire.payments.customer.edit');
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
            $payments = $payments->toArray();
            foreach ($payments as $key => $p) {
                if (in_array($payments[$key]['id'], $this->selected_payment)) {
                    $payments[$key]['selected'] = true;
                } else {
                    $payments[$key]['selected'] = false;
                }

            }
            $this->payments = $payments;
        } else {
            $this->addError('error', 'No pending payment found');
        }
        $customer_balance = Customer::from('customers as c')
            ->join('ledgers as l', function ($q) {
                return $q->on('l.account_id', '=', 'c.account_id')->where('l.is_approve', 't');
            })->where('c.id', $this->customer_id)
            ->select(DB::raw('sum(l.debit - l.credit) as closing'))->first();
        $this->closing_balance = !empty($customer_balance) ? $customer_balance->closing : 0;
    }

    public function create()
    {
        $this->validate([
            'payments' => 'required',
            'customer_id' => 'required',
            'receiving_date' => 'required',
            'description' => 'required'
        ]);
        $is_selected = collect($this->payments)->where('selected', true)->first();
        if (empty($is_selected)) {
            $this->addError('error', 'Please select atleast one receipt');
            return;
        }
        try {
            DB::beginTransaction();
            if (CustomerPayment::whereNotNull('approved_by')->where('id', $this->payment_id)->exists()) {
                throw new \Exception('This payment is already approved so unable to edit.');
            }
            CustomerPayment::find($this->payment_id)->update([
                'description' => $this->description,
                'receiving_date' => $this->receiving_date,
            ]);
            CustomerPaymentDetail::where('customer_payment_id', $this->payment_id)->delete();
            foreach (collect($this->payments)->where('selected',true) as $p){
                CustomerPaymentDetail::create([
                    'customer_payment_id'=>$this->payment_id,
                    'sale_id'=>$p['id']
                ]);
            }
            DB::commit();
            $this->success = 'Record has been updated.';

        } catch (\Exception $e) {
            $this->addError('purchase_orders', $e->getMessage());
            DB::rollBack();
        }
    }
}