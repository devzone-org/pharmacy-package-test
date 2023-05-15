<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Supplier;

use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentRefundDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $pay_from_name;
    public $pay_from;
    public $payment_date;
    public $closing_balance;
    public $description;
    public $success;
    public $purchase_orders = [];
    public $selected_orders = [];
    public $payment_id;
    public $selected_returns = [];
    public $returns = [];

    protected $listeners = ['emitSupplierId'];
    protected $rules = [
        'supplier_id' => 'required|integer',

        'payment_date' => 'required|date',
        'description' => 'nullable|string'
    ];

    public function mount($payment_id)
    {
        $this->payment_id = $payment_id;
        $payment = SupplierPayment::find($payment_id);
        $payment_details = SupplierPaymentDetail::where('supplier_payment_id', $payment_id)->get();
        $refund = SupplierPaymentRefundDetail::where('supplier_payment_id', $payment_id)->get();
        $supplier = Supplier::find($payment->supplier_id);

        $this->supplier_id = $supplier->id;
        $this->supplier_name = $supplier->name;

        $this->description = $payment->description;
        $this->payment_date = $payment->payment_date;
        $this->emitSupplierId();
        $this->selected_orders = ($payment_details->pluck('order_id')->toArray());
        $this->selected_returns = ($refund->pluck('refund_id')->toArray());
        foreach ($this->selected_returns as $key => $a) {
            $this->selected_returns[$key] = (string)$a;
        }
        foreach ($this->selected_orders as $key => $a) {
            $this->selected_orders[$key] = (string)$a;
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
            ->select(
                'p.id', 'p.supplier_invoice', 'p.grn_no', 'p.delivery_date', DB::raw('sum(total_cost) as total_cost')
            )->orderBy('p.delivery_date')->groupBy('pr.purchase_id')->get();
        if ($result->isEmpty()) {
            $this->purchase_orders = [];
        } else {
            $this->purchase_orders = $result->toArray();
        }

        $returns = SupplierRefund::from('supplier_refunds as sr')
            ->where('sr.supplier_id', $this->supplier_id)
            ->where('sr.is_receive', 'f')
            ->whereNotNull('sr.created_by')
            ->select('sr.description', 'sr.id', 'sr.total_amount as total')
            ->get();
        if ($returns->isEmpty()) {
            $this->returns = [];
        } else {
            $this->returns = $returns->toArray();
        }
    }

    public function render()
    {
        return view('pharmacy::livewire.payments.supplier.edit');
    }

    public function create()
    {
        $this->validate();
        $lock = Cache::lock('supplier.payment.edit.' . $this->payment_id, 30);
        if (!empty($this->selected_orders) || !empty($this->selected_returns)) {
            try {
                if ($lock->get()) {
                    DB::beginTransaction();
                    if (SupplierPayment::whereNotNull('approved_by')->where('id', $this->payment_id)->exists()) {
                        throw new \Exception('This payment is already approved so unable to edit.');
                    }
                    SupplierPayment::find($this->payment_id)->update([
                        'supplier_id' => $this->supplier_id,
                        'description' => $this->description,
                        'payment_date' => $this->payment_date,
                    ]);
                    SupplierPaymentDetail::where('supplier_payment_id', $this->payment_id)->delete();
                    SupplierPaymentRefundDetail::where('supplier_payment_id', $this->payment_id)->delete();
                    foreach ($this->selected_orders as $o) {
                        SupplierPaymentDetail::create([
                            'supplier_payment_id' => $this->payment_id,
                            'order_id' => $o
                        ]);
                    }
                    foreach ($this->selected_returns as $o) {
                        SupplierPaymentRefundDetail::create([
                            'supplier_payment_id' => $this->payment_id,
                            'refund_id' => $o
                        ]);
                    }
                    DB::commit();
                    $this->success = 'Record has been updated.';
                }
                optional($lock)->release();
            } catch (\Exception $e) {
                $this->addError('purchase_orders', $e->getMessage());
                DB::rollBack();
                optional($lock)->release();
            }
        } else {
            $this->addError('purchase_orders', 'You have to select at least one order.');
        }


    }
}
