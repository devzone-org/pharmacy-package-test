<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseAdd extends Component
{
    use Searchable;

    public $supplier_id;
    public $product_qty;
    public $loose_purchase = 'f';
    public $supplier_name;
    public $delivery_date;
    public $expected_date;
    public $supplier_invoice;
    public $products_modal = false;
    public $search_products;
    public $product_data = [];
    public $order_list = [];
    public $sale_days = '10';
    public $demand_check = false;

    public $success;
    protected $listeners = ['emitSupplierId'];

    protected $rules = [
        'supplier_id' => 'required|integer',
        'expected_date' => 'required|date',
        'supplier_invoice' => 'nullable|string',
        'order_list' => 'required',
        'order_list.*.qty' => 'required|integer|gte:0',
        'order_list.*.salt' => 'nullable|string',
        'order_list.*.cost_of_price' => 'required|numeric|gte:0',
        'order_list.*.retail_price' => 'required|numeric|gte:0',
    ];
    protected $messages = [
        'order_list.*.cost_of_price.required' => 'The cost price is required.',
        'order_list.*.cost_of_price.numeric' => 'The cost price must be a valid number.',
        'order_list.*.cost_of_price.min' => 'The cost price must be greater than zero.',
        'order_list.*.retail_price.required' => 'The retail price is required.',
        'order_list.*.retail_price.numeric' => 'The retail price must be a valid number.',
        'order_list.*.retail_price.min' => 'The retail price must be greater than zero.',
        'order_list.*.qty.required' => 'The quantity is required.',
        'order_list.*.qty.integer' => 'The quantity must be an integer.',
        'order_list.*.qty.gte' => 'The quantity must be greater than or equal to zero.'
    ];

    protected $validationAttributes = [
        'supplier_id' => 'supplier',
        'order_list' => 'Products',
    ];

    public function mount()
    {
        if (\request()->loose_purchase == 't') {
            $this->loose_purchase = 't';
        }

        $this->expected_date = date('d M Y');
    }

    public function render()
    {
        return view('pharmacy::livewire.purchases.purchase-add');
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }

    public function emitSupplierId()
    {

    }

    public function updatedSaleDays()
    {
        $this->validate(['sale_days' => 'numeric|between:1,100']);
        $this->order_list = [];
    }

    public function inDemand()
    {
        $date = Carbon::now()->subDays($this->sale_days);
        $search = Product::from('products as p')
            ->join('sale_details as sd', 'sd.product_id', '=', 'p.id')
            ->where('p.status', 't')
            ->where('p.supplier_id', $this->supplier_id)
            ->where('sd.created_at', '>=', $date)
            ->groupBy('sd.product_id')
            ->select('p.id', 'p.name', 'p.cost_of_price', 'p.retail_price', 'p.salt', 'p.packing')
            ->get();
//        dd(date('Y-m-01', strtotime($this->sale_days)));
        if ($search->isNotEmpty()) {
            $data = $search->toArray();
        } else {
            $data = [];
        }

        if (!empty($data)) {

            foreach ($data as $key => $d) {
                $existing = collect($this->order_list)->where('id', $d['id'])->all();

                if (empty($existing)) {
                    $this->order_list[] = [
                        'id' => $d['id'],
                        'name' => $d['name'],
                        'qty' => 1,
                        'cost_of_price' => $d['cost_of_price'],
                        'retail_price' => $d['retail_price'],
                        'salt' => $d['salt'],
                        'total_cost' => $d['cost_of_price'],
                        'packing' => $d['packing']
                    ];
                }
//                else {
//                    $key = array_keys($existing)[0];
//                    $qty = $this->order_list[$key]['qty'];
//                    $this->order_list[$key]['qty'] = $qty + 1;
//                    $this->order_list[$key]['total_cost'] = $this->order_list[$key]['qty'] * $this->order_list[$key]['cost_of_price'];
//                }
            }
            $this->demand_check = true;
        }
    }

    public function openProductModal()
    {
        $this->product_qty = null;
        $this->products_modal = true;
        $this->emit('focusProductInput');
    }

    public function removeProduct($key)
    {
        if (isset($this->order_list[$key])) {
            unset($this->order_list[$key]);
        }
    }

    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'order_list') {
            if ($array[2] != 'salt') {
                if (empty($value) || !is_numeric($value)) {
                    $this->order_list[$array[1]][$array[2]] = 0;
                }

                if (in_array($array[2], ['qty', 'cost_of_price'])) {
                    $this->order_list[$array[1]]['total_cost'] = round($this->order_list[$array[1]]['qty'] * $this->order_list[$array[1]]['cost_of_price'], 2);
                }
            }
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
        }
        else {
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
            $existing = collect($this->order_list)->where('id', $data['id'])->all();
            $cop = null;
            $r_price = null;
            $total_cost = null;
            if ($this->loose_purchase == 't') {
                $cop = round($data['cost_of_price'] / $data['packing'], 2);
                $r_price = round($data['retail_price'] / $data['packing'], 2);
                $total_cost = $cop;
            } else {
                $cop = $data['cost_of_price'];
                $r_price = $data['retail_price'];
                $total_cost = $cop;

            }
            if (empty($existing)) {
                $this->order_list[] = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'qty' => ($this->product_qty <0) ? 1 :$this->product_qty,
                    'cost_of_price' => $cop,
                    'retail_price' => $r_price,
                    'salt' => $data['salt'],
                    'total_cost' => $total_cost,
                    'packing' => $data['packing']
                ];
            }
            else {
                $key = array_keys($existing)[0];
                $qty = $this->order_list[$key]['qty'];
                $this->order_list[$key]['qty'] = $qty + $this->product_qty;
                $this->order_list[$key]['total_cost'] = $this->order_list[$key]['qty'] * $this->order_list[$key]['cost_of_price'];
            }
            $this->products_modal = false;
        }
    }

    public function create()
    {
        $this->validate();
        $lock = Cache::lock('sale.add', 30);
        try {
            if ($lock->get()) {
                DB::beginTransaction();
                $purchase_id = Purchase::create([
                    'supplier_id' => $this->supplier_id,
                    'supplier_invoice' => $this->supplier_invoice,
                    'delivery_date' => $this->delivery_date,
                    'expected_date' => $this->formatDate($this->expected_date),
                    'created_by' => Auth::user()->id,
                    'status' => 'approval-awaiting',
                    'is_loose' => $this->loose_purchase
                ])->id;


                foreach ($this->order_list as $o) {
                    $qty = null;
                    $cop = null;
                    $r_price = null;
                    if ($this->loose_purchase == 't') {
                        $qty = $o['qty'];
                        $cop = $o['cost_of_price'];
                        $r_price = $o['retail_price'];
                    } else {
                        $qty = $o['qty'] * $o['packing'];
                        $cop = $o['cost_of_price'] / $o['packing'];
                        $r_price = $o['retail_price'] / $o['packing'];
                    }
                    $check = PurchaseOrder::where('purchase_id', $purchase_id)->where('product_id', $o['id'])->get()->first();
                    if (!empty($check)) {
                        throw new \Exception('An Unknown Error Occurred!');
                    }
                    PurchaseOrder::create([
                        'purchase_id' => $purchase_id,
                        'product_id' => $o['id'],
                        'qty' => $qty,
                        'cost_of_price' => $cop,
                        'retail_price' => $r_price,
                        'total_cost' => $o['cost_of_price'] * $o['qty'],
                    ]);

                    if ($this->loose_purchase == 'f') {
                        Product::find($o['id'])->update([
                            'salt' => $o['salt'] ?? null,
                            'cost_of_price' => $o['cost_of_price'],
                            'retail_price' => $o['retail_price'],
                            'supplier_id' => $this->supplier_id
                        ]);
                    }
                }
                DB::commit();
                $this->success = 'Purchase order has been created and awaiting for approval.';
                $this->reset(['order_list', 'expected_date', 'supplier_id', 'supplier_name', 'supplier_invoice', 'delivery_date']);
            }
            optional($lock)->release();
        } catch (\Exception $e) {
            $this->addError('supplier_name', $e->getMessage());
            DB::rollBack();
            optional($lock)->release();
        }
    }
}
