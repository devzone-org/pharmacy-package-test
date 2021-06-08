<?php

namespace Devzone\Pharmacy\Http\Livewire\Refunds\Supplier;

use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseReceive;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RefundList extends Component
{
    use Searchable;


    public $supplier_id;
    public $supplier_id_s;
    public $supplier_name;

    public $pay_from;
    public $pay_from_s;
    public $pay_from_name;
    public $status;
    public $confirm_dialog = false;
    public $primary_id;
    public $receive_date;

    public function render()
    {
        $payments = SupplierRefund::from('supplier_refunds as sr')
            ->join('supplier_refund_details as srd', 'srd.supplier_refund_id', '=', 'sr.id')
            ->join('suppliers as s', 's.id', 'sr.supplier_id')
            ->leftJoin('chart_of_accounts as coa', 'coa.id', '=', 'sr.receive_in')
            ->join('product_inventories as pi', 'pi.id', '=', 'srd.product_inventory_id')
            ->join('users as c', 'c.id', '=', 'sr.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'sr.approved_by')
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('sr.supplier_id', $this->supplier_id_s);
            })
            ->when(!empty($this->pay_from_s), function ($q) {
                return $q->where('sr.pay_from', $this->pay_from_s);
            })
            ->when(!empty($this->status), function ($q) {
                if ($this->status == 'app') {
                    return $q->whereNotNull('sr.approved_at');
                } else {
                    return $q->whereNull('sr.approved_at');
                }

            })
            ->select('s.name as supplier_name', 'sr.id', 'sr.description', 'coa.name as account_name',
                DB::raw('sum(pi.supply_price * srd.qty) as total_cost'), 'sr.created_at', 'c.name as created_by',
                'a.name as approved_by', 'sr.approved_at','sr.receiving_date','sr.is_receive')
            ->groupBy('srd.supplier_refund_id')
            ->orderBy('sr.id', 'desc')
            ->paginate(20);
        return view('pharmacy::livewire.refunds.supplier.list', ['payments' => $payments]);
    }


    public function markAsApproved($id)
    {
        SupplierRefund::find($id)->update([
            'approved_by' => Auth::user()->id,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function confirm($id)
    {
        //TODO this is refund case pending
        try {
            DB::beginTransaction();
            $supplier_refund = SupplierRefund::findOrFail($id);

            if (!empty($supplier_refund->approved_at)) {
                throw new \Exception('Payment already refunded.');
            }
            $pay_from = ChartOfAccount::findOrFail($supplier_refund->receive_in);



            $amount = PurchaseReceive::whereIn('purchase_id', $orders)->sum('total_cost');
            $description = "Amounting total PKR " . number_format($amount, 2) . "/- paid on dated " . date('d M, Y', strtotime($supplier_payment->payment_date)) .
                " against PO # " . implode(', ', $orders) . " to supplier " . $supplier['name'] . " by " . Auth::user()->name . " " . $supplier_payment->description;


            $vno = Voucher::instance()->voucher()->get();
            GeneralJournal::instance()->account($pay_from['id'])->credit($amount)->voucherNo($vno)
                ->date($supplier_payment->payment_date)->approve()->description($description)->execute();
            GeneralJournal::instance()->account($supplier['account_id'])->debit($amount)->voucherNo($vno)
                ->date($supplier_payment->payment_date)->approve()->description($description)->execute();

            $response = Purchase::whereIn('id', $orders)->update([
                'is_paid' => 't'
            ]);

            $supplier_payment->update([
                'approved_by' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError('status', $exception->getMessage());
        }


    }

    public function openConfirmation($id,$date)
    {
        $this->primary_id =$id;
        $this->receive_date = $date;
        $this->confirm_dialog = true;
    }

    public function proceed()
    {
        $this->resetErrorBag();
        $this->validate([
            'receive_date' => 'required|date',
            'primary_id' => 'required|integer',
        ]);

        $this->confirm($this->primary_id);
        $this->reset(['payment_date', 'primary_id']);
        $this->confirm_dialog = false;
    }


    public function removePurchase($id)
    {

        SupplierRefund::whereNull('approved_at')->where('id', $id)->delete();
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
