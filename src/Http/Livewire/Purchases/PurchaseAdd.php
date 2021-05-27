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
    public $supplier_invoice;
    public $products_modal = false;
    public $search_products;
    public $product_data = [];
    public $order_list = [];

    public $success;

    protected $rules = [
        'supplier_id' => 'required|integer',
        'delivery_date' => 'nullable|date',
        'supplier_invoice' => 'nullable|string',
        'order_list.*.qty' => 'required|integer',
        'order_list.*.cost_of_price' => 'required|numeric',
        'order_list.*.retail_price' => 'required|numeric'
    ];

    protected $validationAttributes = [
        'supplier_id' => 'supplier'
    ];

    public function render()
    {
        return view('pharmacy::livewire.purchases.purchase-add');
    }

    public function openProductModal()
    {
        $this->products_modal = true;
        $this->emit('focusProductInput');
    }

    public function removeProduct($key)
    {
        unset($this->order_list[$key]);
    }

    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'order_list') {
            if (empty($value) || !is_numeric($value)) {
                $this->order_list[$array[1]][$array[2]] = 0;
            }
            if (in_array($array[2], ['qty', 'cost_of_price'])) {
                $this->order_list[$array[1]]['total_cost'] = round($this->order_list[$array[1]]['qty'] * $this->order_list[$array[1]]['cost_of_price'], 2);
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
                    'total_cost' => $data['cost_of_price']
                ];
            } else {
                $key = array_keys($existing)[0];
                $qty = $this->order_list[$key]['qty'];
                $this->order_list[$key]['qty'] = $qty + 1;
            }

        }


    }


    public function create()
    {
        $this->validate();
        try {
            DB::beginTransaction();
            $purchase_id = Purchase::create([
                'supplier_id' => $this->supplier_id,
                'supplier_invoice' => $this->supplier_invoice,
                'delivery_date' => $this->delivery_date,
                'created_by' => Auth::user()->id,
                'status' => 'approval-awaiting'
            ])->id;

            foreach ($this->order_list as $o) {
                PurchaseOrder::create([
                    'purchase_id' => $purchase_id,
                    'product_id' => $o['id'],
                    'qty' => $o['qty'],
                    'cost_of_price' => $o['cost_of_price'],
                    'retail_price' => $o['retail_price'],
                    'total_cost' => $o['cost_of_price'] * $o['qty'],
                ]);
            }
            DB::commit();
            $this->success = 'Purchase order has been created and awaiting for approval.';
            $this->reset(['order_list', 'supplier_id', 'supplier_name', 'supplier_invoice', 'delivery_date']);
        } catch (\Exception $e) {
            $this->addError('supplier_name', $e->getMessage());
            DB::rollBack();
        }
    }
}