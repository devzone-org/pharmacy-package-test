<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use App\Models\Purchases;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class PurchaseEdit extends Component
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
    public $original_order_list = [];
    public $purchase_id;
    public $deleted = [];
    public $sale_days = '10';

    public $success;
    public $loose_purchase = 'f';

    protected $rules = [
        'supplier_id' => 'required|integer',
        'expected_date' => 'required|date',
        'supplier_invoice' => 'nullable|string',
        'order_list.*.qty' => 'required|integer',
        'order_list.*.cost_of_price' => 'required|numeric',
        'order_list.*.retail_price' => 'required|numeric'
    ];

    protected $validationAttributes = [
        'supplier_id' => 'supplier'
    ];

    public function mount($purchase_id)
    {
        $this->purchase_id = $purchase_id;

        $this->loose_purchase = Purchase::where('id', $this->purchase_id)->pluck('is_loose')->first();

        $purchase = Purchase::from('purchases as p')
            ->join('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->join('users as c', 'c.id', '=', 'p.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'p.approved_by')
            ->where('p.id', $this->purchase_id)
            ->select(
                'p.id',
                'p.supplier_id',
                's.name as supplier_name',
                'p.supplier_invoice',
                'p.grn_no',
                'p.delivery_date',
                'p.expected_date',
                'p.status',
                'c.name as created_by',
                'a.name as approved_by',
                'p.approved_at',
                'c.created_at'
            )->orderBy('p.id', 'desc')->first();
        $this->supplier_invoice = $purchase->supplier_invoice;
        $this->supplier_id = $purchase->supplier_id;
        $this->supplier_name = $purchase->supplier_name;
        $this->expected_date = $purchase->expected_date;

        $details = PurchaseOrder::from('purchase_orders as po')
            ->join('products as p', 'p.id', '=', 'po.product_id')
            ->where('po.purchase_id', $this->purchase_id)
            ->select('po.id as purchase_order_id', 'p.id', 'p.packing', 'po.qty', 'po.cost_of_price', 'po.retail_price', 'po.total_cost', 'p.name', 'p.salt')
            ->get();
        $arrays = [];
        foreach ($details->toArray() as $d) {
            if ($this->loose_purchase == 't') {
                $arrays[] = $d;
            } else {
                $d['qty'] = $d['qty'] / $d['packing'];
                $d['cost_of_price'] = $d['packing'] * $d['cost_of_price'];
                $d['retail_price'] = $d['packing'] * $d['retail_price'];
                $arrays[] = $d;
            }
        }

        $this->order_list = $arrays;
        $this->original_order_list = $arrays;
    }

    public function render()
    {
        return view('pharmacy::livewire.purchases.purchase-edit');
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
            }
        }
    }

    public function updatedSaleDays()
    {
        $this->validate(['sale_days' => 'numeric|between:1,100']);
        $this->order_list = $this->original_order_list;
    }

    public function openProductModal()
    {
        $this->products_modal = true;
        $this->emit('focusProductInput');
    }

    public function removeProduct($key)
    {

        if (isset($this->order_list[$key])) {
            $data = ($this->order_list[$key]);
            if (isset($data['purchase_order_id']) && !empty($data['purchase_order_id'])) {
                $this->deleted[] = $data['purchase_order_id'];
            }
            unset($this->order_list[$key]);
        }

    }

    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'order_list') {
            if (empty($value) || !is_numeric($value)) {
                $this->order_list[$array[1]][$array[2]] = 0;
            }
            if (in_array($array[2], ['qty', 'cost_of_price'])) {
                $this->order_list[$array[1]]['cost_of_price'] = round($this->order_list[$array[1]]['cost_of_price'], 2);
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
                    'qty' => 1,
                    'cost_of_price' => $cop,
                    'retail_price' => $r_price,
                    'salt' => $data['salt'],
                    'total_cost' => $total_cost,
                    'packing' => $data['packing']
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
        $lock = Cache::lock('purchase.edit.' . $this->purchase_id, 30);
        try {
            if ($lock->get()) {
                DB::beginTransaction();
                if (!Purchase::whereNull('approved_by')->where('id', $this->purchase_id)->exists()) {
                    throw new \Exception('Unable to edit purchase order because this order already has been approved.');
                }
                Purchase::where('id', $this->purchase_id)->update([
                    'supplier_id' => $this->supplier_id,
                    'supplier_invoice' => $this->supplier_invoice,
                    'delivery_date' => $this->delivery_date,

                ]);

                if (!empty($this->deleted)) {
                    PurchaseOrder::whereIn('id', $this->deleted)->delete();
                }

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

                    if (!empty($o['purchase_order_id'])) {
                        PurchaseOrder::find($o['purchase_order_id'])->update([
                            'qty' => $qty,
                            'cost_of_price' => $cop,
                            'retail_price' => $r_price,
                            'total_cost' => $o['cost_of_price'] * $o['qty'],
                        ]);
                    } else {
                        $check = PurchaseOrder::where('purchase_id', $this->purchase_id)->where('product_id', $o['id'])->exists();
                        if ($check) {
                            continue;
                        }
                        PurchaseOrder::create([
                            'purchase_id' => $this->purchase_id,
                            'product_id' => $o['id'],
                            'qty' => $qty,
                            'cost_of_price' => $cop,
                            'retail_price' => $r_price,
                            'total_cost' => $o['cost_of_price'] * $o['qty'],
                        ]);
                    }
                    if ($this->loose_purchase == 'f') {
                        Product::find($o['id'])->update([
                            'cost_of_price' => $o['cost_of_price'],
                            'retail_price' => $o['retail_price'],
                            'supplier_id' => $this->supplier_id
                        ]);
                    }
                }
                DB::commit();
                $this->success = 'Purchase order has been updated.';
                $this->redirect($this->purchase_id);
            }
            optional($lock)->release();
        } catch (\Exception $e) {
            $this->addError('supplier_name', $e->getMessage());
            DB::rollBack();
            optional($lock)->release();
        }
    }
}
