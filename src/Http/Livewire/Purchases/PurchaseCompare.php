<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Devzone\Pharmacy\Models\PurchaseReceive;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Mockery\Exception;


class PurchaseCompare extends Component
{
    public $purchase_id;
    public $receive;
    public $purchase;
    public $error;
    public $basic_info = false;
    public $grn_no;
    public $delivery_date;
    public $supplier_invoice;

    public function mount($purchase_id)
    {
        $this->purchase_id = $purchase_id;

        $purchase = Purchase::find($purchase_id);
        $this->grn_no = $purchase->grn_no;
        $this->delivery_date = $purchase->delivery_date;
        $this->supplier_invoice = $purchase->supplier_invoice;
    }

    public function render()
    {
        $purchase = Purchase::from('purchases as p')
            ->join('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->join('users as c', 'c.id', '=', 'p.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'p.approved_by')
            ->where('p.id', $this->purchase_id)
            ->select(
                'p.id','p.supplier_id','p.supplier_invoice','p.grn_no','p.delivery_date','p.status','p.approved_at','p.advance_tax',
                's.name as supplier_name','c.name as created_by','a.name as approved_by','c.created_at')
            ->orderBy('p.id', 'desc')
            ->first();

        $order = PurchaseOrder::from('purchase_orders as po')
            ->join('products as p', 'p.id', '=', 'po.product_id')
            ->where('po.purchase_id', $this->purchase_id)
            ->select('po.*', 'p.name', 'p.salt', 'p.packing')
            ->get();

        $receive = PurchaseReceive::from('purchase_receives as po')
            ->join('products as p', 'p.id', '=', 'po.product_id')
            ->where('po.purchase_id', $this->purchase_id)
            ->select('po.*', 'p.name', 'p.salt', 'p.packing')
            ->get();
        $supplier_payment_details=SupplierPaymentDetail::from('supplier_payment_details as spd')
            ->join('supplier_payments as sp','sp.id','=','spd.supplier_payment_id')
            ->join('users as u','u.id','=','sp.added_by')
            ->leftJoin('users as us','us.id','=','sp.approved_by')
            ->where('spd.order_id',$this->purchase_id)
            ->select('sp.created_at','sp.approved_at','u.name as added_by','us.name as approved_by')
            ->first();

        $this->purchase = $purchase;
        $this->receive = $receive;

        $overall = [];
        foreach ($order->toArray() as $o) {
            $overall[] = [
                'type' => 'order',
                'product_id' => $o['product_id'],
                'qty' => $o['qty'],
                'cost_of_price' => $o['cost_of_price'],
                'retail_price' => $o['retail_price'],
                'total_cost' => $o['total_cost'],
                'bonus' => 0,
                'discount' => 0,
                'after_disc_cost' => 0,
                'name' => $o['name'],
                'salt' => $o['salt'],
                'packing' => $o['packing']
            ];
        }

        foreach ($receive->toArray() as $o) {
            $overall[] = [
                'type' => 'receive',
                'product_id' => $o['product_id'],
                'qty' => $o['qty'],
                'cost_of_price' => $o['cost_of_price'],
                'retail_price' => $o['retail_price'],
                'total_cost' => $o['total_cost'],
                'bonus' => $o['bonus'],
                'discount' => $o['discount'],
                'after_disc_cost' => $o['after_disc_cost'],
                'name' => $o['name'],
                'salt' => $o['salt'],
                'packing' => $o['packing']
            ];
        }

        $overall = collect($overall);

        return view('pharmacy::livewire.purchases.purchase-compare', ['purchase' => $purchase, 'overall' => $overall,'supplier_payment_details'=>$supplier_payment_details]);
    }


    public function markAsApproved($id)
    {
        $purchase = Purchase::find($id);
        if ($purchase['status'] == 'approval-awaiting') {
            $purchase->update([
                'approved_at' => date('Y-m-d H:i:s'),
                'approved_by' => Auth::user()->id,
                'status' => 'awaiting-delivery'
            ]);
        }

    }


    public function removePurchase($id)
    {
        $deleted = Purchase::whereNull('approved_by')->where('id', $id)->delete();
        if ($deleted > 0) {
            PurchaseOrder::where('purchase_id', $id)->delete();
        }
        return redirect()->to('/pharmacy/purchases');
    }

    public function markApprove()
    {
        try {
            DB::beginTransaction();
            if (empty($this->purchase->delivery_date)) {
                throw new \Exception("Delivery date not updated.");
            }
            if(Purchase::where('status', 'received')->where('id', $this->purchase_id)->exists()){
                throw new \Exception("Already receive order.");
            }
            $inventory = ChartOfAccount::where('reference', 'pharmacy-inventory-5')->first();
            if (empty($inventory)) {
                throw new \Exception('Inventory account not found in chart of accounts.');
            }
            $supplier = Supplier::find($this->purchase->supplier_id);
            $amount = $this->receive->sum('total_cost');

            $tax = ChartOfAccount::where('reference', 'advance-tax-236')->first();
            if (empty($tax)) {
                throw new \Exception('Advance tax account not found in chart of accounts.');
            }
            $advance_tax_amount = 0;
            if (!empty($this->purchase['advance_tax'])) {
                $advance_tax_amount = $amount * ($this->purchase['advance_tax'] / 100);
            }
            $description = "Inventory amounting total PKR " . number_format($amount, 2) . "/- + taxable amount PKR " . number_format($advance_tax_amount, 2) . "/- gross total PKR " . number_format($amount + $advance_tax_amount, 2) . " received on dated " . date('d M, Y', strtotime($this->delivery_date)) .
                " against PO # " . $this->purchase_id . " from supplier " . $supplier['name'] . " by " . Auth::user()->name;


            $vno = Voucher::instance()->voucher()->get();
            GeneralJournal::instance()->account($inventory['id'])->debit($amount)->voucherNo($vno)
                ->date($this->purchase->delivery_date)->approve()->description($description)->execute();
            GeneralJournal::instance()->account($supplier['account_id'])->credit($amount)->voucherNo($vno)
                ->date($this->purchase->delivery_date)->approve()->description($description)->execute();
            GeneralJournal::instance()->account($tax['id'])->debit($advance_tax_amount)->voucherNo($vno)
                ->date($this->delivery_date)->approve()->description($description)->execute();

            $id = Purchase::where('status', 'receiving')->where('id', $this->purchase_id)->update([
                'is_paid' => 'f',
                'status' => 'received'
            ]);
            if (empty($id)) {
                throw new Exception('Something went wrong please try again.');
            }
            foreach ($this->receive as $r) {
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
                        'type' => 'purchase-bonus',
                        'description' => 'Inventory increase by bonus'
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {

            $this->error = $e->getMessage();
            DB::rollBack();
        }
    }

    public function openBasicInfo()
    {
        $this->basic_info = true;
    }

    public function updateBasicInfo()
    {
        Purchase::find($this->purchase_id)->update([
            'supplier_invoice' => $this->supplier_invoice,
            'grn_no' => $this->grn_no,
            'delivery_date' => $this->delivery_date
        ]);
        $this->basic_info = false;
    }

}
