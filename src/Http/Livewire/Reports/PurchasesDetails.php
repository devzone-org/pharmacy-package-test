<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchasesDetails extends Component
{
    use Searchable;
    public $range;
    public $from;
    public $to;
    public $supplier_id;
    public $supplier_name;
    public $manufacture_id;
    public $manufacture_name;
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('d M Y', strtotime('-7 days'));
        $this->to = date('d M Y');
        $this->range = 'seven_days';
        $this->search();
    }

    public function render(){
        return view('pharmacy::livewire.reports.purchases-details');
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }

    public function search()
    {
        $this->report =Purchase::from('purchases as p')
            ->leftJoin('purchase_receives as pr','pr.purchase_id','=','p.id')
            ->leftJoin('products as pro','pro.id','=','pr.product_id')
            ->leftJoin('suppliers as s','s.id','=','p.supplier_id')
            ->leftJoin('manufactures as m','m.id','=','pro.manufacture_id')

            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('p.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('p.created_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->supplier_id), function ($q) {
                return $q->where('p.supplier_id', $this->supplier_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('pro.manufacture_id', $this->manufacture_id);
            })
            ->orderBy('p.id','ASC')
            ->select(
                'p.id as po_no','p.created_at as placement_date','p.delivery_date as receiving_date','p.grn_no','p.supplier_invoice',
                's.name as supplier_name','pro.name as product_name', 'm.name as manufacture_name',
                'p.advance_tax',
                'pr.qty','pr.cost_of_price','pr.retail_price','pr.total_cost','pr.bonus','pr.discount','pr.batch_no','pr.expiry','pr.after_disc_cost'
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
            $this->to = date('d M Y', strtotime('-1 days'));
            $this->search();
        } elseif ($val == 'today') {
            $this->date_range = false;
            $this->from = date('d M Y');
            $this->to = date('d M Y');
            $this->search();
        }
    }

}