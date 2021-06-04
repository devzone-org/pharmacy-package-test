<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseList extends Component
{
    use WithPagination , Searchable;
    public $supplier_id;
    public $supplier_id_s;
    public $supplier_name;
    public $supplier_invoice;
    public $status;

    public function render()
    {
        $purchase = Purchase::from('purchases as p')
            ->join('purchase_orders as po', 'po.purchase_id', '=', 'p.id')
            ->join('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->join('users as c', 'c.id', '=', 'p.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'p.approved_by')
            ->when(!empty($this->supplier_id_s),function($q){
                return $q -> where('p.supplier_id',$this->supplier_id_s);
            })
            ->when(!empty($this->supplier_invoice),function($q){
                return $q -> where('p.supplier_invoice',$this->supplier_invoice);
            })

            ->when(!empty($this->status),function($q){
                return $q -> where('p.status',$this->status);
            })
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
                'c.created_at',
                DB::raw('SUM(po.total_cost) as cost_before_receiving')
            )->groupBy('p.id')->orderBy('p.id', 'desc')->paginate(20);


        return view('pharmacy::livewire.purchases.purchase-list', ['purchase' => $purchase]);
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
    }

    public function search(){
        $this->resetPage();

        $this->supplier_id_s  = $this->supplier_id;

    }

    public function resetSearch(){
        $this->resetPage();
        $this->reset(['supplier_id_s','supplier_id','supplier_invoice','status']);
    }
}
