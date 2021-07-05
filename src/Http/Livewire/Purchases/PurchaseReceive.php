<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Devzone\Pharmacy\Models\Supplier;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseReceive extends Component
{
    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $delivery_date;
    public $supplier_invoice;
    public $products_modal = false;
    public $more_options_modal = false;
    public $search_products;
    public $key_id;
    public $product_data = [];
    public $order_list = [];
    public $grn_no;
    public $purchase_id;
    public $deleted = [];
    public $success;

    protected $rules = [
        'supplier_id' => 'required|integer',
        'delivery_date' => 'required|date',
        'supplier_invoice' => 'nullable|string',
        'order_list.*.qty' => 'required|integer',
        'order_list.*.bonus' => 'nullable|integer',
        'order_list.*.disc' => 'nullable|numeric',
        'order_list.*.cost_of_price' => 'required|numeric',
        'order_list.*.retail_price' => 'required|numeric'
    ];

    protected $validationAttributes = [
        'supplier_id' => 'supplier'
    ];

    public function mount($purchase_id)
    {
        $this->purchase_id = $purchase_id;
        if (\Devzone\Pharmacy\Models\PurchaseReceive::where('purchase_id', $purchase_id)->exists()) {
            return redirect()->to('pharmacy/purchases/compare/' . $purchase_id);
        }
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
                'p.status',
                'c.name as created_by',
                'a.name as approved_by',
                'p.approved_at',
                'c.created_at'
            )->orderBy('p.id', 'desc')->first();
        $this->supplier_invoice = $purchase->supplier_invoice;
        $this->supplier_id = $purchase->supplier_id;
        $this->supplier_name = $purchase->supplier_name;
        $this->delivery_date = $purchase->delivery_date;

        $details = PurchaseOrder::from('purchase_orders as po')
            ->join('products as p', 'p.id', '=', 'po.product_id')
            ->where('po.purchase_id', $this->purchase_id)
            ->select('po.id as purchase_order_id', 'p.id', 'po.qty', 'po.cost_of_price', 'po.retail_price', 'po.total_cost', 'p.name', 'p.salt', 'p.packing')
            ->get();


        foreach ($details as $data) {
            $this->order_list[] = [
                'id' => $data['id'],
                'name' => $data['name'],
                'qty' => $data['qty'] / $data['packing'],
                'bonus' => 0,
                'disc' => 0,
                'cost_of_price' => $data['cost_of_price'] * $data['packing'],
                'after_disc_cost' => $data['cost_of_price'] * $data['packing'],
                'retail_price' => $data['retail_price'] * $data['packing'],
                'salt' => $data['salt'],
                'total_cost' => $data['cost_of_price'] * ($data['packing']) * ($data['qty'] / $data['packing']),
                'packing' => $data['packing'],
                'total_qty' => $data['qty'] / $data['packing'],
            ];
        }
    }

    public function render()
    {
        return view('pharmacy::livewire.purchases.purchase-receive');
    }

    public function moreOptions($key)
    {
        $this->key_id = $key;
        $this->more_options_modal = true;
    }

    public function openProductModal()
    {
        $this->products_modal = true;
        $this->emit('focusProductInput');
    }

    public function removeProduct($key)
    {

        $data = ($this->order_list[$key]);
        if (isset($data['purchase_order_id']) && !empty($data['purchase_order_id'])) {
            $this->deleted[] = $data['purchase_order_id'];
        }
        unset($this->order_list[$key]);
    }

    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'order_list') {
            if ((empty($value) || !is_numeric($value)) && in_array($array[2], ['qty', 'bonus', 'cost_of_price', 'disc', 'retail_price'])) {
                $this->order_list[$array[1]][$array[2]] = 0;
            }
            if ($array[2] == 'qty') {
                $this->order_list[$array[1]]['total_cost'] = round($this->order_list[$array[1]]['qty'] * $this->order_list[$array[1]]['after_disc_cost'], 2);
                $this->order_list[$array[1]]['total_qty'] = round(($this->order_list[$array[1]]['bonus'] + $this->order_list[$array[1]]['qty']), 2);
            }

            if ($array[2] == 'bonus') {

                $this->order_list[$array[1]]['total_qty'] = round(($this->order_list[$array[1]]['bonus'] + $this->order_list[$array[1]]['qty']) , 2);
            }

            if ($array[2] == 'cost_of_price' || $array[2] == 'disc') {
                $disc = $this->order_list[$array[1]]['disc'];
                $cost_of_price = $this->order_list[$array[1]]['cost_of_price'];
                if (empty($disc) || !is_numeric($disc)) {
                    $disc = 0;
                }

                if (empty($cost_of_price) || !is_numeric($cost_of_price)) {
                    $cost_of_price = 0;
                }
                $discount_value = $cost_of_price * $disc / 100;
                $this->order_list[$array[1]]['after_disc_cost'] = round($cost_of_price - $discount_value, 2);
                $this->order_list[$array[1]]['total_cost'] = round($this->order_list[$array[1]]['qty'] * $this->order_list[$array[1]]['after_disc_cost'], 2);

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
                    'packing' => $data['packing'],
                    'after_disc_cost' => $data['cost_of_price'],
                    'disc' => 0,
                    'bonus' => 0,
                    'total_qty' => 1
                ];
            } else {
                $key = array_keys($existing)[0];
                $qty = $this->order_list[$key]['qty'];
                $this->order_list[$key]['qty'] = $qty + 1;
                $this->order_list[$key]['total_qty'] = $this->order_list[$key]['qty'];
            }

        }


    }


    public function create()
    {
        $this->validate();
        try {

            DB::beginTransaction();

            Purchase::where('id', $this->purchase_id)->update([
                'supplier_id' => $this->supplier_id,
                'supplier_invoice' => $this->supplier_invoice,
                'delivery_date' => $this->delivery_date,
                'status' => 'receiving',
                'grn_no' => $this->grn_no
            ]);
            foreach ($this->order_list as $o) {

                \Devzone\Pharmacy\Models\PurchaseReceive::create([
                    'purchase_id' => $this->purchase_id,
                    'product_id' => $o['id'],
                    'qty' => $o['qty'] * $o['packing'],
                    'bonus' => $o['bonus'] * $o['packing'] ?? 0,
                    'discount' => $o['disc'] ?? 0,
                    'cost_of_price' => $o['cost_of_price'] / $o['packing'],
                    'after_disc_cost' => $o['after_disc_cost'] / $o['packing'],
                    'retail_price' => $o['retail_price'] / $o['packing'],
                    'total_cost' => $o['after_disc_cost'] * $o['qty'],
                    'batch_no' => $o['batch_no'] ?? null,
                    'expiry' => $o['expiry'] ?? null,
                ]);
            }
            $is_auto_approve = true;
            $purchase_order = PurchaseOrder::where('purchase_id', $this->purchase_id)->get();
            foreach ($purchase_order as $p) {
                $auto_approve = \Devzone\Pharmacy\Models\PurchaseReceive::where('purchase_id', $this->purchase_id)
                    ->where('product_id', $p->product_id)
                    ->where('qty', $p->qty)
                    ->where('cost_of_price', $p->cost_of_price)
                    ->where('retail_price', $p->retail_price)
                    ->where('total_cost', $p->total_cost)
                    ->exists();

                if ($auto_approve == false) {
                    $is_auto_approve = false;
                }
            }

            if ($is_auto_approve) {
                $receive = \Devzone\Pharmacy\Models\PurchaseReceive::from('purchase_receives as po')
                    ->join('products as p', 'p.id', '=', 'po.product_id')
                    ->where('po.purchase_id', $this->purchase_id)
                    ->select('po.*', 'p.name', 'p.salt', 'p.packing')
                    ->get();


                if (empty($this->delivery_date)) {
                    throw new \Exception("Delivery date not updated.");
                }

                $inventory = ChartOfAccount::where('reference', 'pharmacy-inventory-5')->first();
                if (empty($inventory)) {
                    throw new \Exception('Inventory account not found in chart of accounts.');
                }
                $supplier = Supplier::find($this->supplier_id);
                $amount = $receive->sum('total_cost');
                $description = "Inventory amounting total PKR " . number_format($amount, 2) . "/- received on dated " . date('d M, Y', strtotime($this->delivery_date)) .
                    " against PO # " . $this->purchase_id . " from supplier " . $supplier['name'] . " by " . Auth::user()->name;


                $vno = Voucher::instance()->voucher()->get();
                GeneralJournal::instance()->account($inventory['id'])->debit($amount)->voucherNo($vno)
                    ->date($this->delivery_date)->approve()->description($description)->execute();
                GeneralJournal::instance()->account($supplier['account_id'])->credit($amount)->voucherNo($vno)
                    ->date($this->delivery_date)->approve()->description($description)->execute();


                $id = Purchase::where('status', 'receiving')->where('id', $this->purchase_id)->update([
                    'is_paid' => 'f',
                    'status' => 'received'
                ]);
                if (empty($id)) {
                    throw new Exception('Something went wrong please try again.');
                }
                foreach ($receive as $r) {
                    ProductInventory::create([
                        'product_id' => $r->product_id,
                        'qty' => $r->qty,
                        'retail_price' => $r->retail_price,
                        'supply_price' => $r->after_disc_cost,
                        'expiry' => !empty($r->expiry) ? $r->expiry : null,
                        'batch_no' => !empty($r->batch_no) ? $r->batch_no : null,
                        'po_id' => $this->purchase_id,
                        'type' => 'regular'
                    ]);
                    InventoryLedger::create([
                        'product_id' => $r->product_id,
                        'order_id' => $this->purchase_id,
                        'increase' => $r->qty,
                        'description' => 'Inventory increase'
                    ]);
                    if ($r->bonus > 0) {
                        ProductInventory::create([
                            'product_id' => $r->product_id,
                            'qty' => $r->bonus,
                            'retail_price' => $r->retail_price,
                            'supply_price' => 0,
                            'po_id' => $this->purchase_id,
                            'type' => 'bonus',
                            'expiry' => !empty($r->expiry) ? $r->expiry : null,
                            'batch_no' => !empty($r->batch_no) ? $r->batch_no : null,
                        ]);
                        InventoryLedger::create([
                            'product_id' => $r->product_id,
                            'order_id' => $this->purchase_id,
                            'increase' => $r->bonus,
                            'description' => 'Inventory increase by bonus'
                        ]);
                    }
                }


            }

            DB::commit();
            return redirect()->to('pharmacy/purchases/compare/' . $this->purchase_id);

        } catch (\Exception $e) {
            $this->addError('supplier_name', $e->getMessage());
            DB::rollBack();
        }
    }
}
