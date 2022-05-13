<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Supplier;

use App\Models\User;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentRefundDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
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

    public $selected_returns = [];
    public $returns = [];
    public $payment_details = [];
    public $payment_id;
    public $created_by;
    public $approved_by;
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
        $refund = SupplierPaymentRefundDetail::where('supplier_payment_id', $payment_id)->get();
        $supplier = Supplier::find($payment->supplier_id);
        $coa = ChartOfAccount::find($payment->pay_from);
        $this->supplier_id = $supplier->id;
        $this->supplier_name = $supplier->name;
        $this->pay_from = $coa->id;
        $this->pay_from_name = $coa->name;
        $this->description = $payment->description;
        $this->payment_date = $payment->payment_date;
        $this->payment_details = $payment->toArray();
        $this->created_by = User::find($payment['added_by']);
        if (!empty($payment['approved_by'])) {
            $this->approved_by = User::find($payment['approved_by']);
        }

        $this->selected_orders = ($payment_details->pluck('order_id')->toArray());
        $this->selected_orders = ($payment_details->pluck('order_id')->toArray());
        $this->selected_returns = ($refund->pluck('refund_id')->toArray());
        $this->emitSupplierId();




    }

    public function emitSupplierId()
    {
        $result = Purchase::from('purchases as p')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->where('p.supplier_id', $this->supplier_id)
            ->whereIn('p.id', $this->selected_orders)
            ->select(
                'p.advance_tax','p.id', 'p.supplier_invoice', 'p.grn_no', 'p.delivery_date', DB::raw('sum(total_cost) as total_cost')
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
//            $this->purchase_orders = $result->toArray();
        }

        $returns = SupplierRefund::from('supplier_refunds as sr')
            ->where('sr.supplier_id', $this->supplier_id)
            ->whereIn('sr.id', $this->selected_returns)
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
        return view('pharmacy::livewire.payments.supplier.view');
    }


}
