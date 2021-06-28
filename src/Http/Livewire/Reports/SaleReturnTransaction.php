<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleReturnTransaction extends Component
{
    public $from;
    public $to;
    public $report = [];

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-10 days'));
        $this->to = date('Y-m-d');
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.sales-return-transaction');
    }

    public function search()
    {
        $this->report = SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->join('sales as s','s.id','=','sr.sale_id')
            ->join('products as pr','pr.id','=','sd.product_id')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->whereDate('sr.updated_at', '<=', $this->to)
            ->whereDate('sr.updated_at', '>=', $this->from)
            ->select('s.sale_at', 'sd.sale_id','sr.id','pr.name as product_name',
                'sr.updated_at as return_date',
                DB::raw("(SELECT SUM(sale_details.total_after_disc) FROM sale_details
                                WHERE sale_details.sale_id = sr.sale_id) as total"),
                DB::raw('sum(sd.total_after_disc) as return_total'),'sr.refund_qty',
                'p.name as patient_name')
            ->groupBy('sr.sale_detail_id')->get()->toArray();


    }
}
