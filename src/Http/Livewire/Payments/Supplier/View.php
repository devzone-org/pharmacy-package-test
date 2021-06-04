<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Supplier;

use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class View extends Component
{


    public $supplier_id;
    public $supplier_name;
    public $pay_from_name;
    public $pay_from;
    public $payment_date;
    public $description;
    public $success;
    public $purchase_orders = [];
    public $selected_orders = [];
    public $payment_id;
    protected $listeners = ['emitSupplierId'];
    protected $rules = [
        'supplier_id' => 'required|integer',
        'pay_from' => 'required|integer',
        'payment_date' => 'required|date',
        'description' => 'nullable|string'
    ];

    public function mount($payment_id)
    {
        $this->payment_id = $payment_id;
        $payment = SupplierPayment::find($payment_id);
        $payment_details = SupplierPaymentDetail::where('supplier_payment_id', $payment_id)->get();
        $supplier = Supplier::find($payment->supplier_id);
        $coa = ChartOfAccount::find($payment->pay_from);
        $this->supplier_id = $supplier->id;
        $this->supplier_name = $supplier->name;
        $this->pay_from = $coa->id;
        $this->pay_from_name = $coa->name;
        $this->description = $payment->description;
        $this->payment_date = $payment->payment_date;

        $this->selected_orders = ($payment_details->pluck('order_id')->toArray());
        $this->emitSupplierId();

    }

    public function emitSupplierId()
    {
        $result = Purchase::from('purchases as p')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->where('p.supplier_id', $this->supplier_id)
            ->where('p.status', 'received')
            ->whereIn('p.id',$this->selected_orders)
            ->where('p.is_paid', 't')
            ->select(
                'p.id', 'p.supplier_invoice', 'p.grn_no', 'p.delivery_date', DB::raw('sum(total_cost) as total_cost')
            )->orderBy('p.delivery_date')->groupBy('pr.purchase_id')->get();
        if ($result->isEmpty()) {
            $this->purchase_orders = [];
        } else {
            $this->purchase_orders = $result->toArray();
        }
    }

    public function render()
    {
        return view('pharmacy::livewire.payments.supplier.view');
    }


}