<?php

namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Livewire\Component;

class SalePurchaseNarcoticDrugs extends Component
{
    use Searchable;

    public $range;
    public $from;
    public $to;
    public $report = [];
    public $date_range = false;
    public $product_id;
    public $product_name;
    public $opening_inv;


    public function mount()
    {
        $this->from = date('d M Y', strtotime('-7 days'));
        $this->to = date('d M Y');
        $this->range = 'seven_days';
//        $this->search();
    }

    public function search()
    {
        if (!empty($this->product_id)) {
            $this->report = \Devzone\Pharmacy\Models\InventoryLedger::from('inventory_ledgers as il')
                ->join('products as p', 'p.id', '=', 'il.product_id')
                ->leftJoin('sales as s', 's.id', '=', 'il.sale_id')
                ->leftJoin('purchases as po', 'po.id', '=', 'il.order_id')
                ->leftJoin('patients as pat', 'pat.id', '=', 's.patient_id')
                ->leftJoin('employees as em', 'em.id', '=', 's.referred_by')
                ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
                ->where('p.control_medicine', 't')
                ->where('il.product_id', $this->product_id)
                ->whereIn('il.type', ['sale', 'purchase'])
                ->when(!empty($this->to), function ($q) {
                    return $q->whereDate('il.created_at', '<=', $this->formatDate($this->to));
                })
                ->when(!empty($this->from), function ($q) {
                    return $q->whereDate('il.created_at', '>=', $this->formatDate($this->from));
                })
                ->select('il.type', 'p.name as product', 'il.created_at', 'em.name as doctor', 'em.is_doctor', 'pat.name as patient', 'il.increase', 'il.decrease', 'm.name as manufacture')
                ->orderBy('created_at', 'desc')
                ->get()->toArray();

        }
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

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function render()
    {
        return view('pharmacy::livewire.reports.sale-purchase-narcotic-drugs');
    }


}