<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseSummary extends Component
{

    use Searchable;

    public $supplier_name;
    public $supplier_id;
    public $manufacture_name;
    public $manufacture_id;
    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('d M Y', strtotime('-7 days'));
        $this->to = date('d M Y');
        $this->range = 'seven_days';
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.purchase-summary');
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function search()
    {

        $this->report = Purchase::from('purchases as p')
            ->leftJoin('purchase_receives as po', 'po.purchase_id', '=', 'p.id')
            ->leftJoin('products as pro', 'pro.id', '=', 'po.product_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->leftJoin('manufactures as m', 'm.id', '=', 'pro.manufacture_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.created_by')
            ->leftJoin('users as us', 'us.id', '=', 'p.approved_by')
            ->when(!empty($this->supplier_id), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('m.id', $this->manufacture_id);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at', '>=', $this->formatDate($this->from));
            })
            ->groupBy('po.purchase_id')
            ->orderBy('p.id', 'ASC')
            ->select(
                's.name as supplier_name',
                'p.created_at as placement_date',
                'u.name as created_by',
                'us.name as approved_by',
                'p.id as po_no', 'p.delivery_date as receiving_date', 'p.grn_no', 'p.supplier_invoice', 'p.is_paid',
                'p.advance_tax', 'm.name as manufacture_name',
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
            $this->from = date('d M Y', strtotime('-7 days'));
            $this->to = date('d M Y');
            $this->search();
        } elseif ($val == 'thirty_days') {
            $this->date_range = false;
            $this->from = date('d M Y', strtotime('-30 days'));
            $this->to = date('d M Y');
            $this->search();
        } elseif ($val == 'yesterday') {
            $this->date_range = false;
            $this->from = date('d M Y', strtotime('-1 days'));
            $this->to = date('d M y', strtotime('-1 days'));
            $this->search();
        } elseif ($val == 'today') {
            $this->date_range = false;
            $this->from = date('d M Y');
            $this->to = date('d M Y');
            $this->search();
        }
    }
}