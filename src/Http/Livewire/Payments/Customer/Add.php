<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Payments\CustomerPayment;
use Devzone\Pharmacy\Models\Payments\CustomerPaymentDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function emitCustomerId()
    {
        $payments = Sale::from('sales as s')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')

            ->where('s.customer_id', $this->customer_id)
            ->whereNull('s.refunded_id')
            ->whereIn('s.is_paid', ['f'])
            ->select('s.*', 'p.name as patient')
            ->get();

        $sale_ids=$payments->where('is_refund','t')->pluck('id')->toArray();
//
        $refund_entries = \Devzone\Pharmacy\Models\Sale\SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd','sd.id','=','sr.sale_detail_id')
            ->whereIn('sr.sale_id',$sale_ids)
            ->groupBy('sr.sale_id')
            ->select(\Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as total_refunded'),
                'sr.sale_id as refunded_id','sr.refunded_id as id')
            ->get();


        if ($payments->isNotEmpty()) {
            $payments = $payments->toArray();
            foreach ($payments as $key => $p) {
                $payments[$key]['selected'] = false;
                $refunded=$refund_entries->where('refunded_id',$p['id'])->first();
                $payments[$key]['refunded'] =!empty($refunded) ? $refunded->total_refunded : 0;
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
        $lock=Cache::lock('customer.payment.add',30);
        try {
            if ($lock->get()) {
                DB::beginTransaction();
                if (empty(Auth::user()->account_id)) {
                    throw new \Exception('Cash in Hand - ' . Auth::user()->name . ' account not found.');
                }
                $id = CustomerPayment::create([
                    'customer_id' => $this->customer_id,
                    'description' => $this->description,
                    'receiving_in' => Auth::user()->account_id,
                    'receiving_date' => $this->formatDate($this->receiving_date),
                    'added_by' => Auth::id(),
                    'amount' => collect($this->payments)->where('selected', true)->sum('on_account') - collect($this->payments)->where('selected', true)->sum('refunded'),
                ])->id;
                foreach (collect($this->payments)->where('selected', true) as $p) {
                    CustomerPaymentDetail::create([
                        'customer_payment_id' => $id,
                        'sale_id' => $p['id']
                    ]);
                }
                DB::commit();
                $this->success = 'Record has been added and waiting for approval.';
                $this->reset(['customer_id', 'customer_name', 'payments', 'receiving_date', 'description', 'closing_balance']);
            }
            optional($lock)->release();
        } catch (\Exception $e) {
            $this->addError('Exception', $e->getMessage());
            DB::rollBack();
            optional($lock)->release();
        }
    }
}