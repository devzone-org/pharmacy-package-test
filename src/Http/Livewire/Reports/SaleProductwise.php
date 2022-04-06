<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use App\Models\Hospital\Department;
use App\Models\Hospital\Employees\Employee;
use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleProductwise extends Component
{
    use Searchable;
    public $range;
    public $from;
    public $to;
    public $product_id;
    public $product_name;
    public $report = [];
    public $products = [];
    public $date_range = false;

    public function mount()
    {
        $this->from = date('d M Y', strtotime('-7 days'));
        $this->to = date('d M Y');
        $this->range = 'seven_days';
        $this->search();
    }
    public function render(){
        return view('pharmacy::livewire.reports.sale-productwise');
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }

    public function search()
    {
        $this->report = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
            ->join('products as p','p.id','=','sd.product_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=',  $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->when(!empty($this->product_id),function ($q){
                return $q->where('sd.product_id',$this->product_id);
            })
            ->select(
                'p.name as product_name',
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
                DB::raw('sum(sr.refund_qty) as refund_qty'),
                DB::raw('sum(sd.qty) as qty'),
                DB::raw('sum(sd.qty) - sum(coalesce(sr.refund_qty,0)) as total_sale_qty'),
                DB::raw('sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) as unit'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) as total_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) as total_after_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) as total_profit'),
            )
            ->groupBy('sd.product_id')
            ->orderBy('qty','DESC')
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