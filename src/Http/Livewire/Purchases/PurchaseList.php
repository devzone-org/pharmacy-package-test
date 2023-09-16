<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseList extends Component
{
    use WithPagination;

    public $supplier_id;
    public $supplier_id_s;
    public $supplier_name;
    public $supplier_invoice;
    public $status;
    public $po_unapproved;
    public $po_approved;
    public $stock_receiving_in_process;
    public $unpaid_invoices;
    public $order_completed;
    public $from;
    public $to;

    public function render()
    {
        $this->stats();
        $purchase = Purchase::from('purchases as p')
            ->join('purchase_orders as po', 'po.purchase_id', '=', 'p.id')
            ->join('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->join('users as c', 'c.id', '=', 'p.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'p.approved_by')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->when(!empty($this->supplier_invoice), function ($q) {
                return $q->where('p.supplier_invoice', $this->supplier_invoice);
            })
            ->when(!empty($this->status), function ($q) {
                if ($this->status == 'received-f') {
                    return $q->where('p.status', 'received')->where('is_paid', 'f');
                } else if ($this->status == 'received-t') {
                    return $q->where('p.status', 'received')->where('is_paid', 't');
                } else {
                    return $q->where('p.status', $this->status);
                }
            })
            ->select(
                'p.id', 'p.advance_tax', 'p.supplier_id', 'p.supplier_invoice', 'p.grn_no', 'p.is_paid', 'p.delivery_date', 'p.expected_date', 'p.status',
                'p.approved_at', 'p.created_at',
                'a.name as approved_by', 's.name as supplier_name', 'c.name as created_by',
                DB::raw('SUM(po.total_cost) as cost_before_receiving')
            )
            ->groupBy('p.id')
            ->orderBy('p.id', 'desc')
            ->paginate(20);

        $purchase_ids = $purchase->pluck('id')->toArray();

        $purchase_receives = \Devzone\Pharmacy\Models\PurchaseReceive::from('purchase_receives as pr')
            ->whereIn('pr.purchase_id',$purchase_ids)
            ->groupBy('pr.purchase_id')
            ->select('pr.purchase_id as id', DB::raw('SUM(pr.total_cost) as cost_after_receiving'))
            ->get();


        $payments = SupplierPaymentDetail::from('supplier_payment_details as spd')
//            ->leftJoin('supplier_payment_details as spd', 'spd.order_id', '=', 'p.id')
            ->leftJoin('supplier_payments as sp', 'sp.id', '=', 'spd.supplier_payment_id')
            ->leftJoin('users as u', 'u.id', '=', 'sp.added_by')
            ->leftJoin('users as us', 'us.id', '=', 'sp.approved_by')
            ->whereIn('spd.order_id',$purchase_ids)
            ->select('spd.order_id', 'sp.created_at', 'sp.approved_at', 'us.name as approved_by', 'u.name as added_by')
            ->get();

        return view('pharmacy::livewire.purchases.purchase-list', ['purchase' => $purchase, 'purchase_receives' => $purchase_receives, 'payments' => $payments]);
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    private function stats()
    {

        $purchase = Purchase::from('purchases as p')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->select('p.status','p.id','is_paid')
            ->get()->groupBy('status')->toArray();


        $this->po_unapproved = PurchaseOrder::whereIn('purchase_id',array_column($purchase['approval-awaiting'] ?? [],'id'))
            ->select(DB::raw('count(distinct purchase_id) as total'),DB::raw('sum(purchase_orders.qty * purchase_orders.cost_of_price) as total_cost_order'))->get();

        $this->po_approved = PurchaseOrder::whereIn('purchase_id',array_column($purchase['awaiting-delivery'] ?? [],'id'))
            ->select(DB::raw('count(distinct purchase_id) as total'),DB::raw('sum(purchase_orders.qty * purchase_orders.cost_of_price) as total_cost_order'))->get();


        $this->stock_receiving_in_process = \Devzone\Pharmacy\Models\PurchaseReceive::whereIn('purchase_id',array_column($purchase['receiving'] ?? [],'id'))
            ->select(DB::raw('count(distinct purchase_id) as total'),DB::raw('sum(purchase_receives.qty * purchase_receives.after_disc_cost) as total_cost_order'))->get();


        $received = collect($purchase['received'] ?? [])->groupBy('is_paid')->toArray();

        $this->unpaid_invoices = \Devzone\Pharmacy\Models\PurchaseReceive::whereIn('purchase_id',array_column($received['f'] ?? [],'id'))
            ->select(DB::raw('count(distinct purchase_id) as total'),DB::raw('sum(purchase_receives.qty * purchase_receives.after_disc_cost) as total_cost_order'))->get();

        $this->order_completed = \Devzone\Pharmacy\Models\PurchaseReceive::whereIn('purchase_id',array_column($received['t'] ?? [],'id'))
            ->select(DB::raw('count(distinct purchase_id) as total'),DB::raw('sum(purchase_receives.qty * purchase_receives.after_disc_cost) as total_cost_order'))->get();
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

    public function search()
    {
        $this->resetPage();
//        $this->supplier_id_s = $this->supplier_id;

    }

    public function resetSearch()
    {
        $this->resetPage();
        $this->dispatchBrowserEvent('clear');
        $this->reset(['supplier_id_s', 'supplier_name', 'supplier_id', 'supplier_invoice', 'status', 'from', 'to']);
    }
}
