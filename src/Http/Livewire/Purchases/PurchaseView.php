<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
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
                'p.id',
                'p.supplier_id',
                's.name as supplier_name',
                'p.supplier_invoice',
                'p.grn_no',
                'p.is_paid',
                'p.delivery_date',
                'p.status',
                'c.name as created_by',
                'a.name as approved_by',
                'p.approved_at',
                'c.created_at'
            )->orderBy('p.id', 'desc')->first();


        $details = PurchaseOrder::from('purchase_orders as po')
            ->join('products as p', 'p.id', '=', 'po.product_id')
            ->where('po.purchase_id', $this->purchase_id)
            ->select('po.*', 'p.name', 'p.salt')
            ->get();

        return view('pharmacy::livewire.purchases.purchase-view', ['purchase' => $purchase, 'details' => $details]);
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
