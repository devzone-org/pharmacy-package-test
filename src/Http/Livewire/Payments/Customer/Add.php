<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Payments\CustomerPayment;
use Devzone\Pharmacy\Models\Payments\CustomerPaymentDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Add extends Component
{
    use Searchable;

    public $customer_id;
    public $customer_name;
    public $payments = [];
    public $description;
    public $receiving_date;
    public $closing_balance;

    protected $listeners = ['emitCustomerId'];

    protected $validationAttributes = [
        'customer_id' => 'Customer'
    ];

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
            $payments = $payments->toArray();
            foreach ($payments as $key => $p) {
                $payments[$key]['selected'] = false;
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
            if (empty(Auth::user()->account_id)) {
                throw new \Exception('Cash in Hand - ' . Auth::user()->name . ' account not found.');
            }
            $id=CustomerPayment::create([
                'customer_id' => $this->customer_id,
                'description'=>$this->description,
                'receiving_in'=>Auth::user()->account_id,
                'receiving_date'=>$this->receiving_date,
                'added_by'=>Auth::id(),
            ])->id;
            foreach (collect($this->payments)->where('selected',true) as $p){
                CustomerPaymentDetail::create([
                    'customer_payment_id'=>$id,
                    'sale_id'=>$p['id']
                ]);
            }
            DB::commit();
            $this->success = 'Record has been added and waiting for approval.';
            $this->reset(['customer_id', 'customer_name', 'payments', 'receiving_date','description','closing_balance']);

        } catch (\Exception $e) {
            $this->addError('Exception', $e->getMessage());
            DB::rollBack();
        }
    }
}