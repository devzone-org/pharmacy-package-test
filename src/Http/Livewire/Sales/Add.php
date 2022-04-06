<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use App\Models\Hospital\Admission;
use App\Models\Hospital\AdmissionJobDetail;
use App\Models\Hospital\Employees\Employee;
use App\Models\Hospital\Hospital;
use App\Models\Hospital\Patient;
use Devzone\Ams\Helper\ChartOfAccount;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount as COA;
use Devzone\Ams\Models\Ledger;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Sale\PendingSale;
use Devzone\Pharmacy\Models\Sale\PendingSaleDetail;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Devzone\Pharmacy\Models\Sale\SaleIssuance;
use Devzone\Pharmacy\Models\Sale\UserTill;
use Devzone\Pharmacy\Models\UserLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

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
    public $credit;

    public $customer_name_credit;
    public $customer_id_credit;
    public $customer_credit_limit;
    public $customer_previous_credit;
    public $customer_id;
    public $account_id;
    public $customer_modal = false;
    public $customers = [];
    public $employees = [];
    public $doctors = [];
    public $sales_men = [];

    public $add_modal = false;
    public $add_sale_modal = false;

    public $old_user_id;
    public $user_email;
    public $user_password;

    public $patient_mr;
    public $add_patient_name;
    public $father_husband_name;
    public $patient_gender;
    public $patient_contact;
    public $patient_contact_whatsApp;
    public $patient_relation;
    public $patient_contact_2;
    public $patient_contact_3;
    public $patient_dob;
    public $patient_doctor;
    public $patient_registration_date;
    public $patient_city;
    public $patient_address;
    public $patient_referred_by;
    public $patient_age;
    public $has_contact = true;
    public $pending_sale = false;
    public $pending_and_complete = false;
    public $pending_sale_id;
    public $control_med_check = false;

    public $rounded = 0;
    public $after_round_off = 0;


    protected $listeners = ['openSearch', 'searchReferredBy', 'searchPatient', 'searchCustomer', 'emitCustomerIdCredit', 'emitProductId', 'emitPatientId', 'emitReferredById', 'saleComplete'];

    protected $validationAttributes = [
        'add_patient_name' => 'patient name'
    ];

    public function mount($admission_id = null, $procedure_id = null, $doctor_id = null, Request $request)
    {

        if (env('USER_CONFIRMATION', false)) {
            $this->sales_men = Permission::from('permissions')
                ->where('permissions.name', '12.add-sale')
                ->join('model_has_permissions as mhp', 'mhp.permission_id', '=', 'permissions.id')
                ->join('users', 'users.id', '=', 'mhp.model_id')
                ->select('permissions.id as p_id', 'users.*')->get()->toArray();

            if (!empty($request->get('old_user'))) {
                $this->old_user_id = $request->get('old_user');
            } else {
                $this->old_user_id = \auth()->id();
            }
        }

        if (auth()->user()->can('add-pending-sale')) {
            $this->pending_sale = true;
        } else {
            $this->pending_sale = false;
        }

        $this->validatePendingSale();
        $this->admission_id = $admission_id;
        $this->procedure_id = $procedure_id;
        if (!empty($this->admission_id) && !empty($this->procedure_id)) {
            if (class_exists(\App\Models\Hospital\ProcedureMedicine::class)) {

                $this->admission = true;
                $this->admission_details = AdmissionJobDetail::from('admission_job_details as a')
                    ->join('employees as e', 'e.id', '=', 'a.doctor_id')
                    ->join('admissions as ad', 'ad.id', '=', 'a.admission_id')
                    ->join('patients as p', 'p.id', '=', 'ad.patient_id')
                    ->where('a.admission_id', $admission_id)
                    ->where('a.procedure_id', $procedure_id)
                    ->where('a.doctor_id', $doctor_id)
                    ->select('p.mr_no', 'p.name', 'ad.admission_no', 'e.name as doctor', 'ad.patient_id', 'a.doctor_id')->first();

                if (!empty($this->admission_details)) {
                    $this->admission_details = $this->admission_details->toArray();
                }
                $this->patient_id = $this->admission_details['patient_id'];
                $this->referred_by_id = $this->admission_details['doctor_id'];
                $proce = DB::table('procedures')->where('id', $procedure_id)->get();
                $procedure_name = '';
                if ($proce->isNotEmpty()) {
                    $procedure_name = $proce->first()->name;
                }

                $medicines = \App\Models\Hospital\ProcedureMedicine::from('procedure_medicines as pm')
                    ->join('procedures as pro', 'pro.id', '=', 'pm.procedure_id')
                    ->join('products as p', 'p.id', '=', 'pm.product_id')
                    ->leftJoin('product_inventories as pi', function ($q) {
                        return $q->on('p.id', '=', 'pi.product_id');
                    })
                    ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                    ->where('pm.procedure_id', $this->procedure_id)
                    ->where('pi.qty', '>', 0)
                    ->select('p.name as item', 'p.retail_price as product_price', 'p.discountable', 'p.max_discount', 'p.control_medicine', 'pm.qty as required_qty',
                        'pi.qty as available_qty', 'pi.retail_price', 'pro.name as procedure_name',
                        'pi.supply_price', 'pi.id', 'p.packing', 'pi.product_id', 'p.type', 'r.name as rack', 'r.tier')
                    ->groupBy('p.id')
                    ->orderBy('pi.qty', 'desc')->get()->toArray();


                $this->admission_details['procedure_name'] = $procedure_name;
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
                        'discount_check' => $medicine['discountable'],
                        'max_discount' => $medicine['max_discount'],
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


        $this->searchable_emit_only = true;

        $this->employees = DB::table('employees')->get()->toArray();
        $this->employees = (json_decode(json_encode($this->employees), true));
    }

    public function validatePendingSale()
    {
        $input = request()->all();
        if (!empty($input['pending_sale_id'])) {
            $this->pending_sale_id = $input['pending_sale_id'];
            $sales = PendingSale::from('pending_sales as ps')
                ->join('pending_sale_details as psd', 'ps.id', '=', 'psd.sale_id')
                ->join('products as p', 'p.id', 'psd.product_id')
                ->join('product_inventories as pi', 'p.id', '=', 'pi.product_id')
                ->where('ps.id', $this->pending_sale_id)
                ->select('psd.product_id', 'psd.qty as s_qty', 'ps.sale_by', 'pi.supply_price', 'psd.total_after_disc', 'psd.total', 'p.name as item', 'p.packing', 'p.control_medicine', 'p.retail_price as product_price', 'p.cost_of_price as product_supply_price', 'p.discountable', 'p.max_discount', 'p.type', 'psd.retail_price', 'psd.disc', 'ps.patient_id', 'ps.referred_by')
                ->groupBy('p.id')
                ->get();

            foreach ($sales as $key => $sale) {
                $this->sales[] = $sale;
                if ($sale->control_medicine == 't') {
                    $this->control_med_check = true;
                    $sale->control_medicine = true;
                } else {
                    $sale->control_medicine = false;
                }
            }
            if (!empty($sales[0]['patient_id'])) {
                $patient = Patient::find($sales[0]['patient_id']);
                $this->patient_id = $patient['id'];
                $this->patient_name = $patient['name'];
            }

            if (!empty($sales[0]['referred_by'])) {
                $doctor = Employee::find($sales[0]['referred_by']);
                $this->referred_by_id = $doctor['id'];
                $this->referred_by_name = $doctor['name'];
            }

        }
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
            $this->customer_id = $data['customer_id'];
            $this->account_id = $data['account_id'];
            $this->searchableReset();
        }
        $this->credit = false;
        $this->error = '';

        $this->reset('customer_previous_credit', 'customer_credit_limit');

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

                $data['s_qty'] = $this->product_qty > $data['qty'] ? $data['qty'] : $this->product_qty;
                $data['disc'] = 0;
                $data['total'] = $data['retail_price'] * $data['s_qty'];
                $data['total_after_disc'] = $data['retail_price'] * $data['s_qty'];

                if ($data['control_medicine'] == 't') {
                    $this->control_med_check = true;
                    $data['control_medicine'] = true;
                } else {
                    $data['control_medicine'] = false;
                }
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
            $this->searchable_query = '';
            $this->searchable_data = [];
            $this->product_qty = null;
            $this->emit('focusInput');
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

    public function searchCustomer()
    {
        $this->searchableOpenModal('customer_id_credit', 'customer_name_credit', 'customer');
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


                $discount = 0;
                if ($this->sales[$array[1]]['disc'] > 0 && $this->sales[$array[1]]['disc'] <= 100) {
                    if ($this->sales[$array[1]]['discountable'] == 't') {
                        if (!empty($this->sales[$array[1]]['max_discount'])) {
                            if ($this->sales[$array[1]]['disc'] >= $this->sales[$array[1]]['max_discount']) {
                                $this->sales[$array[1]]['disc'] = $this->sales[$array[1]]['max_discount'];
                            }

                            $discount = round(($this->sales[$array[1]]['disc'] / 100) * $this->sales[$array[1]]['total'], 2);
                        } else {
                            $this->sales[$array[1]]['disc'] = 0;
                        }

                    } else {
                        $this->sales[$array[1]]['disc'] = 0;
                    }
                    $this->sales[$array[1]]['total_after_disc'] = $this->sales[$array[1]]['total'] - $discount;
                } else {
                    $this->sales[$array[1]]['disc'] = 0;
                    $this->sales[$array[1]]['total_after_disc'] = $this->sales[$array[1]]['total'] - $discount;
                }
            }
        }

    }

    public function updatedDiscount($value)
    {
        if (empty($value) || !is_numeric($value) || $value < 0 || $value > 100) {
            $this->discount = 0;
            $value = 0;
        }

        if ($value != 0) {
            foreach ($this->sales as $key => $s) {
                if ($s['discountable'] == 't') {
                    $disc = 0;
                    if (!empty($s['max_discount'])) {
                        if ($value >= $s['max_discount']) {
                            $disc = $s['max_discount'];
                        } else {
                            $disc = $value;
                        }
                    }

                    $discount = round(($disc / 100) * $this->sales[$key]['total'], 2);
                    $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'] - $discount;
                    $this->sales[$key]['disc'] = $disc;
                }
            }
        }

    }

    public function updatedReceived($value)
    {
        if ($value < 0 || $value > 10000000) {
            $this->received = 0;
        }
        if (empty($value) || !is_numeric($value)) {
            $this->received = 0;
        }

        if (empty($this->credit) && env('ROUNDOFF_CHECK', false) && collect($this->sales)->sum('total_after_disc') >= env('MIMIMUM_ROUNDOFF_BILL', 50)) {
            $this->payable = $this->received - round(collect($this->sales)->sum('total_after_disc') / 5) * 5;
        } else {
            $this->payable = $this->received - collect($this->sales)->sum('total_after_disc');
        }


    }

    public function removeEntry($key)
    {
        unset($this->sales[$key]);
    }

    public function updatedCredit($val)
    {
        $this->received = 0;
        $this->payable = 0;
        if ($val == true) {
            if (empty($this->patient_id)) {
                $this->error = 'Please select patient first to proceed credit sale.';
                $this->credit = false;
            } else {
                $this->error = '';
                $this->resetErrorBag();
                if (empty($this->customer_id)) {
                    $this->customer_modal = true;
                } else {
                    $this->checkCustomerBalances();
                }
            }
        }
    }

    public function checkCustomerBalances()
    {

        $customer_account = Customer::find($this->customer_id);
        $this->customer_credit_limit = $customer_account->credit_limit;
        $previous_credit = Ledger::where('account_id', $customer_account->account_id)
            ->groupBy('account_id')
            ->select(DB::raw('sum(debit-credit) as balance'))
            ->first();
        $this->customer_previous_credit = !empty($previous_credit) ? $previous_credit->balance : 0;

    }

    public function addCreditor()
    {
        $validatedData = $this->validate([
            'patient_id' => 'required|integer',
            'customers.care_of' => 'required',
            'customers.credit_limit' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $account_id = ChartOfAccount::instance()->createCustomerAccount('Receivable ' . ucwords($this->patient_name));
            $customer = Customer::create([
                'name' => ucwords($this->patient_name),
                'employee_id' => $this->customers['care_of'],
                'credit_limit' => $this->customers['credit_limit'],
                'account_id' => $account_id
            ]);

            Patient::find($this->patient_id)->update([
                'customer_id' => $customer->id,
                'account_id' => $account_id
            ]);

            $this->customer_id = $customer->id;
            $this->account_id = $account_id;

            DB::commit();
            $this->checkCustomerBalances();
            $this->customer_modal = false;

            $this->reset('customers');
        } catch (\Exception $e) {
            $this->addError('customers.care_of', $e->getMessage());
            DB::rollBack();
        }
    }


    public function confirmUser()
    {
        if (empty($this->user_email) || empty($this->user_password)) {
            $this->addError('login', 'Please enter your credentials to proceed.');
        }

        // auth::once only authenticates user for single request so in case we close the modal after validation it goes back to old login
        if (!Auth::once(['email' => $this->user_email, 'password' => $this->user_password])) {
            $this->addError('login', 'The provided credentials do not match our records.');
        }

    }


    public function closeSaleModal()
    {
        $this->reset('user_password');
        $this->add_sale_modal = false;
    }



    public function reloginUser()
    {
        if (!\auth()->loginUsingId($this->old_user_id)) {
            return new \Exception('An Unknown Error Occurred.');
        }

    }


    public function pendingSaleComplete()
    {
        $this->pending_and_complete = true;
        $this->saleComplete();
    }

    public function saleComplete()
    {
        try {
            if (!$this->admission) {
                if ($this->control_med_check && empty($this->patient_id)) {
                    throw new \Exception('Patient information is required.');
                }
            }
            $total_discount_give = 0;
            DB::beginTransaction();
            if (empty($this->sales)) {
                throw new \Exception('Unable to complete because invoice is empty.');
            }
            $this->reset(['success']);

            foreach ($this->sales as $key => $s) {
                if ($s['discountable'] == 't') {
                    $disc = 0;
                    if (!empty($s['max_discount'])) {
                        if ($this->sales[$key]['disc'] >= $s['max_discount']) {
                            $disc = $s['max_discount'];
                        } else {
                            $disc = $this->sales[$key]['disc'];
                        }
                    }

                    $discount = round(($disc / 100) * $this->sales[$key]['total'], 2);
                    $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'] - $discount;
                    $this->sales[$key]['disc'] = $disc;
                    $packing = 1;
                    if (!empty($s['packing'])) {
                        $packing = $s['packing'];
                    }
                    $after_discount_retail = ($this->sales[$key]['total_after_disc']) / $s['s_qty'];
                    if (round($after_discount_retail, 2) < round($s['product_supply_price'] / $packing, 2)) {
                        $avail_pe = round($s['product_supply_price'] / $s['product_price'], 4);
                        $avail_per = round((1 - $avail_pe) * 100, 4);

                        throw new \Exception('Maximum discount possible ' . $avail_per . '% on ' . $s['item']);
                    }

                    $total_discount_give = $total_discount_give + $discount;
                }
            }

            if (auth()->user()->can('add-pending-sale') || $this->pending_and_complete) {

                $pending_sale_id = PendingSale::create([
                    'patient_id' => $this->patient_id,
                    'referred_by' => $this->referred_by_id,
                    'sale_by' => Auth::id(),
                    'sale_at' => date('Y-m-d H:i:s'),
                    'sub_total' => collect($this->sales)->sum('total'),
                    'gross_total' => collect($this->sales)->sum('total_after_disc'),
                ])->id;

                foreach ($this->sales as $s) {

                    $total = $s['retail_price'] * $s['s_qty'];

                    $discount = round(($s['disc'] / 100) * $total, 2);
                    $after_total = $total - $discount;

                    PendingSaleDetail::create([
                        'sale_id' => $pending_sale_id,
                        'product_id' => $s['product_id'],

                        'qty' => $s['s_qty'],
                        'supply_price' => $s['supply_price'],
                        'retail_price' => $s['retail_price'],
                        'total' => $total,
                        'disc' => $s['disc'],
                        'total_after_disc' => $after_total,
                        'retail_price_after_disc' => $after_total / $s['s_qty']
                    ]);

                }


                $this->resetAll();
                $this->searchableReset();
                $this->success = 'Sale has been added to pending list.';
                DB::commit();
                $this->pending_and_complete = false;
                return;
            }


            //Here should be the conditions
            if (env('USER_CONFIRMATION', false)) {
                $this->add_sale_modal = true;
                $this->confirmUser();
                if($this->getErrorBag()->has('login')){
                    throw new \Exception();
                }

                if (auth()->user()->can('add-pending-sale')){
                    $this->addError('login', 'User does not have permission for a complete sale.');
                    throw new \Exception();
                }

            }


            if (empty($this->credit)) {
                if (empty($this->received) && $this->admission == false) {
                    throw new \Exception('Please enter received amount.');
                }
                //Round off code
                $this->after_round_off = collect($this->sales)->sum('total_after_disc'); //gross-price
                $min_bill = env('MIMIMUM_ROUNDOFF_BILL', 50);

                if (env('ROUNDOFF_CHECK', false) && $this->after_round_off > $min_bill) {
                    $v1 = $this->after_round_off;
                    $v2 = round($v1 / 5) * 5;
                    $this->rounded = $v2 - $v1;
                    $this->after_round_off = $v2;
                }
//                dump($this->after_round_off, $this->rounded);
                if ($this->admission == false && $this->received < $this->after_round_off) {
                    throw new \Exception('Received amount should be greater than PKR ' . $this->after_round_off . "/-");
                }
            } else {
                if (empty($this->customer_id)) {
                    throw new \Exception('Please select patient to credit sale.');
                }

                if (!auth()->user()->can('12.add-credit-sale')) {
                    throw new \Exception('You dont have permission for credit sale');
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
                if (collect($this->sales)->sum('total_after_disc') + $previous_balance > $customer_account->credit_limit) {
                    throw new \Exception('Credit limit exceeding: Available balance is PKR ' . number_format($this->customer_previous_credit));
                }
                $user_limit = UserLimit::where('user_id', Auth::id())
                    ->where('date', date('Y-m-d'))
                    ->first();
                $balance = !empty($user_limit) ? $user_limit->balance : 0;

                if ((collect($this->sales)->sum('total_after_disc')) + $balance > Auth::user()->credit_limit) {
                    throw new \Exception('Amount exceeding User credit limit (PKR ' . number_format(Auth::user()->credit_limit) . ')');
                }
                $credit_amount = collect($this->sales)->sum('total_after_disc');
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

            if ($this->admission == true) {
                if (empty($this->handed_over)) {
                    throw new \Exception('Handed over field is required.');
                }
            }

            $sale_receipt_no = Voucher::instance()->advances()->get();
            $sub_total = collect($this->sales)->sum('total');
            $total_after_disc = collect($this->sales)->sum('total_after_disc');
            if (round($sub_total - $total_after_disc, 2) > round($total_discount_give, 2)) {
                throw new \Exception('Total discount is more than as you given to customer. Please recheck sale.');
            }
            //dd(round($sub_total - $total_after_disc, 2), round($total_discount_give, 2));

            $sale_id = Sale::create([
                'patient_id' => $this->patient_id,
                'referred_by' => $this->referred_by_id,
                'sale_by' => !empty($this->pending_sale_id) ? $this->sales[0]['sale_by'] : Auth::id(),
                'sale_at' => date('Y-m-d H:i:s'),
                'remarks' => $this->remarks,
                'receive_amount' => $this->received,
                'payable_amount' => !empty($this->credit) ? 0 : $this->payable,
                'rounded_inc' => $this->rounded > 0 ? abs($this->rounded) : null,
                'rounded_dec' => $this->rounded < 0 ? abs($this->rounded) : null,
                'sub_total' => collect($this->sales)->sum('total'),
                'gross_total' => collect($this->sales)->sum('total_after_disc'),
                'admission_id' => $this->admission_id ?? null,
                'procedure_id' => $this->procedure_id ?? null,
                'receipt_no' => $sale_receipt_no,
                'customer_id' => $this->customer_id ?? null,
                'is_credit' => !empty($this->credit) ? 't' : 'f',
                'is_paid' => !empty($this->credit) ? 'f' : 't',
                'on_account' => !empty($this->credit) ? $total_after_disc : 0,
            ])->id;


            foreach ($this->sales as $s) {
                $inv = ProductInventory::where('product_id', $s['product_id'])
                    ->where('qty', '>', 0)->orderBy('id', 'asc')->get();

                if ($inv->sum('qty') < $s['s_qty']) {
                    throw new \Exception('System does not have much inventory for the item ' . $s['item']);
                }

                $sale_qty = $s['s_qty'];
                foreach ($inv as $i) {
                    if ($sale_qty > 0) {
                        $dec = 0;
                        $product_inv = ProductInventory::find($i->id);
                        if (round($product_inv->supply_price, 2) > round($s['retail_price'], 2)) {
                            throw new \Exception('You are selling ' . $s['item'] . ' at loss.');
                        }
                        if (round($product_inv->retail_price, 2) != round($s['retail_price'], 2)) {
                            throw new \Exception('The retail price of ' . $s['item'] . ' is not correct.');
                        }
                        if ($sale_qty <= $product_inv->qty) {
                            $dec = $sale_qty;
                            $product_inv->decrement('qty', $sale_qty);
                            InventoryLedger::create([
                                'product_id' => $product_inv->product_id,
                                'order_id' => $product_inv->po_id,
                                'decrease' => $sale_qty,
                                'type' => 'sale',
                                'description' => "Sale on dated " . date('d M, Y H:i:s') .
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
                                'type' => 'sale',
                                'description' => "Sale on dated " . date('d M, Y H:i:s') .
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
                            'retail_price_after_disc' => $after_total / $dec
                        ]);
                    }
                }
            }
            $accounts = COA::whereIn('reference', ['pharmacy-inventory-5', 'income-pharmacy-5', 'cost-of-sales-pharmacy-5', 'exp-invoice-rounding-off'])->get();

            $amounts = SaleDetail::where('sale_id', $sale_id)->select(DB::raw('SUM(total_after_disc) as sale'), DB::raw('SUM(qty * supply_price) as cost'))->first();
            $customer_name = $this->patient_name ?? 'walking customer';
            $description = "Being goods worth PKR " . number_format($amounts['sale'], 2) . " receipt # {$sale_id} & invoice # inv-{$sale_receipt_no} sold to Patient {$customer_name}. Cash received PKR " .
                number_format($amounts['sale'], 2) . " on " . date('d M, Y') . " by " . Auth::user()->name . " at " . date('h:i A');

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
                    $old_sale = \App\Models\Hospital\AdmissionPaymentDetail::from('admission_payment_details as apd')->where('apd.admission_id', $this->admission_id)
                        ->where('apd.procedure_id', $this->procedure_id)
                        ->where('apd.medicines', 't')->first();

                    $price = 0;
                    if (!empty($old_sale)) {
                        $price = $old_sale['amount'];
                    }

                    \App\Models\Hospital\AdmissionPaymentDetail::from('admission_payment_details as apd')->where('apd.admission_id', $this->admission_id)
                        ->where('apd.procedure_id', $this->procedure_id)
                        ->where('apd.medicines', 't')
                        ->update([
                            'sale_id' => $sale_id,
                            'amount' => $price + $amounts->sale,
                        ]);

                    $ipd_medicine_account = COA::where('reference', 'payable-medicine-5')->first();

                    $description = "Being goods worth PKR " . number_format($amounts['sale'], 2) .
                        " receipt # {$sale_id} & invoice # inv-{$sale_receipt_no} issued against admission # " . $admission_details->admission_no . " and procedure " . $admission_details->procedure_name . ". Account " . $ipd_medicine_account->name . " debited with PKR " .
                        number_format($amounts['sale'], 2) . " on " . date('d M, Y') . " by " . Auth::user()->name;
                    GeneralJournal::instance()->account($ipd_medicine_account->id)->debit($amounts['sale'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
            }

            if ($this->admission == false) {
                if (!empty($this->credit)) {
                    $description = "Being goods worth PKR " . number_format($amounts['sale'], 2) . " receipt # {$sale_id} & invoice # inv-{$sale_receipt_no} sold to Patient {$customer_name}.  on Account {$customer_account->name} : PKR " .
                        number_format($amounts['sale'], 2) . " on " . date('d M, Y') . " by " . Auth::user()->name . " at " . date('h:i A');
                    GeneralJournal::instance()->account($customer_account->account_id)->debit($amounts['sale'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                } else {
//                    Rounding off changes are here!
                    $min_bill = env('MIMIMUM_ROUNDOFF_BILL', 50);
                    $rounded = 0;
                    if (env('ROUNDOFF_CHECK', false) && $amounts['sale'] > $min_bill) {
                        $v1 = $amounts['sale'];
                        $v2 = round($v1 / 5) * 5;
                        $rounded = $v2 - $v1;
                        $round_acc_id = $accounts->where('reference', 'exp-invoice-rounding-off')->first();
                        if (empty($round_acc_id)) {
                            throw new \Exception('Invoice Round Off Account not found!');
                        }
                        $round_acc_id = $round_acc_id->id;

                        if ($rounded < 0) {

                            GeneralJournal::instance()->account($round_acc_id)->debit(abs($rounded))->voucherNo($vno)
                                ->date(date('Y-m-d'))->approve()->reference('exp-invoice-rounding-off')->description($description)->execute();

                        } elseif ($rounded > 0) {

                            GeneralJournal::instance()->account($round_acc_id)->credit(abs($rounded))->voucherNo($vno)
                                ->date(date('Y-m-d'))->approve()->reference('exp-invoice-rounding-off')->description($description)->execute();
                        }
                    }

//                    Idr tak...

                    GeneralJournal::instance()->account(Auth::user()->account_id)->debit($amounts['sale'] + $rounded)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }

            }

            foreach ($accounts as $a) {
                if ($a->reference == 'pharmacy-inventory-5') {
                    GeneralJournal::instance()->account($a->id)->credit($amounts['cost'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
                if ($a->reference == 'income-pharmacy-5') {
                    GeneralJournal::instance()->account($a->id)->credit($amounts['sale'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
                if ($a->reference == 'cost-of-sales-pharmacy-5') {
                    GeneralJournal::instance()->account($a->id)->debit($amounts['cost'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
            }
            if ($this->admission == true) {
                SaleIssuance::create([
                    'sale_id' => $sale_id,
                    'handed_over_to' => $this->handed_over,
                ]);
            }
            if (!empty($this->pending_sale_id)) {
                PendingSale::where('id', $this->pending_sale_id)->delete();
                PendingSaleDetail::where('sale_id', $this->pending_sale_id)->delete();
            }
            $this->resetAll();
            $this->searchableReset();
            $this->success = 'Sale has been complete with receipt #' . $sale_id;

            DB::commit();
            $this->emit('printInvoice', $sale_id, $this->admission_id, $this->procedure_id);
            if (env('USER_CONFIRMATION', false)) {
                $this->add_sale_modal = false;
                $this->reloginUser();
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            DB::rollBack();
        }
    }

    public function resetAll()
    {
        $this->reset(['sales', 'referred_by_id', 'pending_sale_id', 'referred_by_name', 'success', 'patient_id', 'patient_name', 'customer_credit_limit',
            'payable', 'received', 'remarks', 'discount', 'error', 'customer_id_credit', 'customer_id', 'account_id', 'customer_previous_credit', 'customer_name_credit', 'credit']);
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

    public function openAddModel()
    {
        $this->resetErrorBag();
        $this->reset('success');
        $this->patient_contact_whatsApp = 't';
        $this->searchable_modal = false;
        $this->patient_registration_date = date('Y-m-d');
        $last_patient = Patient::orderBy('id', 'DESC')->first();
        $this->patient_mr = 'MR' . str_pad($last_patient->id + 1, 6, '0', STR_PAD_LEFT);
        $this->doctors = Employee::where('is_doctor', 't')->where('status', 't')->get()->toArray();
        $this->add_modal = true;
    }


    public function hasContact()
    {
        if ($this->has_contact) {
            $this->has_contact = false;
        } else {
            $this->has_contact = true;
        }
    }

    public function updatedPatientDob($val)
    {
        $this->patient_age = Carbon::parse($val)->diff(\Carbon\Carbon::now())->format('%y');
    }

    public function updatedPatientAge($val)
    {

        if (!empty($val)) {
            $age = '-' . $val . ' year';
            $this->patient_dob = date('Y-m-d', strtotime($age));
        }
    }

    public function addPatient()
    {
        $validation = [
            'add_patient_name' => 'required',
            'father_husband_name' => 'required',
            'patient_gender' => 'required',
        ];
        if ($this->has_contact) {
            $validation['patient_contact'] = 'required';
        }
        $this->validate($validation);

        $last_patient = Patient::orderBy('id', 'DESC')->first();
        $this->patient_mr = 'MR' . str_pad($last_patient->id + 1, 6, '0', STR_PAD_LEFT);
        $created_patient = Patient::create([
            'mr_no' => $this->patient_mr ?? null,
            'name' => $this->add_patient_name,
            'father_husband_name' => $this->father_husband_name ?? null,
            'gender' => $this->patient_gender ?? null,
            'phone' => $this->patient_contact ?? null,
            'contact_relation' => $this->patient_relation ?? null,
            'has_whatsApp' => $this->patient_contact_whatsApp ?? 'f',
            'phone_2' => $this->patient_contact_2 ?? null,
            'phone_3' => $this->patient_contact_3 ?? null,
            'dob' => $this->patient_dob ?? null,
            'age' => $this->patient_age ?? null,
            'doctor_id' => $this->patient_doctor ?? null,
            'registration_date' => $this->patient_registration_date ?? date('Y-m-d'),
            'address' => $this->patient_address ?? null,
            'referred_by' => $this->patient_referred_by ?? null,
            'created_by' => auth()->user()->id,
            'city' => $this->patient_city ?? null,
        ]);
        $this->patient_id = $created_patient->id;
        $this->patient_name = $created_patient->mr_no . ' - ' . $created_patient->name;
        $this->referred_by_id = $created_patient->doctor_id;
        $this->referred_by_name = collect($this->doctors)->where('id', $this->referred_by_id)->first()['name'] ?? null;
        $this->add_modal = false;
        $this->patient_contact_whatsApp = 't';

        $this->reset('add_patient_name', 'credit', 'customer_id', 'account_id', 'customer_previous_credit', 'customer_credit_limit', 'father_husband_name', 'patient_gender', 'patient_contact', 'patient_contact_2', 'patient_contact_3', 'patient_relation', 'patient_dob', 'patient_age', 'patient_doctor', 'patient_registration_date', 'patient_address', 'patient_city', 'patient_referred_by');
    }
}
