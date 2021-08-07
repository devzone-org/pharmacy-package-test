<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseView extends Component
{
    public $purchase_id;
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
                'p.id', 'p.supplier_invoice', 'p.grn_no', 'p.is_paid', 'p.delivery_date', 'p.status', 'p.supplier_id', 'p.approved_at', 'p.advance_tax', 'p.created_at',
                's.name as supplier_name', 'a.name as approved_by', 'c.name as created_by')
            ->first();
        if ($purchase->status == 'received') {
            $details = \Devzone\Pharmacy\Models\PurchaseReceive::from('purchase_receives as pr')
                ->join('products as p', 'p.id', '=', 'pr.product_id')
                ->where('pr.purchase_id', $this->purchase_id)
                ->select('pr.*', 'p.name', 'p.salt')
                ->get();
        } else {
            $details = PurchaseOrder::from('purchase_orders as po')
                ->join('products as p', 'p.id', '=', 'po.product_id')
                ->where('po.purchase_id', $this->purchase_id)
                ->select('po.*', 'p.name', 'p.salt')
                ->get();
        }
        $purchase_receive = \Devzone\Pharmacy\Models\PurchaseReceive::where('purchase_id', $this->purchase_id)
            ->select(DB::raw('sum(total_cost) as total_receive'))
            ->groupBy('purchase_id')
            ->first();
        $supplier_payment_details = SupplierPaymentDetail::from('supplier_payment_details as spd')
            ->join('supplier_payments as sp', 'sp.id', '=', 'spd.supplier_payment_id')
            ->join('users as u', 'u.id', '=', 'sp.added_by')
            ->leftJoin('users as us', 'us.id', '=', 'sp.approved_by')
            ->where('spd.order_id', $this->purchase_id)
            ->select('sp.created_at', 'sp.approved_at', 'u.name as added_by', 'us.name as approved_by')
            ->first();
        return view('pharmacy::livewire.purchases.purchase-view', ['purchase' => $purchase, 'details' => $details, 'purchase_receive' => $purchase_receive, 'supplier_payment_details' => $supplier_payment_details]);
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
