<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Supplier;

use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentRefundDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseReceive;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentList extends Component
{
    use Searchable;


    public $supplier_id;
    public $supplier_id_s;
    public $supplier_name;
    public $approval_supplier_name;
    public $confirm_dialog = false;
    public $pay_from;
    public $pay_from_s;
    public $pay_from_name;
    public $status;
    public $payment_date;
    public $primary_id;
    public $amt;
    public $paid_from;


    public function render()
    {

        $payments = SupplierPayment::from('supplier_payments as sp')
            ->join('suppliers as s', 's.id', 'sp.supplier_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'sp.pay_from')
            ->join('supplier_payment_details as spd', 'spd.supplier_payment_id', '=', 'sp.id')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'spd.order_id')
            ->join('purchases as p', 'p.id', '=', 'spd.order_id')
            ->join('users as c', 'c.id', '=', 'sp.added_by')
            ->leftJoin('users as a', 'a.id', '=', 'sp.approved_by')
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('sp.supplier_id', $this->supplier_id_s);
            })
            ->when(!empty($this->pay_from_s), function ($q) {
                return $q->where('sp.pay_from', $this->pay_from_s);
            })
            ->when(!empty($this->status), function ($q) {
                if ($this->status == 'app') {
                    return $q->whereNotNull('sp.approved_at');
                } else {
                    return $q->whereNull('sp.approved_at');
                }

            })
            ->select('p.advance_tax', 's.name as supplier_name', 'sp.id', 'sp.description', 'coa.name as account_name', 'sp.payment_date',
                DB::raw('sum(pr.total_cost) as total_cost'), 'sp.created_at', 'c.name as created_by',
                'a.name as approved_by', 'sp.approved_at', 'spd.order_id')
            ->groupBy('sp.id')
            ->orderBy('sp.id', 'desc')
            ->paginate(20);
        return view('pharmacy::livewire.payments.supplier.payment-list', ['payments' => $payments]);
    }

    public function markAsApproved($id, $date, $supplier_name, $amt, $paid_from)
    {
        $this->payment_date = $date;
        $this->primary_id = $id;
        $this->approval_supplier_name = $supplier_name;
        $this->amt = $amt;
        $this->paid_from = $paid_from;
        $this->confirm_dialog = true;
    }

    public function proceed()
    {
        $this->resetErrorBag();
        $this->validate([
            'payment_date' => 'required|date',
            'primary_id' => 'required|integer',
        ]);

        $this->markAsApprovedConfirm();
        $this->reset(['payment_date', 'primary_id', 'approval_supplier_name', 'amt', 'paid_from']);
        $this->confirm_dialog = false;
    }


    public function markAsApprovedConfirm()
    {
        try {
            $id = $this->primary_id;
            DB::beginTransaction();
            $supplier_payment = SupplierPayment::findOrFail($id);
            if (!auth()->user()->can('12.approve-supplier-payments')) {
                throw new \Exception(env('PERMISSION_ERROR', 'Access Denied'));
            }
            if (!empty($supplier_payment->approved_at)) {
                throw new \Exception('Payment already approved.');
            }
            $tax = ChartOfAccount::where('reference', 'advance-tax-236')->first();
            if (empty($tax)) {
                throw new \Exception('Advance tax account not found in chart of accounts.');
            }
            $orders = SupplierPaymentDetail::where('supplier_payment_id', $id)->get()->pluck('order_id')->toArray();
            if (Purchase::whereIn('id', $orders)->where('is_paid', 't')->exists()) {
                throw new \Exception('Purchase order that you select already mark as paid.');
            }

            $returns = SupplierPaymentRefundDetail::where('supplier_payment_id', $id)->get()->pluck('refund_id')->toArray();
            if (SupplierRefund::whereIn('id', $returns)->where('is_receive', 't')->exists()) {
                throw new \Exception('Un adjusted returns that you select already mark as settled.');
            }

            $supplier_payment_receipt_no = Voucher::instance()->advances()->get();
            $pay_from = ChartOfAccount::findOrFail($supplier_payment->pay_from);
            $supplier = Supplier::findOrFail($supplier_payment->supplier_id);

            $advance_tax = Purchase::whereIn('id', $orders)->first()->advance_tax;

            $amount = PurchaseReceive::whereIn('purchase_id', $orders)->sum('total_cost');
            $return_amount = SupplierRefund::whereIn('id', $returns)->sum('total_amount');
            $diff = $amount - $return_amount;
            $tax_amount = 0;
            if (!empty($advance_tax)) {
                $tax_amount = $diff * (abs($advance_tax) / 100);
            }
            $diff = $diff + $tax_amount;


            $vno = Voucher::instance()->voucher()->get();
            $bank = GeneralJournal::instance()->account($pay_from['id']);
            if ($diff > 0) {
                $bank = $bank->credit($diff);
                $des = "paid";
            } else {
                $bank = $bank->debit(abs($diff));
                $des = "received";
            }

            $description = "PAYMENT Amounting total PKR " . number_format(abs($diff), 2) .
                "/- to supplier '" . $supplier['name'] . "' against PO # " . implode(', ', $orders) . " & invoice # inv-" . $supplier_payment_receipt_no .
                ". Payment from '" . $pay_from['name'] . "' by user " . Auth::user()->name . " on dated " . date('d M, Y h:i A');

            $bank->voucherNo($vno)->date($this->payment_date)->reference('supplier-payment')
                ->approve()->description($description)->execute();

            $sp = GeneralJournal::instance()->account($supplier['account_id']);
            if ($diff > 0) {
                $sp = $sp->debit($diff);
            } else {
                $sp = $sp->credit(abs($diff));
            }
            $sp->voucherNo($vno)->date($this->payment_date)->approve()->reference('supplier-payment')
                ->description($description)->execute();

            Purchase::whereIn('id', $orders)->update([
                'is_paid' => 't'
            ]);
            SupplierRefund::whereIn('id', $returns)->update([
                'is_receive' => 't'
            ]);

            $supplier_payment->update([
                'approved_by' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
                'payment_date' => $this->payment_date,
                'receipt_no' => $supplier_payment_receipt_no,
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError('status', $exception->getMessage());
        }


    }


    public function removePurchase($id)
    {
        SupplierPayment::whereNull('approved_at')->where('id', $id)->delete();
    }

    public function search()
    {
        $this->supplier_id_s = $this->supplier_id;
        $this->pay_from_s = $this->pay_from;
    }

    public function resetSearch()
    {
        $this->reset(
            'supplier_id_s',
            'pay_from_s',
            'pay_from',
            'supplier_id',
            'supplier_name',
            'pay_from_name',
            'status'
        );
    }
}
