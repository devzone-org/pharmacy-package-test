<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


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
    use WithPagination, Searchable;

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
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
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
                'p.id','p.advance_tax','p.supplier_id','p.supplier_invoice','p.grn_no','p.is_paid','p.delivery_date','p.expected_date','p.status',
                'p.approved_at','p.created_at',
                'a.name as approved_by','s.name as supplier_name','c.name as created_by',
                DB::raw('SUM(po.total_cost) as cost_before_receiving')
            )
            ->groupBy('p.id')
            ->orderBy('p.id', 'desc')
            ->paginate(20);
        $purchase_receives=Purchase::from('purchases as p')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
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
            ->select('p.id',DB::raw('SUM(pr.total_cost) as cost_after_receiving'))
            ->groupBy('p.id')
            ->get();
        $payments=Purchase::from('purchases as p')
            ->leftJoin('supplier_payment_details as spd','spd.order_id','=','p.id')
            ->leftJoin('supplier_payments as sp','sp.id','=','spd.supplier_payment_id')
            ->leftJoin('users as u','u.id','=','sp.added_by')
            ->leftJoin('users as us','us.id','=','sp.approved_by')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('sp.supplier_id', $this->supplier_id_s);
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
            ->select('spd.order_id','sp.created_at','sp.approved_at','us.name as approved_by','u.name as added_by')
            ->get();
        return view('pharmacy::livewire.purchases.purchase-list', ['purchase' => $purchase,'purchase_receives'=>$purchase_receives,'payments'=>$payments]);
    }

    private function stats()
    {
        $this->po_unapproved = Purchase::from('purchases as p')
            ->leftJoin('purchase_orders as po', 'po.purchase_id', '=', 'p.id')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->where('p.status', 'approval-awaiting')
            ->select(
                'p.id', 'p.status', 'p.is_paid',
                DB::raw('count(distinct p.id) as total')
                , DB::raw('sum(po.qty * po.cost_of_price) as total_cost_order'))
            ->groupBy('p.id')
            ->get();
        $this->po_approved = Purchase::from('purchases as p')
            ->leftJoin('purchase_orders as po', 'po.purchase_id', '=', 'p.id')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->where('p.status', 'awaiting-delivery')
            ->select(
                'p.id', 'p.status', 'p.is_paid', DB::raw('count(distinct p.id) as total')
                , DB::raw('sum(po.qty * po.cost_of_price) as total_cost_order'))
            ->groupBy('p.id')
            ->get();
        $this->stock_receiving_in_process = Purchase::from('purchases as p')
            ->leftJoin('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->where('p.status', 'receiving')
            ->select(
                'p.id', 'p.status', 'p.is_paid', DB::raw('count(distinct p.id) as total')
                , DB::raw('sum(pr.qty * pr.after_disc_cost) as total_cost_order'))
            ->groupBy('p.id')
            ->get();
        $this->unpaid_invoices = Purchase::from('purchases as p')
            ->leftJoin('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->where('p.status', 'received')
            ->where('p.is_paid', 'f')
            ->select(
                'p.id', 'p.status', 'p.is_paid', DB::raw('count(distinct p.id) as total')
                , DB::raw('sum(pr.qty * pr.after_disc_cost) as total_cost_order'))
            ->groupBy('p.id')
            ->get();
        $this->order_completed = Purchase::from('purchases as p')
            ->leftJoin('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at','>=', date('Y-m-d',strtotime($this->from)));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at','<=', date('Y-m-d',strtotime($this->to)));
            })
            ->when(!empty($this->supplier_id_s), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id_s);
            })
            ->where('p.status', 'received')
            ->where('p.is_paid', 't')
            ->select(
                'p.id', 'p.status', 'p.is_paid', DB::raw('count(distinct p.id) as total')
                , DB::raw('sum(pr.qty * pr.after_disc_cost) as total_cost_order'))
            ->groupBy('p.id')
            ->get();
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

        $this->supplier_id_s = $this->supplier_id;

    }

    public function resetSearch()
    {
        $this->resetPage();
        $this->reset(['supplier_id_s', 'supplier_name', 'supplier_id', 'supplier_invoice', 'status','from','to']);
    }
}
