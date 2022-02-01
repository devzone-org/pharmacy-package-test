<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseAdd extends Component
{
    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $delivery_date;
    public $expected_date;
    public $supplier_invoice;
    public $products_modal = false;
    public $search_products;
    public $product_data = [];
    public $order_list = [];
    public $sale_days = '-10 days';
    public $demand_check = false;

    public $success;
    protected $listeners = ['emitSupplierId'];

    protected $rules = [
        'supplier_id' => 'required|integer',
        'expected_date' => 'required|date',
        'supplier_invoice' => 'nullable|string',
        'order_list' => 'required',
        'order_list.*.qty' => 'required|integer',
        'order_list.*.salt' => 'nullable|string',
        'order_list.*.cost_of_price' => 'required|numeric',
        'order_list.*.retail_price' => 'required|numeric'
    ];

    protected $validationAttributes = [
        'supplier_id' => 'supplier',
        'order_list' => 'Products',
    ];

    public function mount()
    {
        
        $this->expected_date = date('Y-m-d');
    }

    public function render()
    {
        return view('pharmacy::livewire.purchases.purchase-add');
    }

    public function emitSupplierId()
    {

    }

    public function updatedSaleDays()
    {

    }

    public function inDemand()
    {
        $search = Product::from('products as p')
            ->join('sale_details as sd', 'sd.product_id', '=', 'p.id')
            ->where('p.status', 't')
            ->where('p.supplier_id', $this->supplier_id)
            ->where('sd.created_at', '>=', date('Y-m-01', strtotime($this->sale_days)))
            ->groupBy('sd.product_id')
            ->get();
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
                } else {
                    $key = array_keys($existing)[0];
                    $qty = $this->order_list[$key]['qty'];
                    $this->order_list[$key]['qty'] = $qty + 1;
                    $this->order_list[$key]['total_cost'] = $this->order_list[$key]['qty'] * $this->order_list[$key]['cost_of_price'];
                }
            }
        $this->demand_check = true;
        }
    }

    public function openProductModal()
    {
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
            $existing = collect($this->order_list)->where('id', $data['id'])->all();
            if (empty($existing)) {
                $this->order_list[] = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'qty' => 1,
                    'cost_of_price' => $data['cost_of_price'],
                    'retail_price' => $data['retail_price'],
                    'salt' => $data['salt'],
                    'total_cost' => $data['cost_of_price'],
                    'packing' => $data['packing']
                ];
            } else {
                $key = array_keys($existing)[0];
                $qty = $this->order_list[$key]['qty'];
                $this->order_list[$key]['qty'] = $qty + 1;
                $this->order_list[$key]['total_cost'] = $this->order_list[$key]['qty'] * $this->order_list[$key]['cost_of_price'];
            }
        }
    }

    public function create()
    {
        dd($this->order_list);
        $this->validate();
        try {
            DB::beginTransaction();
            $purchase_id = Purchase::create([
                'supplier_id' => $this->supplier_id,
                'supplier_invoice' => $this->supplier_invoice,
                'delivery_date' => $this->delivery_date,
                'expected_date' => $this->expected_date,
                'created_by' => Auth::user()->id,
                'status' => 'approval-awaiting'
            ])->id;
            foreach ($this->order_list as $o) {
                $check = PurchaseOrder::where('purchase_id', $purchase_id)->where('product_id', $o['id'])->get()->first();
                if (!empty($check)){
                    throw new \Exception('An Unknown Error Occurred!');
                }
                PurchaseOrder::create([
                    'purchase_id' => $purchase_id,
                    'product_id' => $o['id'],
                    'qty' => $o['qty'] * $o['packing'],
                    'cost_of_price' => $o['cost_of_price'] / $o['packing'],
                    'retail_price' => $o['retail_price'] / $o['packing'],
                    'total_cost' => $o['cost_of_price'] * $o['qty'],
                ]);

                Product::find($o['id'])->update([
                    'salt' => $o['salt'] ?? null,
                    'cost_of_price' => $o['cost_of_price'],
                    'retail_price' => $o['retail_price'],
                ]);
            }
            DB::commit();
            $this->success = 'Purchase order has been created and awaiting for approval.';
            $this->reset(['order_list', 'expected_date', 'supplier_id', 'supplier_name', 'supplier_invoice', 'delivery_date']);
        } catch (\Exception $e) {
            $this->addError('supplier_name', $e->getMessage());
            DB::rollBack();
        }
    }
}
