<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Supplier;

use Devzone\Ams\Helper\Voucher;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentRefundDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Add extends Component
{
    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $pay_from_name;
    public $closing_balance;
    public $pay_from;
    public $payment_date;
    public $description;
    public $success;
    public $purchase_orders = [];
    public $selected_orders = [];
    public $selected_returns = [];
    public $returns = [];
    protected $listeners = ['emitSupplierId'];

    protected $rules = [
        'supplier_id' => 'required|integer',
        'payment_date' => 'required|date',
        'description' => 'nullable|string'
    ];

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
    }


    public function render()
    {

        return view('pharmacy::livewire.payments.supplier.add');
    }

    public function create()
    {
        $this->validate();
        if (!empty($this->selected_orders) || !empty($this->selected_returns)) {
            try {

                DB::beginTransaction();
                if (empty(Auth::user()->account_id)) {
                    throw new \Exception('Cash in Hand - ' . Auth::user()->name . ' account not found.');
                }
//                $supplier_receipt_no=Voucher::instance()->advances()->get();
                $id = SupplierPayment::create([
                    'supplier_id' => $this->supplier_id,
                    'description' => $this->description,
                    'pay_from' => Auth::user()->account_id,
                    'payment_date' => $this->payment_date,
                    'added_by' => Auth::user()->id,
                ])->id;

                foreach ($this->selected_orders as $o) {
                    SupplierPaymentDetail::create([
                        'supplier_payment_id' => $id,
                        'order_id' => $o
                    ]);
                }
                foreach ($this->selected_returns as $o) {
                    SupplierPaymentRefundDetail::create([
                        'supplier_payment_id' => $id,
                        'refund_id' => $o
                    ]);
                }
                DB::commit();
                $this->success = 'Record has been added and need for approval.';
                $this->reset(['supplier_id', 'selected_returns', 'supplier_name', 'purchase_orders', 'selected_orders', 'returns', 'description', 'pay_from', 'pay_from_name']);

            } catch (\Exception $e) {
                $this->addError('purchase_orders', $e->getMessage());
                DB::rollBack();
            }
        } else {
            $this->addError('purchase_orders', 'You have to select at least one order.');
        }


    }

    public function emitSupplierId()
    {
        $supplier = Supplier::from('suppliers as s')
            ->join('ledgers as l', function ($q) {
                return $q->on('l.account_id', '=', 's.account_id')->where('l.is_approve', 't');
            })->where('s.id', $this->supplier_id)
            ->select(DB::raw('sum(l.credit - l.debit) as closing'))->first();
        $this->closing_balance = !empty($supplier) ? $supplier->closing : 0;
        $result = Purchase::from('purchases as p')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->where('p.supplier_id', $this->supplier_id)
            ->where('p.status', 'received')
            ->where('p.is_paid', 'f')
            ->select('p.advance_tax',
                'p.id', 'p.supplier_invoice', 'p.grn_no', 'p.delivery_date', DB::raw('sum(total_cost) as total_cost')
            )->orderBy('p.delivery_date')->groupBy('pr.purchase_id')->get();
        if ($result->isEmpty()) {
            $this->purchase_orders = [];
        } else {

            foreach ($result->toArray() as $r) {
                $tax_amount=0;
                if(!empty($r['advance_tax'])){
                    $tax_amount = $r['total_cost'] * ($r['advance_tax'] / 100);
                }
                $r['tax_amount'] = $tax_amount;
                $this->purchase_orders[] = $r;
            }
        }

        $returns = SupplierRefund::from('supplier_refunds as sr')
            ->where('sr.supplier_id', $this->supplier_id)
            ->where('sr.is_receive', 'f')
            ->whereNotNull('sr.approved_at')
            ->select('sr.description', 'sr.id', 'sr.total_amount as total')
            ->get();
        if ($returns->isEmpty()) {
            $this->returns = [];
        } else {
            $this->returns = $returns->toArray();
        }
    }
}
