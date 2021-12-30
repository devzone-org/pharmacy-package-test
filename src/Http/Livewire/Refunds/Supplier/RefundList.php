<?php

namespace Devzone\Pharmacy\Http\Livewire\Refunds\Supplier;

use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Refunds\SupplierRefundDetail;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RefundList extends Component
{
    use Searchable;


    public $supplier_id;
    public $supplier_id_s;
    public $supplier_name;

    public $pay_from;
    public $pay_from_s;
    public $pay_from_name;
    public $status;
    public $confirm_dialog = false;
    public $primary_id;
    public $receive_date;

    public function render()
    {
        $payments = SupplierRefund::from('supplier_refunds as sr')
            ->join('supplier_refund_details as srd', 'srd.supplier_refund_id', '=', 'sr.id')
            ->join('suppliers as s', 's.id', 'sr.supplier_id')
            ->join('product_inventories as pi', 'pi.id', '=', 'srd.product_inventory_id')
            ->join('users as c', 'c.id', '=', 'sr.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'sr.approved_by')
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('sr.supplier_id', $this->supplier_id_s);
            })
            ->when(!empty($this->pay_from_s), function ($q) {
                return $q->where('sr.pay_from', $this->pay_from_s);
            })
            ->when(!empty($this->status), function ($q) {
                if ($this->status == 'app') {
                    return $q->whereNotNull('sr.approved_at');
                } else {
                    return $q->whereNull('sr.approved_at');
                }

            })
            ->select('s.name as supplier_name', 'sr.id', 'sr.description',
                'sr.created_at', 'c.name as created_by','sr.is_receive',
                'a.name as approved_by', 'sr.approved_at', 'sr.total_amount as total_cost')
            ->groupBy('srd.supplier_refund_id')
            ->orderBy('sr.id', 'desc')
            ->paginate(20);
        return view('pharmacy::livewire.refunds.supplier.list', ['payments' => $payments]);
    }




    public function confirm($id)
    {
        //TODO this is refund case pending
        try {
            DB::beginTransaction();
            $supplier_refund = SupplierRefund::findOrFail($id);

            if (!empty($supplier_refund->approved_at)) {
                throw new \Exception('Payment already refunded.');
            }
            if(!auth()->user()->can('12.approve-supplier-returns')){
                throw new \Exception(env('PERMISSION_ERROR','Access Denied'));
            }
            $supplier = Supplier::find($supplier_refund['supplier_id']);

            $refund_details = SupplierRefundDetail::from('supplier_refund_details as srd')
                ->join('product_inventories as pi', 'pi.id', '=', 'srd.product_inventory_id')
                ->where('supplier_refund_id',$id)
                ->select('pi.id', 'srd.qty as return','srd.po_id', 'pi.qty', 'pi.supply_price')
                ->get();
            $orders = array_unique($refund_details->pluck('po_id')->toArray());
            foreach ($refund_details as $rd) {
                if ($rd->return > $rd->qty) {
                    throw new \Exception('Unable to refund because system does not have much inventory.');
                }

                $product_inv = ProductInventory::find($rd->id);
                $product_inv->decrement('qty',$rd->return);
                InventoryLedger::create([
                    'product_id' => $product_inv->product_id,
                    'order_id' => $product_inv->po_id,
                    'decrease' => $rd->return,
                    'type'=>'purchase-refund',
                    'description' => "Refunded on dated " . date('d M, Y H:i:s') .
                " against PO # " . $product_inv->po_id. " to supplier " . $supplier['name']
                ]);
            }

            $supplier_refund_receipt_no=Voucher::instance()->advances()->get();
            $description = "Amounting total PKR " . number_format($supplier_refund->total_amount, 2) . "/- refunded on dated " . date('d M, Y') .
                " against PO # " . implode(', ', $orders) . " & invoice # inv-".$supplier_refund_receipt_no." to supplier " . $supplier['name'] . " by " . Auth::user()->name . " " . $supplier_refund->description;

            $inventory = ChartOfAccount::where('reference', 'pharmacy-inventory-5')->first();
            $vno = Voucher::instance()->voucher()->get();
            GeneralJournal::instance()->account($supplier['account_id'])->debit($supplier_refund->total_amount)->voucherNo($vno)
                ->date(date('Y-m-d'))->approve()->description($description)->execute();
            GeneralJournal::instance()->account($inventory['id'])->credit($supplier_refund->total_amount)->voucherNo($vno)
                ->date(date('Y-m-d'))->approve()->description($description)->execute();


            $supplier_refund->update([
                'approved_by' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
                'receipt_no'=>$supplier_refund_receipt_no,
            ]);

            DB::commit();
            $this->confirm_dialog = false;

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError('status', $exception->getMessage());
        }
    }

    public function openConfirmation($id)
    {
        $this->primary_id = $id;
        $this->confirm_dialog = true;
    }


    public function removePurchase($id)
    {

        SupplierRefund::whereNull('approved_at')->where('id', $id)->delete();
        SupplierRefundDetail::where('supplier_refund_id', $id)->delete();
    }

    public function search()
    {
        $this->supplier_id_s = $this->supplier_id;

    }

    public function resetSearch()
    {
        $this->reset(
            'supplier_id_s',
            'pay_from_s',
            'pay_from',
            'supplier_id',
            'supplier_name',
            'pay_from_name',
            'status'
        );
    }
}
