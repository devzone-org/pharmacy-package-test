<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Customer;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\Payments\CustomerPayment;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Models\Payments\CustomerPaymentDetail;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use Searchable, WithPagination;

    public $confirm_dialog = false;
    public $success;
    public $status;
    public $customer_id;
    public $customer_name;
    public $receiving_date;
    public $primary_id;
    public $approval_customer_name;
    public $amt;
    public $receiving_in;

    public function render()
    {
        $payments = CustomerPayment::from('customer_payments as cp')
            ->join('customers as c', 'c.id', 'cp.customer_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'cp.receiving_in')
            ->join('users as us', 'us.id', '=', 'cp.added_by')
            ->leftJoin('users as a', 'a.id', '=', 'cp.approved_by')
            ->when(!empty($this->customer_id), function ($q) {
                return $q->where('cp.customer_id', $this->customer_id);
            })
            ->when(!empty($this->status), function ($q) {
                if ($this->status == 'app') {
                    return $q->whereNotNull('cp.approved_at');
                } else {
                    return $q->whereNull('cp.approved_at');
                }
            })
            ->select(
                'c.name as customer_name', 'coa.name as account_name', 'us.name as added_by', 'a.name as approved_by',
                'cp.id', 'cp.description', 'cp.receiving_date',
                'cp.created_at', 'cp.approved_at', 'cp.amount'
            )
            ->groupBy('cp.id')
            ->orderBy('cp.id', 'desc')
            ->paginate(20);
        return view('pharmacy::livewire.payments.customer.payment-list', ['payments' => $payments]);
    }

    public function search()
    {

    }

    public function markAsApproved($id, $date, $customer_name, $amt, $received_in)
    {
        $this->receiving_date = $date;
        $this->primary_id = $id;
        $this->approval_customer_name = $customer_name;
        $this->amt = $amt;
        $this->receiving_in = $received_in;
        $this->confirm_dialog = true;
    }

    public function proceed()
    {
        $this->resetErrorBag();
        $this->validate([
            'receiving_date' => 'required|date',
            'primary_id' => 'required|integer',
        ]);

        $this->markAsApprovedConfirm();
        $this->reset(['receiving_date', 'primary_id', 'approval_customer_name', 'amt', 'receiving_in']);
        $this->confirm_dialog = false;
    }

    public function markAsApprovedConfirm()
    {
        try {
            $id = $this->primary_id;
            DB::beginTransaction();
            $customer_payment = CustomerPayment::findOrFail($id);

            if (!empty($customer_payment->approved_at)) {
                throw new \Exception('Payment already approved.');
            }
            $sales = CustomerPaymentDetail::where('customer_payment_id', $id)->get()->pluck('sale_id')->toArray();
            if (Sale::whereIn('id', $sales)->where('is_paid', 't')->exists()) {
                throw new \Exception('Sale Receipt that you select already mark as paid.');
            }

            $customer_payment_receipt_no = Voucher::instance()->advances()->get();
            $receiving_in = ChartOfAccount::findOrFail($customer_payment->receiving_in);
            $customer = Customer::findOrFail($customer_payment->customer_id);


            $amount = Sale::whereIn('id', $sales)->sum('gross_total');

            $refund_entries = \Devzone\Pharmacy\Models\Sale\SaleRefund::from('sale_refunds as sr')
                ->join('sale_details as sd','sd.id','=','sr.sale_detail_id')
                ->whereIn('sr.sale_id',$sales)
                ->groupBy('sr.sale_id')
                ->select(\Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as total_refunded'))
                ->get();

            $return_amount = $refund_entries->sum('total_refunded');
            $diff = $amount - $return_amount;
            if ($diff!= $customer_payment['amount']) {
                throw new \Exception('Receive amount mismatch.');
            }
            $vno = Voucher::instance()->voucher()->get();


            $description = "Received: Amounting total PKR " . number_format(abs($diff), 2) .
                "/- from customer '" . $customer['name'] . "' against sale # " . implode(', ', $sales) . " & invoice # inv-" . $customer_payment_receipt_no .
                ". Received '" . $receiving_in['name'] . "' by user " . Auth::user()->name . " on dated " . date('d M, Y h:i A');

            GeneralJournal::instance()->account($customer->account_id)->credit($diff)->voucherNo($vno)
                ->date(date('Y-m-d'))->approve()->description($description)->execute();

            GeneralJournal::instance()->account($customer_payment->receiving_in)->debit($diff)->voucherNo($vno)
                ->date(date('Y-m-d'))->approve()->description($description)->execute();

            Sale::whereIn('id', $sales)->update([
                'is_paid' => 't'
            ]);
            $customer_payment->update([
                'approved_by' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
                'receiving_date' => $this->receiving_date,
                'receipt_no' => $customer_payment_receipt_no,
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError('status', $exception->getMessage());
        }
    }

    public function removePayment($id)
    {
        CustomerPayment::whereNull('approved_at')->where('id', $id)->delete();
    }

    public function resetSearch()
    {
        $this->reset(
            'customer_id',
            'customer_name',
            'status'
        );
    }
}