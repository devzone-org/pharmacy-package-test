<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
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

    protected $listeners = ['openSearch', 'emitProductId', 'emitPatientId', 'emitReferredById', 'saleComplete'];

    public function mount()
    {
        $this->tills = ChartOfAccount::from('chart_of_accounts as p')
            ->join('chart_of_accounts as c', 'p.id', '=', 'c.sub_account')
            ->where('p.reference', 'cash-at-pharmacy-tills-4')->get()->toArray();
        $till = UserTill::where('user_id', Auth::id())->first();
        if (!empty($till)) {
            $this->till_id = $till['account_id'];
            $till_name = collect($this->tills)->firstWhere('id', $till['account_id']);
            $this->till_name = $till_name['name'];
        }

        $this->searchable_emit_only = true;
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

            if (empty($check)) {
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
            if (empty($this->sales)) {
                throw new \Exception('Invoice is empty.');
            }
            if (empty($this->till_id)) {
                throw new \Exception('To complete sale you must choose till.');
            }
            DB::beginTransaction();
            $sale_id = Sale::create([
                'patient_id' => $this->patient_id,
                'referred_by' => $this->referred_by_id,
                'sale_by' => Auth::id(),
                'sale_at' => date('Y-m-d H:i:s'),
                'remarks' => $this->remarks,
                'receive_amount' => $this->received,
                'payable_amount' => $this->payable,
                'sub_total' => collect($this->sales)->sum('total'),
                'gross_total' => collect($this->sales)->sum('total_after_disc')
            ])->id;

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
            $accounts = ChartOfAccount::whereIn('reference',['pharmacy-inventory-5','income-pharmacy-5','cost-of-sales-pharmacy-5'])->get();

            $amounts = SaleDetail::where('sale_id', $sale_id)->select(DB::raw('SUM(total_after_disc) as sale'),DB::raw('SUM(qty * supply_price) as cost'))->first();
            $description = "Being goods worth PKR " . number_format($amounts['sale'], 2) . " sold to the walking customer. Cash received PKR " .
                number_format($amounts['sale'], 2) . " on " . date('d M, Y') . " by " . Auth::user()->name;

            $vno = Voucher::instance()->voucher()->get();
            GeneralJournal::instance()->account($this->till_id)->debit($amounts['sale'])->voucherNo($vno)
                ->date(date('Y-m-d'))->approve()->description($description)->execute();

            foreach ($accounts as $a){
                if($a->reference == 'pharmacy-inventory-5'){
                    GeneralJournal::instance()->account($a->id)->credit($amounts['cost'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
                if($a->reference == 'income-pharmacy-5'){
                    GeneralJournal::instance()->account($a->id)->credit($amounts['sale'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
                if($a->reference == 'cost-of-sales-pharmacy-5'){
                    GeneralJournal::instance()->account($a->id)->debit($amounts['cost'])->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
            }


            $this->resetAll();
            $this->searchableReset();
            $this->success = 'Sale has been complete with receipt #' . $sale_id;
            DB::commit();
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
