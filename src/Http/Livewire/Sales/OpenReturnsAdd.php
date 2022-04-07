<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Devzone\Ams\Helper\ChartOfAccount;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount as COA;
use Devzone\Ams\Models\Ledger;
use Devzone\Pharmacy\Models\Sale\OpenReturn;
use Devzone\Pharmacy\Models\Sale\OpenReturnDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OpenReturnsAdd extends Component
{
    use Searchable;

    public $date;
    public $remarks;
    public $products_modal = false;
    public $search_products;
    public $product_data = [];
    public $returns = [];
    public $deduction = 0;
    public $total_after_deduction;
    public $open_return_data;

    public $is_view = false;
    public $success;

    protected $rules = [
        'deduction' => 'required|numeric|between:0,100',
        'returns' => 'required',
        'returns.*.qty' => 'required|integer|gt:0',
        'returns.*.expiry' => 'required|date|after:today',
        'remarks' => 'required|string',
    ];

    protected $validationAttributes = [
        'returns' => 'Products',
        'deduction' => 'Deduction %',
        'remarks' => 'Remarks',
    ];

    public function mount($id = null)
    {
        $data = OpenReturn::find($id);
        if (!empty($data)){
            $this->open_return_data = OpenReturn::from('open_returns as or')
                ->where('or.id', $id)
                ->join('open_return_details as ord', 'ord.open_return_id', '=', 'or.id')
                ->join('products as p', 'p.id', '=', 'ord.product_id')
                ->join('users as u', 'u.id', '=', 'or.added_by')
                ->select('p.name', 'p.salt', 'ord.*', 'or.remarks', 'or.total as grand_total', 'or.total_after_deduction as grand_total_after_deduction', 'u.name as added_by')
                ->get()->toArray();
            if (!empty($this->open_return_data)){
                $this->is_view = true;
            }
        }
        $this->date = date('Y-m-d');
    }



    public function render()
    {
        return view('pharmacy::livewire.sales.open-returns-add');
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function openProductModal()
    {
        $this->products_modal = true;
        $this->emit('focusProductInput');
    }

    public function removeProduct($key)
    {
        if (isset($this->returns[$key])) {
            unset($this->returns[$key]);
        }
    }

    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'returns') {
            if (!in_array($array[2], ['salt', 'expiry'])) {
                if (!is_numeric($value) || empty($value)) {
                    if ($array[2] == 'qty') {
                        $this->returns[$array[1]][$array[2]] = 1;
                    } else {
                        $this->returns[$array[1]][$array[2]] = 0;
                    }
                }
                if (in_array($array[2], ['qty'])) {
                    $this->returns[$array[1]]['total_cost'] = round($this->returns[$array[1]]['qty'] * $this->returns[$array[1]]['retail_price'], 2);
                }
            }
        }
    }

    public function updatedDeduction($value)
    {
        if (!is_numeric($value) || empty($value) || $value > 100) {
            $this->deduction = 0;
        }
    }


    public function updatedSearchProducts($value)
    {
        if (strlen($value) > 1) {
            $this->highlight_index = 0;
            $search = Product::from('products as p')
                ->where('p.status', 't')
                ->where(function ($q) use ($value) {
                    return $q->orWhere('p.name', 'LIKE', '%' . $value . '%')
                        ->orWhere('p.salt', 'LIKE', '%' . $value . '%');
                })
                ->limit(15)
                ->get();
            if ($search->isNotEmpty()) {
                $this->product_data = $search->toArray();
            } else {
                $this->product_data = [];
            }

        } else {
            $this->product_data = [];
        }
    }

    public function selectProduct($key = null)
    {
        if (!empty($key)) {
            $this->highlight_index = $key;
        }
        $data = $this->product_data[$this->highlight_index] ?? null;
        if (!empty($data)) {
            $existing = collect($this->returns)->where('id', $data['id'])->all();
            if (empty($existing)) {
                $this->returns[] = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'qty' => 1,
                    'cost_of_price' => $data['cost_of_price'],
                    'retail_price' => $data['retail_price'] / $data['packing'],
                    'salt' => $data['salt'],
                    'total_cost' => $data['retail_price'] / $data['packing'],
                    'packing' => $data['packing']
                ];
            } else {
                $key = array_keys($existing)[0];
                $qty = $this->returns[$key]['qty'];
                $this->returns[$key]['qty'] = $qty + 1;
                $this->returns[$key]['total_cost'] = $this->returns[$key]['qty'] * $this->returns[$key]['retail_price'];
            }
        }
    }

    public function create()
    {
        $this->validate();
        $total = collect($this->returns)->sum('total_cost');
        $this->total_after_deduction = $total - ($total*$this->deduction/100);


        try {
            $vno = Voucher::instance()->voucher()->get();
            $accounts = COA::whereIn('reference', ['pharmacy-inventory-5', 'income-return-pharmacy-5', 'cost-of-sales-pharmacy-5'])->get();
            DB::beginTransaction();

            $or = OpenReturn::create([
                'remarks' => $this->remarks,
                'total' => $total,
                'total_after_deduction' => $this->total_after_deduction,
                'added_by' => \auth()->id(),
                'voucher' => $vno,
            ]);


            foreach ($this->returns as $k => $pro){
                $inv = ProductInventory::create([
                    'product_id' => $pro['id'],
                    'qty' => $pro['qty'],
                    'retail_price' => $pro['retail_price'],
                    'supply_price' => 0.00,
                    'expiry' => $this->formatDate($pro['expiry']),
                    'type' => 'open-return'
                ]);


                $des = "Open Return of PKR " . number_format($pro['total_cost'],2) . "/- on date " . date('d M, Y H:i:s') .
                    " with " . $this->deduction . "% deduction and total of PKR " . number_format($this->total_after_deduction,2) . "/-.";
                InventoryLedger::create([
                    'product_id' => $pro['id'],
                    'increase' => $pro['qty'],
                    'type' => 'open-return',
                    'description' => $des
                ]);

                OpenReturnDetail::create([
                    'open_return_id' => $or->id,
                    'product_id' => $pro['id'],
                    'expiry' => $this->formatDate($pro['expiry']),
                    'qty' => $pro['qty'],
                    'retail_price' => $pro['retail_price'],
                    'total' => $pro['total_cost'],
                    'deduction' => $this->deduction,
                    'total_after_deduction' => $pro['total_cost'] - ($pro['total_cost'] * $this->deduction/100),
                ]);
            }


            $description = "Open Return of PKR " . number_format($total,2) . "/- issued against ID# " . $or->id . " with " . $this->deduction .
                "% deduction and total of PKR " . number_format($this->total_after_deduction,2) . "/- on date ". date('d M, Y H:i:s')." by ". Auth::user()->name.".";

            // Account entries.
            GeneralJournal::instance()->account(Auth::user()->account_id)->credit($this->total_after_deduction)->voucherNo($vno)
                ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
            foreach ($accounts as $a) {
                if ($a->reference == 'pharmacy-inventory-5') {
                    GeneralJournal::instance()->account($a->id)->debit($total)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
                if ($a->reference == 'income-return-pharmacy-5') {
                    GeneralJournal::instance()->account($a->id)->debit($this->total_after_deduction)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
                if ($a->reference == 'cost-of-sales-pharmacy-5') {
                    GeneralJournal::instance()->account($a->id)->credit($total)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->reference('pharmacy')->description($description)->execute();
                }
            }

            DB::commit();
            $this->success = 'Product inventory updated successfully.';
            $this->reset(['returns', 'date', 'deduction', 'total_after_deduction', 'remarks']);
        } catch (\Exception $e) {
            $this->addError('error', $e->getMessage());
            DB::rollBack();
        }
    }
}
