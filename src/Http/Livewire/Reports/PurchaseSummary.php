<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseSummary extends Component
{
    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-7 days'));
        $this->to = date('Y-m-d');
        $this->range = 'seven_days';
        $this->search();
    }
    public function render(){
        return view('pharmacy::livewire.reports.purchase-summary');
    }
    public function search()
    {
        $this->report =Purchase::from('purchases as p')
            ->leftJoin('purchase_orders as po','po.purchase_id','=','p.id')
            ->leftJoin('suppliers as s','s.id','=','p.supplier_id')
            ->leftJoin('users as u','u.id','=','p.created_by')
            ->leftJoin('users as us','us.id','=','p.approved_by')
            ->leftJoin('supplier_payment_details as spd','spd.order_id','=','p.id')
            ->leftJoin('supplier_payments as sp','sp.id','=','spd.supplier_payment_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at', '<=', $this->to);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at', '>=', $this->from);
            })
            ->groupBy('po.purchase_id')
            ->orderBy('p.id','ASC')
            ->select(
                's.name as supplier_name',
                'p.created_at as placement_date',
                'u.name as created_by',
                'us.name as approved_by',
                'p.id as po_no','p.delivery_date as receiving_date','p.grn_no','p.supplier_invoice','p.is_paid',
                'sp.payment_date','sp.created_at as invoice_date',
                DB::raw('sum(po.total_cost) as po_value'),
                DB::raw('sum(po.qty*po.cost_of_price) as cos'),
            )

            ->get()
            ->toArray();
    }
    public function updatedRange($val)
    {
        if ($val == 'custom_range') {
            $this->date_range = true;

        } elseif ($val == 'seven_days') {
            $this->date_range = false;
            $this->from = date('Y-m-d', strtotime('-7 days'));
            $this->to = date('Y-m-d');
            $this->search();
        } elseif ($val == 'thirty_days') {
            $this->date_range = false;
            $this->from = date('Y-m-d', strtotime('-30 days'));
            $this->to = date('Y-m-d');
            $this->search();
        } elseif ($val == 'yesterday') {
            $this->date_range = false;
            $this->from = date('Y-m-d', strtotime('-1 days'));
            $this->to = date('Y-m-d', strtotime('-1 days'));
            $this->search();
        } elseif ($val == 'today') {
            $this->date_range = false;
            $this->from = date('Y-m-d');
            $this->to = date('Y-m-d');
            $this->search();
        }
    }
}