<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use App\Models\SaleRefundDetail;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Ams\Models\Ledger;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Devzone\Pharmacy\Models\Sale\SaleIssuance;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Devzone\Pharmacy\Models\UserLimit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Refund extends Component
{
    use Searchable;

    public $product_id;
    public $product_name;
    public $discount = 0;
    public $received = 0;
    public $payable = 0;
    public $referred_by_id;
    public $referred_by_name;
    public $patient_id;
    public $patient_name;
    public $remarks;
    public $success;
    public $error;
    public $old_sales = [];
    public $sales = [];
    public $till_id;
    public $refunds = [];
    public $sale_id;
    public $admission_id;
    public $procedure_id;
    public $type;
    public $admission_details = [];
    public $hospital_info = [];
    public $handed_over;
    public $credit;
    public $is_credited;
    public $customer_id;
    public $customer_name;
    protected $listeners = ['openSearch', 'emitProductId', 'emitPatientId', 'emitReferredById', 'saleComplete'];

    public function mount($primary_id, $type)
    {
        $this->sale_id = $primary_id;
        $this->type = $type;
        $this->searchable_emit_only = true;
        $this->old_sales = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->join('products as pr', 'pr.id', '=', 'sd.product_id')
            ->leftJoin('procedures as pro', 'pro.id', '=', 's.procedure_id')
            ->leftJoin('customers as cus', 'cus.id', '=', 's.customer_id')
            ->where('s.id', $this->sale_id)
            ->select('sd.*', DB::raw('sum(sd.qty) as qty'), 'pr.name as item', 's.remarks', 's.receive_amount', 's.payable_amount', 's.sub_total', 's.gross_total'
                , 's.patient_id', 's.referred_by', 's.admission_id', 's.procedure_id', 's.customer_id', 's.is_credit', 'e.name as referred_by_name', 'p.mr_no', 'p.name as patient_name',
                'pro.name as procedure_name', 'cus.name as customer_name')
            ->groupBy('sd.id')
            ->orderBy('sd.product_id')
            ->get()
            ->toArray();

        $first = collect($this->old_sales)->first();

        $this->admission_id = $first['admission_id'];
        $this->procedure_id = $first['procedure_id'];
        $this->customer_id = $first['customer_id'];
        $this->is_credited = $first['is_credit'];
        $this->hospital_info = \App\Models\Hospital\Hospital::first()->toArray();
        if (!empty($this->admission_id) && !empty($this->procedure_id)) {
            $this->admission_details = \App\Models\Hospital\Admission::from('admissions as a')
                ->join('patients as p', 'p.id', '=', 'a.patient_id')
                ->leftJoin('employees as e', 'e.id', '=', 'a.doctor_id')
                ->where('a.id', $this->admission_id)
                ->select('p.mr_no', 'p.name', 'a.admission_no', 'e.name as doctor')->first()
                ->toArray();
            $this->admission_details['procedure_name'] = collect($this->old_sales)->first()['procedure_name'];
        }
        foreach ($this->old_sales as $s) {
            $check = SaleRefund::where('sale_id', $s['sale_id'])->where('sale_detail_id', $s['id'])
                ->where('product_id', $s['product_id'])
                ->sum('refund_qty');
            if (!empty($check)) {
                $s['qty'] = $check;
                $s['s_qty'] = $s['qty'];
                $s['restrict'] = true;
                $s['total'] = $s['qty'] * $s['retail_price'];
                $s['total_after_disc'] = $s['total'];
                if ($s['disc'] > 0) {
                    $discount = round(($s['disc'] / 100) * $s['total'], 2);
                    $s['total_after_disc'] = $s['total'] - $discount;
                }
                $this->refunds[] = $s;
            }
        }

    }

    public function emitReferredById()
    {
        $data = $this->searchable_data[$this->highlight_index];
        $this->referred_by_id = $data['id'];
        $this->referred_by_name = $data['name'];
        $this->searchableReset();
    }

    public function emitPatientId()
    {
        $data = $this->searchable_data[$this->highlight_index];
        $this->patient_id = $data['id'];
        $this->patient_name = $data['mr_no'] . ' - ' . $data['name'];
        $this->searchableReset();
    }

    public function emitProductId()
    {
        $data = $this->searchable_data[$this->highlight_index];

        if ($data['qty'] > 0) {
            $check = collect($this->sales)->where('id', $data['id'])->all();
            $price = $data['retail_price'];

            if ($this->hospital_info['transfer_medicine'] == 'cost_of_price' && !empty($this->admission_id)) {
                $price = $data['supply_price'];
                $data['retail_price'] = $price;
            }

            if (empty($check)) {
                $data['s_qty'] = 1;
                $data['disc'] = 0;
                $data['total'] = $price;
                $data['total_after_disc'] = $price;
                $this->sales[] = $data;
            } else {
                $key = array_keys($check)[0];
                if ($check[$key]['s_qty'] < $check[$key]['qty']) {
                    $qty = $this->sales[$key]['s_qty'];
                    $this->sales[$key]['s_qty'] = $qty + 1;
                    $this->sales[$key]['total'] = $this->sales[$key]['retail_price'] * $this->sales[$key]['s_qty'];
                    $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'];
                }
            }
        }
    }

    public function openSearch()
    {
        $this->searchableOpenModal('product_id', 'product_name', 'inventory');
    }

    public function render()
    {
        return view('pharmacy::livewire.sales.refund');
    }


    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'sales') {
            if (empty($value) || !is_numeric($value)) {
                $this->sales[$array[1]][$array[2]] = 0;
            }
            if (in_array($array[2], ['s_qty', 'retail_price', 'disc'])) {
                if ($this->sales[$array[1]]['s_qty'] > $this->sales[$array[1]]['qty']) {
                    $this->sales[$array[1]]['s_qty'] = $this->sales[$array[1]]['qty'];
                } else if ($this->sales[$array[1]]['s_qty'] < 0) {
                    $this->sales[$array[1]]['s_qty'] = 0;
                }
                $this->sales[$array[1]]['total'] = round($this->sales[$array[1]]['s_qty'] * $this->sales[$array[1]]['retail_price'], 2);

                if ($this->sales[$array[1]]['disc'] >= 0 || $this->sales[$array[1]]['disc'] <= 100) {
                    $discount = round(($this->sales[$array[1]]['disc'] / 100) * $this->sales[$array[1]]['total'], 2);
                    $this->sales[$array[1]]['total_after_disc'] = $this->sales[$array[1]]['total'] - $discount;
                }
            }
        }

        if ($array[0] == 'refunds') {
            if (empty($value) || !is_numeric($value)) {
                $this->refunds[$array[1]][$array[2]] = 0;
            }

            if (in_array($array[2], ['qty'])) {
                if ($this->refunds[$array[1]]['qty'] > $this->refunds[$array[1]]['s_qty']) {
                    $this->refunds[$array[1]]['qty'] = $this->refunds[$array[1]]['s_qty'];
                } else if ($this->refunds[$array[1]]['qty'] < 0) {
                    $this->refunds[$array[1]]['qty'] = 0;
                }
                $this->refunds[$array[1]]['total'] = round($this->refunds[$array[1]]['qty'] * $this->refunds[$array[1]]['retail_price'], 2);


                if ($this->refunds[$array[1]]['disc'] >= 0 || $this->refunds[$array[1]]['disc'] <= 100) {
                    $discount = round(($this->refunds[$array[1]]['disc'] / 100) * $this->refunds[$array[1]]['total'], 2);
                    $this->refunds[$array[1]]['total_after_disc'] = $this->refunds[$array[1]]['total'] - $discount;
                }

            }
        }
    }


    public function updatedDiscount($value)
    {
        if (empty($value) || !is_numeric($value)) {
            $this->discount = 0;
            $value = 0;
        }

        foreach ($this->sales as $key => $s) {
            if ($value >= 0 || $value <= 100) {
                $discount = round(($value / 100) * $this->sales[$key]['total'], 2);
                $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'] - $discount;
                $this->sales[$key]['disc'] = $value;
            }
        }

    }

    public function updatedReceived($value)
    {
        if (empty($value) || !is_numeric($value)) {
            $value = 0;
        }
        $this->payable = $value - collect($this->sales)->sum('total_after_disc');
    }

    public function removeEntry($key)
    {
        unset($this->sales[$key]);
    }

    public function removeRefundEntry($key)
    {
        unset($this->refunds[$key]);
    }

    public function refundEntry($key)
    {
        $old_sale = $this->old_sales[$key];
        $data = collect($this->refunds)->where('id', $old_sale['id'])->firstWhere('sale_id', $this->sale_id);

        if ($old_sale['qty'] > $data['qty']) {
            $old_sale['s_qty'] = $old_sale['qty'];
            $old_sale['qty'] = $old_sale['qty'] - $data['qty'];
            $old_sale['total'] = $old_sale['qty'] * $old_sale['retail_price'];
            $old_sale['total_after_disc'] = $old_sale['total'];
            $old_sale['new'] = '1';
            if ($old_sale['disc'] > 0) {
                $discount = round(($old_sale['disc'] / 100) * $old_sale['total'], 2);
                $old_sale['total_after_disc'] = $old_sale['total'] - $discount;
            }
            $this->refunds[] = $old_sale;
        }
    }

    public function saleComplete()
    {
        try {
            if ($this->type == 'refund') {
                if (empty($this->refunds)) {
                    throw new \Exception('Refund invoice is empty.');
                }
            } elseif ($this->type == 'issue') {
                if (empty($this->sales)) {
                    throw new \Exception('No item added in invoice.');
                }
            }
            if (empty(Auth::user()->account_id)) {
                throw new \Exception('Cash in Hand - ' . Auth::user()->name . ' not found.');
            }
            if (!empty($this->admission_id) && !empty($this->procedure_id)) {
                if (empty($this->handed_over) && $this->type == 'issue') {
                    throw new \Exception('Handed over field is required.');
                }
            }
            $refund_cost = 0;
            $refund_retail = 0;

            $sales_cost = 0;
            $sales_retail = 0;

            foreach (collect($this->refunds)->where('new', '1')->all() as $r) {
                $refund_cost = $refund_cost + ($r['qty'] * $r['supply_price']);
                $refund_retail = $refund_retail + ($r['total_after_disc']);
            }

            foreach ($this->sales as $r) {
                $sales_cost = $sales_cost + ($r['s_qty'] * $r['supply_price']);
                $sales_retail = $sales_retail + ($r['total_after_disc']);
            }

            DB::beginTransaction();
            $refund = false;
            $sale = Sale::find($this->sale_id);
            $sale_receipt_no = Voucher::instance()->advances()->get();
            $dif = collect($this->sales)->sum('total_after_disc') + collect($this->refunds)->where('restrict', true)->sum('total_after_disc') - collect($this->refunds)->sum('total_after_disc');
            $new_sub_total = collect($this->sales)->sum('total');
            $new_total_after_disc = collect($this->sales)->sum('total_after_disc');
            $total_refund = collect($this->refunds)->sum('total_after_disc') - collect($this->refunds)->where('restrict', true)->sum('total_after_disc');
            if ($dif > 0) {
                if (!empty($this->credit) && $this->received < collect($this->sales)->sum('total_after_disc')){
                    if (empty($this->customer_id)) {
                        throw new \Exception('Please select customer to credit amount.');
                    }
                    $customer_account = Customer::join('chart_of_accounts as coa', 'coa.id', '=', 'customers.account_id')
                        ->where('customers.id', $this->customer_id)
                        ->select('coa.name', 'customers.*')
                        ->first();
                    $previous_credit = Ledger::where('account_id', $customer_account->account_id)
                        ->groupBy('account_id')
                        ->select(DB::raw('sum(debit-credit) as balance'))
                        ->first();
                    $previous_balance = !empty($previous_credit) ? $previous_credit->balance : 0;
                    if ($this->received < collect($this->sales)->sum('total_after_disc')) {
                        if ((collect($this->sales)->sum('total_after_disc') - $this->received) + $previous_balance > $customer_account->credit_limit) {
                            throw new \Exception('Amount exceeding customer credit limit (PKR ' . number_format($customer_account->credit_limit) . ')');
                        }
                        $user_limit = UserLimit::where('user_id', Auth::id())
                            ->where('date', date('Y-m-d'))
                            ->first();
                        $balance = !empty($user_limit) ? $user_limit->balance : 0;

                        if (collect($this->sales)->sum('total_after_disc') + $balance > Auth::user()->credit_limit) {
                            throw new \Exception('Amount exceeding User credit limit (PKR ' . number_format(Auth::user()->credit_limit) . ')');
                        }
                        $credit_amount = collect($this->sales)->sum('total_after_disc') - $this->received;
                        if (!empty($user_limit)) {
                            UserLimit::where('id', $user_limit->id)->update([
                                'balance' => DB::raw('balance +' . $credit_amount)
                            ]);
                        } else {
                            UserLimit::create([
                                'user_id' => Auth::id(),
                                'date' => date('Y-m-d'),
                                'balance' => $credit_amount
                            ]);
                        }
                    }
                }else{
                    if ($this->received == '') {
                        throw new \Exception('Please Enter Received Amount');
                    }
                    if ($this->received < $dif) {
                        throw new \Exception('Received Amount is Not Valid');
                    }
                }

                $change_due = $this->received != '' ? $this->received - $dif : 0;
            } else {
                if ($this->received == '' && empty($this->credit)) {
                    throw new \Exception('Please Enter Paid Amount');
                }
                if ($this->received != abs($dif) && empty($this->credit)) {
                    throw new \Exception('Paid Amount is Not Valid');
                }
                $change_due = 0;
            }
            $newSale = $sale->replicate();
            $newSale->created_at = Carbon::now();
            $newSale->sale_at = date('Y-m-d H:i:s');
            $newSale->sale_by = Auth::user()->id;
            $newSale->refunded_id = $sale->id;
            $newSale->sub_total = $new_sub_total;
            $newSale->gross_total = $new_total_after_disc;
            $newSale->receive_amount = $this->received;
            $newSale->payable_amount = $change_due;
            $newSale->is_refund = 'f';
            $newSale->receipt_no = $sale_receipt_no;
            $newSale->save();
            foreach ($this->refunds as $r) {
                if (isset($r['restrict'])) {
                    continue;
                }
                $limit = SaleDetail::from('sale_details as sd')
                    ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
                    ->where('sd.product_id', $r['product_id'])
                    ->where('sd.sale_id', $r['sale_id'])
                    ->select('sd.id', 'sd.qty', 'sr.refund_qty')
                    ->get();
                $total_limit = $limit->sum('qty') - $limit->sum('refund_qty');
                $qty_tobe_refunded = $r['qty'];

                if ($qty_tobe_refunded <= $total_limit) {
                    foreach ($limit as $l) {
                        $available_qty = $l->qty - $l->refund_qty;
                        if ($qty_tobe_refunded > 0 && $available_qty > 0) {

                            if ($qty_tobe_refunded > $available_qty) {
                                $dec = $available_qty;
                            } else {
                                $dec = $qty_tobe_refunded;
                            }

                            $qty_tobe_refunded = $qty_tobe_refunded - $dec;
                            SaleRefund::updateOrCreate([
                                'sale_id' => $r['sale_id'],
                                'sale_detail_id' => $l->id,
                                'product_id' => $r['product_id']
                            ], [
                                'refund_qty' => $dec + $l->refund_qty,
                                'refunded_id' => $newSale->id
                            ]);
                            \Devzone\Pharmacy\Models\Sale\SaleRefundDetail::create([
                                'sale_id' => $r['sale_id'],
                                'sale_detail_id' => $l->id,
                                'product_id' => $r['product_id'],
                                'refund_qty' => $dec,
                                'refunded_id' => $newSale->id
                            ]);

                            ProductInventory::find($r['product_inventory_id'])->increment('qty', $dec);
                            InventoryLedger::create([
                                'product_id' => $r['product_id'],
                                'increase' => $dec,
                                'type' => 'sale-refund',
                                'description' => "Refund on dated " . date('d M, Y') .
                                    " against receipt #" . $this->sale_id
                            ]);
                        }
                    }
                    $refund = true;
                }

            }
            if ($refund) {
                Sale::find($this->sale_id)->update([
                    'is_refund' => 't',
                ]);
            }
            if (!empty($this->sales)) {
                foreach ($this->sales as $s) {
                    $inv = ProductInventory::where('product_id', $s['product_id'])
                        ->where('supply_price', $s['supply_price'])
                        ->where('qty', '>', 0)->get();

                    if ($inv->sum('qty') < $s['s_qty']) {
                        throw new \Exception('System does not have much inventory for the item ' . $s['item']);
                    }

                    $sale_qty = $s['s_qty'];
                    foreach ($inv as $i) {
                        if ($sale_qty > 0) {
                            $dec = 0;
                            $product_inv = ProductInventory::find($i->id);
                            if ($sale_qty <= $product_inv->qty) {
                                $dec = $sale_qty;
                                $product_inv->decrement('qty', $sale_qty);
                                InventoryLedger::create([
                                    'product_id' => $product_inv->product_id,
                                    'order_id' => $product_inv->po_id,
                                    'decrease' => $sale_qty,
                                    'type' => 'sale',
                                    'description' => "Sale on dated " . date('d M, Y') .
                                        " against receipt #" . $this->sale_id
                                ]);
                                $sale_qty = 0;

                            }
                            if ($sale_qty > $product_inv->qty) {
                                $dec = $product_inv->qty;
                                $product_inv->decrement('qty', $dec);
                                InventoryLedger::create([
                                    'product_id' => $product_inv->product_id,
                                    'order_id' => $product_inv->po_id,
                                    'decrease' => $dec,
                                    'type' => 'sale',
                                    'description' => "Sale on dated " . date('d M, Y') .
                                        " against receipt #" . $this->sale_id
                                ]);
                                $sale_qty = $sale_qty - $dec;
                            }
                            $total = $s['retail_price'] * $dec;

                            $discount = round(($s['disc'] / 100) * $total, 2);
                            $after_total = $total - $discount;
                            SaleDetail::create([
                                'sale_id' => $newSale->id,
                                'product_id' => $s['product_id'],
                                'product_inventory_id' => $i->id,
                                'qty' => $dec,
                                'supply_price' => $product_inv->supply_price,
                                'retail_price' => $s['retail_price'],
                                'total' => $total,
                                'disc' => $s['disc'],
                                'total_after_disc' => $after_total,
                            ]);
                        }
                    }
                }

            }
            $cash_acount = Auth::user()->account_id;
            if (!empty($this->admission_id) && !empty($this->procedure_id)) {
                $admission_details = \App\Models\Hospital\AdmissionJobDetail::from('admission_job_details as ajd')
                    ->join('admissions as a', 'a.id', '=', 'ajd.admission_id')
                    ->join('procedures as p', 'p.id', '=', 'ajd.procedure_id')
                    ->where('ajd.admission_id', $this->admission_id)
                    ->where('ajd.procedure_id', $this->procedure_id)
                    ->select('a.admission_no', 'p.name as procedure_name', 'a.checkout_date')
                    ->first();
                if (!empty($admission_details->checkout_date)) {
                    throw new \Exception('Admission Closed already. You can not Proceed further.');
                }
                $diff = $refund_retail - $sales_retail;
                if (class_exists(\App\Models\Hospital\AdmissionPaymentDetail::class)) {
                    $check = \App\Models\Hospital\AdmissionPaymentDetail::from('admission_payment_details as apd')
                        ->where('apd.admission_id', $this->admission_id)
                        ->where('apd.procedure_id', $this->procedure_id)
                        ->where('apd.medicines', 't')
                        ->update([
                            'amount' => $diff < 0 ? DB::raw('amount +' . abs($diff)) : DB::raw('amount -' . $diff)
                        ]);
                    $ipd_medicine_account = ChartOfAccount::where('reference', 'payable-medicine-5')->first();
                    $description = $this->getDescriptionAdmissionProcedure($refund_retail, $sales_retail, $admission_details->admission_no, $admission_details->procedure_name, $ipd_medicine_account->name);
                    $cash_acount = $ipd_medicine_account->id;
                }
                if (!empty($this->handed_over)) {
                    SaleIssuance::create([
                        'sale_id' => $this->sale_id,
                        'handed_over_to' => $this->handed_over,
                    ]);
                }
            } else {
                $description = $this->getDescription($refund_retail, $sales_retail);
            }
            $vno = Voucher::instance()->voucher()->get();
            $accounts = ChartOfAccount::whereIn('reference', ['cost-of-sales-pharmacy-5', 'income-pharmacy-5', 'income-return-pharmacy-5', 'pharmacy-inventory-5'])->get();
            $customer = Customer::find($this->customer_id);
            $dif = $refund_retail - $sales_retail;
            if ($dif > 0) {
                if ($this->is_credited == 't' || !empty($this->credit)) {
                    GeneralJournal::instance()->account($customer->account_id)->credit(abs($dif))->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                } else {
                    GeneralJournal::instance()->account($cash_acount)->credit(abs($dif))->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }

            } else {
                if ($this->is_credited == 't' || !empty($this->credit)) {
//                    $customer_balance = Ledger::from('ledgers as l')
//                        ->where('l.account_id', '=', $customer->account_id)
//                        ->where('l.is_approve', 't')
//                        ->select(DB::raw('sum(l.debit - l.credit) as closing'))->first();
//                    $closing_balance = !empty($customer_balance) ? $customer_balance->closing : 0;
//                    if ($closing_balance >= $dif){
                        GeneralJournal::instance()->account($customer->account_id)->debit(abs($dif))->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
//                    }
//                    else {
//                        GeneralJournal::instance()->account($cash_acount)->debit(abs($dif))->voucherNo($vno)
//                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
//                    }
                } else {
                    GeneralJournal::instance()->account($cash_acount)->debit(abs($dif))->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
            }
            foreach ($accounts as $a) {
                if ($sales_retail > 0) {
                    if ($a->reference == 'cost-of-sales-pharmacy-5') {
                        GeneralJournal::instance()->account($a->id)->debit($sales_cost)->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                    }
                    if ($a->reference == 'pharmacy-inventory-5') {
                        GeneralJournal::instance()->account($a->id)->credit($sales_cost)->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                    }
                    if ($a->reference == 'income-pharmacy-5') {
                        GeneralJournal::instance()->account($a->id)->credit($sales_retail)->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                    }
                }

                if ($refund_retail > 0) {
                    if ($a->reference == 'cost-of-sales-pharmacy-5') {
                        GeneralJournal::instance()->account($a->id)->credit($refund_cost)->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                    }
                    if ($a->reference == 'pharmacy-inventory-5') {
                        GeneralJournal::instance()->account($a->id)->debit($refund_cost)->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                    }
                    if ($a->reference == 'income-return-pharmacy-5') {
                        GeneralJournal::instance()->account($a->id)->debit($refund_retail)->voucherNo($vno)
                            ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                    }
                }
            }


            $this->searchableReset();
            $this->success = 'Refund has been complete with receipt #' . $this->sale_id;
            DB::commit();


            if (empty($this->admission_id) && empty($this->procedure_id)) {
                return redirect()->to('pharmacy/sales/refund/' . $this->sale_id . '?type=refund');
            } else {
                if ($this->type == 'refund') {
                    return redirect()->to('pharmacy/sales/refund/' . $this->sale_id . '?type=refund&admission_id=' . $this->admission_id . '&procedure_id=' . $this->procedure_id);
                } elseif ($this->type == 'issue') {
                    $this->emit('printInvoice', $this->sale_id, $this->admission_id, $this->procedure_id);
//                    return redirect()->to('pharmacy/sales/refund/' . $this->sale_id . '?type=issue&admission_id=' . $this->admission_id . '&procedure_id=' . $this->procedure_id);
                }
            }
        } catch
        (\Exception $e) {
            $this->error = $e->getMessage();
            DB::rollBack();
        }
    }

    public function getDescriptionAdmissionProcedure($refund_retail, $sales_retail, $admission_no, $procedure_name, $account_name)
    {
        if ($this->type == 'refund') {
            $description = 'Refunded: PKR ' . number_format($refund_retail, 2) . ' ';
        }
        if ($this->type == 'issue') {
            $description = 'Being goods worth PKR ' . number_format($sales_retail, 2) . ' ';
        }
        $description .= ' issued against Admission # ' . $admission_no . ' and procedure ' . $procedure_name . " on " . date('d M, Y h:i A') . " by " . Auth::user()->name;
        return $description;
    }

    public function getDescription($refund_retail, $sales_retail)
    {
        if ($refund_retail > 0 && $sales_retail > 0) {
            $description = 'Refunded: PKR ' . number_format($refund_retail, 2) . ' SOLD: PKR ' . number_format($sales_retail, 2) . ' ';
        } else {
            if ($refund_retail > 0) {
                $description = 'Refunded: PKR ' . number_format($refund_retail, 2) . ' ';
            }
            if ($sales_retail > 0) {
                $description = 'Refunded: PKR ' . number_format($sales_retail, 2) . ' ';
            }
        }

        if ($this->old_sales[0]['patient_id'] > 0) {
            $description .= 'to patient ' . $this->old_sales[0]['patient_name'] . ' against MR# ' . $this->old_sales[0]['mr_no'] . '. ';
        } else {
            $description .= 'to walking customer. ';
        }
        $dif = $refund_retail - $sales_retail;
        if ($dif > 0) {
            $description .= "Net paid amount PKR " . number_format(abs($dif), 2) . ' ';
        } else {
            $description .= "Net received amount PKR " . number_format(abs($dif), 2) . ' ';
        }

        $description .= " on " . date('d M, Y') . " by " . Auth::user()->name;
        return $description;
    }

    public function resetAll()
    {
        $this->reset(['sales', 'referred_by_id', 'referred_by_name', 'success', 'patient_id', 'patient_name',
            'payable', 'received', 'remarks', 'discount', 'error']);
    }


}
