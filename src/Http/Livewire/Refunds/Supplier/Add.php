<?php


namespace Devzone\Pharmacy\Http\Livewire\Refunds\Supplier;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Refunds\SupplierRefundDetail;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Add extends Component
{

    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $receiving_account_name;
    public $receiving_account;
    public $receiving_date;
    public $closing_balance;
    public $description;
    public $success;
    public $purchase_orders = [];
    public $selected_orders = [];
    protected $listeners = ['emitSupplierId'];

    protected $rules = [
        'supplier_id' => 'required|integer',
        'receiving_account' => 'nullable|integer',
        'receiving_date' => 'nullable|date',
        'description' => 'nullable|string'
    ];

    public function mount()
    {
        $this->receiving_date = date('Y-m-d');
    }


    public function render()
    {
        return view('pharmacy::livewire.refunds.supplier.add');
    }

    public function create()
    {
        $return = (collect($this->purchase_orders)->where('return', '>', 0))->toArray();
        $this->validate();
        $lock = Cache::lock('supplier.refund.add', 30);
        try {
            if ($lock->get()) {
                DB::beginTransaction();
                if (empty($return)) {
                    throw new \Exception('No product found for refund.');
                }

                $total_amount = 0;
                foreach ($return as $o) {
                    $total_amount = $total_amount + ($o['supply_price'] * $o['return']);
                }
                $id = SupplierRefund::create([
                    'supplier_id' => $this->supplier_id,
                    'description' => $this->description,
                    'created_by' => Auth::user()->id,
                    'total_amount' => $total_amount
                ])->id;

                foreach ($return as $o) {
                    $check = ProductInventory::find($o['product_inventory_id']);
                    if ($o['return'] > $check['qty']) {
                        throw new \Exception('You cannot refund more than available qty.');
                    }

                    SupplierRefundDetail::create([
                        'supplier_refund_id' => $id,
                        'product_id' => $o['product_id'],
                        'po_id' => $o['po_id'],
                        'supply_price' => $o['supply_price'],
                        'qty' => $o['return'],
                        'product_inventory_id' => $o['product_inventory_id']
                    ]);
                }
                DB::commit();
                $this->success = 'Record has been added and need for approval.';
                $this->reset(['supplier_id', 'closing_balance', 'supplier_name', 'purchase_orders', 'selected_orders', 'description', 'receiving_account', 'receiving_account_name']);
            }
            optional($lock)->release();
        } catch (\Exception $e) {
            $this->addError('purchase_orders', $e->getMessage());
            DB::rollBack();
            optional($lock)->release();
        }

    }

    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'purchase_orders') {
            if (empty($value)) {
                $this->purchase_orders[$array[1]]['return'] = 0;
            }
            $this->purchase_orders[$array[1]]['total_return'] = round($this->purchase_orders[$array[1]]['return'] * $this->purchase_orders[$array[1]]['supply_price'], 2);

        }
    }

    public function emitSupplierId()
    {
        $supplier = Supplier::from('suppliers as s')
            ->join('ledgers as l', function ($q) {
                return $q->on('l.account_id', '=', 's.account_id')->where('l.is_approve', 't');
            })->where('s.id', $this->supplier_id)
            ->select(DB::raw('sum(l.credit - l.debit) as closing'))->first();
        $this->closing_balance = !empty($supplier) ? $supplier->closing : 0;
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
}
