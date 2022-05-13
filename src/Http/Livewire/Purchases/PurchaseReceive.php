<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Carbon\Carbon;
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
    public $advance_tax;
    public $advance_tax_amount = 0;
    public $deleted = [];
    public $success;
    public $current_date;
    public $loose_purchase = 'f';

    protected $rules = [
        'supplier_id' => 'required|integer',
        'delivery_date' => 'required|date',
        'supplier_invoice' => 'nullable|string',
        'order_list.*.qty' => 'required|integer',
        'order_list.*.bonus' => 'nullable|integer',
        'order_list.*.disc' => 'nullable|numeric',
        'order_list.*.cost_of_price' => 'required|numeric',
        'order_list.*.retail_price' => 'required|numeric',
        'order_list.*.expiry' => 'required|date_format:d-m-Y',
        'advance_tax' => 'numeric|lte:100|gte:0'
    ];

    protected $validationAttributes = [
        'supplier_id' => 'supplier',
        'order_list.*.expiry' => 'Expiry Date'
    ];

    protected $messages = [
        'order_list.*.expiry.date_format' => 'The expiry date is invalid.',
    ];

    public function mount($purchase_id)
    {
        $this->delivery_date = date('d M Y');

        $this->loose_purchase = Purchase::where('id', $purchase_id)->pluck('is_loose')->first();

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
        if (!empty($purchase->delivery_date)) {
            $this->delivery_date = $purchase->delivery_date;
        }


        $details = PurchaseOrder::from('purchase_orders as po')
            ->join('products as p', 'p.id', '=', 'po.product_id')
            ->where('po.purchase_id', $this->purchase_id)
            ->select('po.id as purchase_order_id', 'p.id', 'po.qty', 'po.cost_of_price', 'po.retail_price', 'po.total_cost', 'p.name', 'p.salt', 'p.packing')
            ->get();

        foreach ($details as $data) {
            $qty = null;
            $cop = null;
            $r_price = null;
            $total_qty = null;
            if ($this->loose_purchase == 't') {

                $qty = $data['qty'];
                $cop = round($data['cost_of_price'], 2);
                $r_price = round($data['retail_price'], 2);
                $total_qty = $data['qty'];

            } else {
                $qty = $data['qty'] / $data['packing'];
                $cop = round($data['cost_of_price'] * $data['packing'], 2);
                $r_price = round($data['retail_price'] * $data['packing'], 2);
                $total_qty = $data['qty'] / $data['packing'];

            }
            $this->order_list[] = [
                'id' => $data['id'],
                'name' => $data['name'],
                'qty' => $qty,
                'bonus' => 0,
                'disc' => 0,
                'cost_of_price' => $cop,
                'after_disc_cost' => $cop,
                'retail_price' => $r_price,
                'total_retail_price' => round($data['retail_price'] * $data['qty'], 2),
                'profit' => round(($data['retail_price'] * $data['qty']) - ($data['cost_of_price'] * $data['qty']), 2),
                'salt' => $data['salt'],
                'total_cost' => round($data['cost_of_price'] * $data['qty'], 2),
                'packing' => $data['packing'],
                'total_qty' => $total_qty,
            ];
        }
    }


    public function render()
    {
        return view('pharmacy::livewire.purchases.purchase-receive');
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }

    private function formatExpiryDate($date)
    {
        return Carbon::createFromFormat('d-m-Y', $date)
            ->format('Y-m-d');
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
                $this->order_list[$array[1]]['total_retail_price'] = round($this->order_list[$array[1]]['qty'] * $this->order_list[$array[1]]['retail_price'], 2);

                $this->order_list[$array[1]]['profit'] = round(($this->order_list[$array[1]]['retail_price'] * $this->order_list[$array[1]]['qty']) - ($this->order_list[$array[1]]['cost_of_price'] * $this->order_list[$array[1]]['qty']), 2);

            }

            if ($array[2] == 'bonus') {

                $this->order_list[$array[1]]['total_qty'] = round(($this->order_list[$array[1]]['bonus'] + $this->order_list[$array[1]]['qty']), 2);
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
                $this->order_list[$array[1]]['profit'] = round(($this->order_list[$array[1]]['retail_price'] * $this->order_list[$array[1]]['qty']) - ($this->order_list[$array[1]]['cost_of_price'] * $this->order_list[$array[1]]['qty']), 2);

            }

            if ($array[2] == 'retail_price') {

                $retail_price = $this->order_list[$array[1]]['total_retail_price'];
                if (empty($retail_price) || !is_numeric($retail_price)) {
                    $retail_price = 0;
                }
                $this->order_list[$array[1]]['total_retail_price'] = round($this->order_list[$array[1]]['qty'] * $this->order_list[$array[1]]['retail_price'], 2);
                $this->order_list[$array[1]]['profit'] = round(($this->order_list[$array[1]]['retail_price'] * $this->order_list[$array[1]]['qty']) - ($this->order_list[$array[1]]['cost_of_price'] * $this->order_list[$array[1]]['qty']), 2);

            }
        }


        if ($name == 'advance_tax') {
            if ($this->advance_tax > 100 || $this->advance_tax < 0) {
                $this->advance_tax = 0;
                $this->addError('error', 'Advance Tax cannot be greater than 100% & less than 0%.');
            }
            $total_amount = collect($this->order_list)->sum('total_cost');
            if (!empty($this->advance_tax)) {
                $this->advance_tax_amount = $total_amount * ($this->advance_tax / 100);
            } else {
                $this->advance_tax_amount = 0;
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
                    'total_cost' => $cop,
                    'total_retail_price' => $r_price,
                    'profit' => round($r_price - $cop, 2),
                    'packing' => $data['packing'],
                    'after_disc_cost' => $cop,
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
            $po = Purchase::find($this->purchase_id);

            if (empty($po['approved_at'])) {
                throw new Exception('Purchase order is not approved.');
            }

            if ($po['status'] == 'Void') {
                throw new Exception('Purchase order already voided.');
            }


            $purchase_receipt_no = Voucher::instance()->advances()->get();
            Purchase::where('id', $this->purchase_id)->whereNotNull('approved_at')->where('status', 'awaiting-delivery')->update([
                'supplier_id' => $this->supplier_id,
                'supplier_invoice' => $this->supplier_invoice,
                'delivery_date' => $this->formatDate($this->delivery_date),
                'status' => 'receiving',
                'grn_no' => $this->grn_no,
                'advance_tax' => $this->advance_tax,
                'receipt_no' => $purchase_receipt_no,
                'is_loose' => $this->loose_purchase
            ]);

            foreach ($this->order_list as $o) {
                $qty = null;
                $cop = null;
                $r_price = null;
                $bonus = null;
                if ($this->loose_purchase == 't') {
                    $qty = $o['qty'];
                    $cop = $o['cost_of_price'];
                    $r_price = $o['retail_price'];
                    $bonus = $o['bonus'];
                } else {
                    $qty = $o['qty'] * $o['packing'];
                    $cop = $o['cost_of_price'] / $o['packing'];
                    $r_price = $o['retail_price'] / $o['packing'];
                    $bonus = $o['bonus'] * $o['packing'];
                }

                \Devzone\Pharmacy\Models\PurchaseReceive::create([
                    'purchase_id' => $this->purchase_id,
                    'product_id' => $o['id'],
                    'qty' => $qty,
                    'bonus' => $bonus ?? 0,
                    'discount' => $o['disc'] ?? 0,
                    'cost_of_price' => $cop,
                    'after_disc_cost' => $cop,
                    'retail_price' => $r_price,
                    'total_cost' => $cop * $qty,
                    'batch_no' => $o['batch_no'] ?? null,
                    'expiry' => $this->formatExpiryDate($o['expiry']) ?? null,
                ]);

                if ($this->loose_purchase == 'f') {
                    Product::find($o['id'])->update([
                        'cost_of_price' => $o['cost_of_price'],
                        'retail_price' => $o['retail_price'],
                    ]);
                }
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

                if (Purchase::where('status', 'received')->where('id', $this->purchase_id)->exists()) {
                    throw new \Exception("Already receive order.");
                }
                if (empty($this->delivery_date)) {
                    throw new \Exception("Delivery date not updated.");
                }

                $inventory = ChartOfAccount::where('reference', 'pharmacy-inventory-5')->first();
                if (empty($inventory)) {
                    throw new \Exception('Inventory account not found in chart of accounts.');
                }
                $get_purchase = Purchase::where('id', $this->purchase_id)->first();
                $supplier = Supplier::find($this->supplier_id);
                $amount = $receive->sum('total_cost');
                $grn_no = empty($get_purchase->grn_no) ? '-' : $get_purchase->grn_no;
                $description = "RECEIVED INVENTORY amounting total PKR " . number_format($amount, 2) . "/- + Recoverable Advance Tax u/s 236(h)(" . $get_purchase->advance_tax . "%) amount PKR " .
                    number_format($this->advance_tax_amount, 2) . "/- = Net Payable to supplier '" . $supplier['name'] . "' PKR " . number_format($amount + $this->advance_tax_amount, 2) .
                    "/- against PO # " . $this->purchase_id . " & invoice # INV-" . $purchase_receipt_no . " & GRN # " . $grn_no . " received by " . Auth::user()->name . " on dated " . date('d M, Y h:i A');

                $tax = ChartOfAccount::where('reference', 'advance-tax-236')->first();
                if (empty($tax)) {
                    throw new \Exception('Advance tax account not found in chart of accounts.');
                }
                $vno = Voucher::instance()->voucher()->get();
                GeneralJournal::instance()->account($inventory['id'])->debit($amount)->voucherNo($vno)
                    ->date($this->formatDate($this->delivery_date))->approve()->description($description)->execute();
                GeneralJournal::instance()->account($tax['id'])->debit($this->advance_tax_amount)->voucherNo($vno)
                    ->date($this->formatDate($this->delivery_date))->approve()->description($description)->execute();
                GeneralJournal::instance()->account($supplier['account_id'])->credit($amount + $this->advance_tax_amount)->voucherNo($vno)
                    ->date($this->formatDate($this->delivery_date))->approve()->description($description)->execute();


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
                        'type' => 'purchase',
                        'description' => $description
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
                            'type' => 'purchase-bonus',
                            'description' => '[BONUS] ' . $description
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
