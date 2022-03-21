<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Devzone\Pharmacy\Models\Sale\OpenReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OpenReturnsAdd extends Component
{
    use Searchable;

    public $date;
    public $products_modal = false;
    public $search_products;
    public $product_data = [];
    public $returns = [];
    public $deduction = 0;
    public $total_after_deduction;

    public $success;

    protected $rules = [
        'deduction' => 'required|numeric|between:0,100',
        'returns' => 'required',
        'returns.*.qty' => 'required|integer|gt:0',
        'returns.*.expiry' => 'required|date|after:today'
    ];

    protected $validationAttributes = [
        'returns' => 'Products',
    ];

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function render()
    {
        return view('pharmacy::livewire.sales.open-returns-add');
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
            DB::beginTransaction();
            foreach ($this->returns as $k => $pro){
                $inv = ProductInventory::create([
                    'product_id' => $pro['id'],
                    'qty' => $pro['qty'],
                    'retail_price' => $pro['retail_price'],
                    'supply_price' => 0.00,
                    'expiry' => $pro['expiry'],
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

                OpenReturn::create([
                    'product_id' => $pro['id'],
                    'expiry' => $pro['expiry'],
                    'qty' => $pro['qty'],
                    'retail_price' => $pro['retail_price'],
                    'total' => $pro['total_cost'],
                    'deduction' => $this->deduction,
                    'total_after_deduction' => $pro['total_cost'] - ($pro['total_cost'] * $this->deduction/100),
                    'added_by' => \auth()->id(),
                ]);
            }

            // Account entries remain.
            DB::commit();
            $this->success = 'Product inventory updated successfully.';
            $this->reset(['returns', 'date', 'deduction', 'total_after_deduction']);
        } catch (\Exception $e) {
            $this->addError('error', $e->getMessage());
            DB::rollBack();
        }
    }
}
