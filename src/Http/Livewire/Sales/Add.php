<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use App\Models\Hospital\Admission;
use App\Models\Hospital\Hospital;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Devzone\Pharmacy\Models\Sale\SaleIssuance;
use Devzone\Pharmacy\Models\Sale\UserTill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Add extends Component
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
    public $sales = [];
    public $tills = [];
    public $till_id;
    public $till_name;
    public $choose_till = false;
    public $admission_id;
    public $procedure_id;
    public $admission = false;
    public $admission_details = [];
    public $hospital_info = [];
    public $handed_over;

    protected $listeners = ['openSearch','searchReferredBy','searchPatient', 'emitProductId', 'emitPatientId', 'emitReferredById', 'saleComplete'];

    public function mount($admission_id = null, $procedure_id = null)
    {
        $this->admission_id = $admission_id;
        $this->procedure_id = $procedure_id;
        if (!empty($this->admission_id) && !empty($this->procedure_id)) {
            if (class_exists(\App\Models\Hospital\ProcedureMedicine::class)) {
                $this->admission = true;
                $this->admission_details = \App\Models\Hospital\Admission::from('admissions as a')
                    ->join('patients as p', 'p.id', '=', 'a.patient_id')
                    ->join('employees as e', 'e.id', '=', 'a.doctor_id')
                    ->where('a.id', $admission_id)
                    ->select('p.mr_no', 'p.name', 'a.admission_no', 'e.name as doctor')->first()
                    ->toArray();
                $medicines = \App\Models\Hospital\ProcedureMedicine::from('procedure_medicines as pm')
                    ->join('procedures as pro', 'pro.id', '=', 'pm.procedure_id')
                    ->join('products as p', 'p.id', '=', 'pm.product_id')
                    ->leftJoin('product_inventories as pi', 'p.id', '=', 'pi.product_id')
                    ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                    ->where('pm.procedure_id', $this->procedure_id)
                    ->select('p.name as item', 'p.retail_price as product_price', 'pm.qty as required_qty',
                        'pi.qty as available_qty', 'pi.retail_price', 'pro.name as procedure_name',
                        'pi.supply_price', 'pi.id', 'p.packing', 'pi.product_id', 'p.type', 'r.name as rack', 'r.tier')
                    ->groupBy('p.id')
                    ->groupBy('pi.retail_price')
                    ->orderBy('pi.qty', 'desc')->get()->toArray();
                $this->admission_details['procedure_name'] = collect($medicines)->first()['procedure_name'];
                $this->hospital_info = Hospital::first()->toArray();
                foreach ($medicines as $medicine) {
                    $required_qty = null;
                    if ($medicine['required_qty'] <= $medicine['available_qty']) {
                        $sale_qty = $medicine['required_qty'];
                    } else {
                        $sale_qty = $medicine['available_qty'];
                        $required_qty = $medicine['required_qty'];
                    }

                    if ($this->hospital_info['transfer_medicine'] == 'cost_of_price' && !empty($this->admission_id)) {
                        $medicine['retail_price'] = $medicine['supply_price'];
                    }

                    $this->sales[] = [
                        'id' => $medicine['id'],
                        'item' => $medicine['item'],
                        'qty' => $medicine['available_qty'],
                        's_qty' => $sale_qty,
                        'required_qty' => $required_qty,
                        'retail_price' => $medicine['retail_price'],
                        'product_price' => $medicine['product_price'],
                        'supply_price' => $medicine['supply_price'],
                        'disc' => 0,
                        'packing' => $medicine['packing'],
                        'product_id' => $medicine['product_id'],
                        'type' => $medicine['type'],
                        'rack' => $medicine['rack'],
                        'tier' => $medicine['tier'],
                        'total' => $sale_qty * $medicine['retail_price'],
                        'total_after_disc' => $sale_qty * $medicine['retail_price'],
                    ];
                }
            }

        }
//        $this->tills = ChartOfAccount::from('chart_of_accounts as p')
//            ->join('chart_of_accounts as c', 'p.id', '=', 'c.sub_account')
//            ->where('p.reference', 'cash-at-pharmacy-tills-4')->get()->toArray();
//        $till = UserTill::where('user_id', Auth::id())->first();
//        if (!empty($till)) {
//            $this->till_id = $till['account_id'];
//            $till_name = collect($this->tills)->firstWhere('id', $till['account_id']);
//            $this->till_name = $till_name['name'];
//        }

        $this->searchable_emit_only = true;
    }

    public function emitReferredById()
    {
        if (empty($this->admission_id) && empty($this->procedure_id)) {
            $data = $this->searchable_data[$this->highlight_index];
            $this->referred_by_id = $data['id'];
            $this->referred_by_name = $data['name'];
            $this->searchableReset();
        }

    }

    public function emitPatientId()
    {
        if (empty($this->admission_id) && empty($this->procedure_id)) {
            $data = $this->searchable_data[$this->highlight_index];
            $this->patient_id = $data['id'];
            $this->patient_name = $data['mr_no'] . ' - ' . $data['name'];
            $this->searchableReset();
        }

    }

    public function emitProductId()
    {
        $data = $this->searchable_data[$this->highlight_index];
        if ($data['qty'] > 0) {
            $check = collect($this->sales)->where('id', $data['id'])->all();

            if (empty($check)) {

                if ($this->admission) {
                    if ($this->hospital_info['transfer_medicine'] == 'cost_of_price') {
                        $data['retail_price'] = $data['supply_price'];
                    }
                }

                $data['s_qty'] = 1;
                $data['disc'] = 0;
                $data['total'] = $data['retail_price'];
                $data['total_after_disc'] = $data['retail_price'];
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
        $this->searchableOpenModal('product_id', 'product_name', 'item');
    }
    public function searchReferredBy()
    {
        $this->searchableOpenModal('referred_by_id', 'referred_by_name', 'referred_by');
    }
    public function searchPatient()
    {
        $this->searchableOpenModal('patient_id', 'patient_name', 'patient');
    }

    public function render()
    {
        return view('pharmacy::livewire.sales.add');
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

    public function saleComplete()
    {
        try {
            DB::beginTransaction();
            if (empty($this->sales)) {
                throw new \Exception('Invoice is empty.');
            }
            if (empty(Auth::user()->account_id)) {
                throw new \Exception('Cash in Hand - ' . Auth::user()->name . ' not found.');
            }
            if (empty($this->received) && $this->admission == false) {
                throw new \Exception('Please enter received amount.');
            }
            if ((collect($this->sales)->sum('total_after_disc') > $this->received) && $this->admission == false) {
                throw new \Exception('Received amount should be greater than PKR ' . collect($this->sales)->sum('total_after_disc') . "/-");
            }
            if ($this->admission == true) {
                if (empty($this->handed_over)) {
                    throw new \Exception('Handed over field is required.');
                }
            }

            $sale_id = Sale::create([
                'patient_id' => $this->patient_id,
                'referred_by' => $this->referred_by_id,
                'sale_by' => Auth::id(),
                'sale_at' => date('Y-m-d H:i:s'),
                'remarks' => $this->remarks,
                'receive_amount' => $this->received,
                'payable_amount' => $this->payable,
                'sub_total' => collect($this->sales)->sum('total'),
                'gross_total' => collect($this->sales)->sum('total_after_disc'),
                'admission_id' => $this->admission_id ?? null,
                'procedure_id' => $this->procedure_id ?? null,
            ])->id;

            foreach ($this->sales as $s) {
                $inv = ProductInventory::where('product_id', $s['product_id'])
                    ->where('supply_price', $s['supply_price'])
                    ->where('qty', '>', 0)->orderBy('id', 'asc')->get();

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
                                'type'=>'sale',
                                'description' => "Sale on dated " . date('d M, Y') .
                                    " against receipt #" . $sale_id
                            ]);
                            $sale_qty = 0;
                        }
                        if ($sale_qty > $product_inv->qty) {
                            $dec = $product_inv->qty;
                            $product_inv->decrement('qty', $dec);
                            InventoryLedger::create([
                                'product_id' => $product_inv->product_id,
                                'order_id' => $product_inv->po_id,
                                'decrease' => $product_inv->qty,
                                'type'=>'sale',
                                'description' => "Sale on dated " . date('d M, Y') .
                                    " against receipt #" . $sale_id
                            ]);
                            $sale_qty = $sale_qty - $dec;
                        }
                        $total = $s['retail_price'] * $dec;

                        $discount = round(($s['disc'] / 100) * $total, 2);
                        $after_total = $total - $discount;
                        SaleDetail::create([
                            'sale_id' => $sale_id,
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
            $accounts = ChartOfAccount::whereIn('reference', ['pharmacy-inventory-5', 'income-pharmacy-5', 'cost-of-sales-pharmacy-5'])->get();

            $amounts = SaleDetail::where('sale_id', $sale_id)->select(DB::raw('SUM(total_after_disc) as sale'), DB::raw('SUM(qty * supply_price) as cost'))->first();
            $customer_name = $this->patient_name ?? 'walking customer';
            $description = "Being goods worth PKR " . number_format($amounts['sale'], 2) . " receipt # {$sale_id} sold to Patient {$customer_name}. Cash received PKR " .
                number_format($amounts['sale'], 2) . " on " . date('d M, Y') . " by " . Auth::user()->name;
            $vno = Voucher::instance()->voucher()->get();

            if ($this->admission) {
                if (class_exists(\App\Models\Hospital\AdmissionJobDetail::class)) {
                    $admission_details = \App\Models\Hospital\AdmissionJobDetail::from('admission_job_details as ajd')
                        ->join('admissions as a', 'a.id', '=', 'ajd.admission_id')
                        ->join('procedures as p', 'p.id', '=', 'ajd.procedure_id')
                        ->where('ajd.admission_id', $this->admission_id)
                        ->where('ajd.procedure_id', $this->procedure_id)
                        ->select('a.admission_no', 'a.checkout_date', 'p.name as procedure_name')
                        ->first();
                    if (!empty($admission_details->checkout_date)) {
                        throw new \Exception('Can not Issue Medicines for Closed Admission');
                    }
                }
                if (class_exists(\App\Models\Hospital\AdmissionPaymentDetail::class)) {
                    \App\Models\Hospital\AdmissionPaymentDetail::from('admission_payment_details as apd')->where('apd.admission_id', $this->admission_id)
                        ->where('apd.procedure_id', $this->procedure_id)
                        ->where('apd.medicines', 't')
                        ->update([
                            'sale_id' => $sale_id,
                            'amount' => $amounts->sale,
                        ]);

                    $ipd_medicine_account = ChartOfAccount::where('reference', 'payable-medicine-5')->first();

                    $description = "Being goods worth PKR " . number_format($amounts['sale'], 2) .
                        " receipt # {$sale_id} issued against admission # " . $admission_details->admission_no . " and procedure " . $admission_details->procedure_name . ". Account " . $ipd_medicine_account->name . " debited with PKR " .
                        number_format($amounts['sale'], 2) . " on " . date('d M, Y') . " by " . Auth::user()->name;
                    GeneralJournal::instance()->account($ipd_medicine_account->id)->debit($amounts['sale'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
            }

            if ($this->admission == false) {
                GeneralJournal::instance()->account(Auth::user()->account_id)->debit($amounts['sale'])->voucherNo($vno)
                    ->date(date('Y-m-d'))->approve()->description($description)->execute();
            }

            foreach ($accounts as $a) {
                if ($a->reference == 'pharmacy-inventory-5') {
                    GeneralJournal::instance()->account($a->id)->credit($amounts['cost'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
                if ($a->reference == 'income-pharmacy-5') {
                    GeneralJournal::instance()->account($a->id)->credit($amounts['sale'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
                if ($a->reference == 'cost-of-sales-pharmacy-5') {
                    GeneralJournal::instance()->account($a->id)->debit($amounts['cost'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
            }
            if ($this->admission == true) {
                SaleIssuance::create([
                    'sale_id' => $sale_id,
                    'handed_over_to' => $this->handed_over,
                ]);
            }
            $this->resetAll();
            $this->searchableReset();
            $this->success = 'Sale has been complete with receipt #' . $sale_id;
            DB::commit();
            $this->emit('printInvoice', $sale_id, $this->admission_id, $this->procedure_id);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            DB::rollBack();
        }
    }

    public function resetAll()
    {
        $this->reset(['sales', 'referred_by_id', 'referred_by_name', 'success', 'patient_id', 'patient_name',
            'payable', 'received', 'remarks', 'discount', 'error']);
    }

    public function updatedTillId($value)
    {
        $till = collect($this->tills)->firstWhere('id', $value);
        if (!empty($till)) {
            $this->till_name = $till['name'];
        }
    }

    public function updateTill()
    {
        UserTill::updateOrCreate([
            'user_id' => Auth::id()
        ], [
            'account_id' => $this->till_id
        ]);
        $this->choose_till = false;
    }
}
