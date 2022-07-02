<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Carbon\Carbon;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Devzone\Pharmacy\Models\Sale\SaleRefundDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleReturnTransaction extends Component
{
    public $salemen = [];
    public $salesman_id;
    public $range;
    public $from;
    public $to;
    public $time_from;
    public $time_to;
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->salemen = Sale::from('sales as s')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->groupBy('s.sale_by')
            ->select('u.id', 'u.name')
            ->get()
            ->toArray();
        $this->from = date('d M Y', strtotime('-7 days'));
        $this->to = date('d M Y');
        $this->range = 'seven_days';
        $this->search();
    }


    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function render()
    {
        return view('pharmacy::livewire.reports.sales-return-transaction');
    }

    public function search()
    {
        $this->report = SaleRefundDetail::from('sale_refund_details as srd')
            ->join('sales as s', 's.id', '=', 'srd.sale_id')
            ->join('sales as rs', 'rs.id', '=', 'srd.refunded_id')
            ->leftJoin('patients as p', 'p.id', '=', 'rs.patient_id')
            ->join('products as pr', 'pr.id', '=', 'srd.product_id')
            ->join('sale_details as sd', 'sd.id', 'srd.sale_detail_id')
            ->leftJoin('users as sb','sb.id','=','s.sale_by')
            ->leftJoin('users as rb','rb.id','=','rs.sale_by')
            ->select('srd.created_at as return_date', 's.sale_at as original_sale_date',
                'srd.refunded_id as invoice_no', 'p.name as patient_name', 'p.mr_no', 'pr.name as product_name',
                's.gross_total as original_invoice_total', 'srd.refund_qty','sb.name as sale_by','rb.name as return_by',
                DB::raw('(sd.retail_price_after_disc*srd.refund_qty) as refund_value'))
            ->when(!empty($this->salesman_id), function ($q) {
                return $q->where('rs.sale_by', $this->salesman_id);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('srd.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('srd.created_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->time_to), function ($q) {
                return $q->whereTime('srd.created_at', '<=', date('H:i:s', strtotime($this->time_to)));
            })
            ->when(!empty($this->time_from), function ($q) {
                return $q->whereTime('srd.created_at', '>=', date('H:i:s', strtotime($this->time_from)));
            })
            ->orderBy('srd.created_at', 'desc')
            ->get()->toArray();

    }

    public function resetSearch()
    {
        $this->reset('salesman_id');
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
