<?php


namespace Devzone\Pharmacy\Http\Livewire\Refunds\Supplier;


use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Refunds\SupplierRefundDetail;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $receiving_account_name;
    public $receiving_account;
    public $receiving_date;
    public $description;
    public $success;
    public $purchase_orders = [];
    public $selected_orders = [];
    public $primary_id;
    protected $listeners = ['emitSupplierId'];

    protected $rules = [
        'supplier_id' => 'required|integer',
        'receiving_account' => 'nullable|integer',
        'receiving_date' => 'nullable|date',
        'description' => 'nullable|string'
    ];

    public function mount($primary_id)
    {
        $this->primary_id = $primary_id;
        $refund = SupplierRefund::find($primary_id);
        if (!empty($refund['approved_by'])) {
            return redirect()->to('pharmacy/purchases/refund');
        }

        $refund_details = SupplierRefundDetail::where('supplier_refund_id', $refund->id)->get();
        $this->receiving_date = $refund->receiving_date;


        $this->description = $refund->description;
        $this->supplier_id = $refund->supplier_id;
        $supplier = Supplier::find($this->supplier_id);
        $this->supplier_name = $supplier->name;
        $this->emitSupplierId();

        foreach ($this->purchase_orders as $key => $po) {
            $return = $refund_details->where('product_inventory_id', $po['product_inventory_id'])->first();
            if (!empty($return)) {
                $this->purchase_orders[$key]['return'] = $return['qty'];
                $this->purchase_orders[$key]['id'] = $return['id'];
            }
        }


    }

    public function emitSupplierId()
    {
        $result = ProductInventory::from('product_inventories as pi')
            ->join('purchases as p', 'p.id', '=', 'pi.po_id')
            ->join('products as pr', 'pr.id', '=', 'pi.product_id')
            ->where('pi.type', 'regular')
            ->where('pi.qty', '>', 0)
            ->where('p.supplier_id', $this->supplier_id)
            ->select('pr.name', 'pi.qty', 'pi.supply_price',
                'pi.po_id', 'pi.product_id', 'pi.id as product_inventory_id')
            ->orderBy('pi.product_id')
            ->get();

        if ($result->isEmpty()) {
            $this->purchase_orders = [];
        } else {
            $this->purchase_orders = $result->toArray();
        }
    }

    public function render()
    {
        return view('pharmacy::livewire.refunds.supplier.edit');
    }

    public function create()
    {
        $return = (collect($this->purchase_orders)->where('return', '>', 0))->toArray();

        $this->validate();
        try {
            DB::beginTransaction();
            if (empty($return)) {
                throw new \Exception('No product found for refund.');
            }
            if (SupplierRefund::whereNotNull('approved_by')->where('id', $this->primary_id)->exists()) {
                throw new \Exception('You cannot edit this record because record already has been approved.');
            }

            $total_amount = 0;
            foreach ($return as $o) {
                $total_amount = $total_amount + ($o['supply_price'] * $o['return']);
            }


            SupplierRefund::where('id', $this->primary_id)->update([
                'supplier_id' => $this->supplier_id,
                'description' => $this->description,
                'total_amount' => $total_amount
            ]);
            SupplierRefundDetail::where('supplier_refund_id', $this->primary_id)->delete();
            foreach ($return as $o) {
                $check = ProductInventory::find($o['product_inventory_id']);
                if($o['return']>$check['qty']){
                    throw new \Exception('You cannot refund more than available qty.');
                }
                SupplierRefundDetail::create([
                    'supplier_refund_id' => $this->primary_id,
                    'product_id' => $o['product_id'],
                    'po_id' => $o['po_id'],
                    'qty' => $o['return'],
                    'supply_price' => $o['supply_price'],
                    'product_inventory_id' => $o['product_inventory_id']
                ]);
            }
            DB::commit();
            $this->success = 'Record has been edited.';


        } catch (\Exception $e) {
            $this->addError('purchase_orders', $e->getMessage());
            DB::rollBack();
        }

    }
}
