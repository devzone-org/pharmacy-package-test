<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Devzone\Pharmacy\Models\Sale\OpenReturn;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleSummary extends Component
{
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
        return view('pharmacy::livewire.reports.sale-summary');
    }


    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function search()
    {

        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->select(
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(s.on_account) as credit'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
                DB::raw('count(DISTINCT(s.patient_id)) as unique_customers'),
                DB::raw('count(DISTINCT(sd.product_id)) as no_of_items'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->groupBy('date')
            ->get();

//        $sale_return=SaleRefund::from('sale_refunds as sr')
//            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
//            ->join('sales as s', 's.id', '=', 'sr.sale_id')
//            ->when(!empty($this->to), function ($q) {
//                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
//            })
//            ->when(!empty($this->from), function ($q) {
//                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
//            })
//            ->select(DB::raw('DATE(s.sale_at) as date'),'sd.sale_id',DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
//                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos')
//            )
//            ->groupBy('sr.sale_detail_id')->get();
//
        $sale_return = Sale::from('sales as s')
            ->join('sale_refund_details as sfd', 'sfd.refunded_id', '=', 's.id')
            ->join('sale_details as sd', 'sd.id', '=', 'sfd.sale_detail_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->where('s.refunded_id', '>', 0)
            ->select(DB::raw('DATE(s.sale_at) as date'), DB::raw('sum(sd.retail_price_after_disc*sfd.refund_qty) as return_total'), DB::raw('sum(sd.supply_price*sfd.refund_qty) as return_cos'))
            ->groupBy('sfd.sale_detail_id')->get();

        $open_return = OpenReturn::from('open_returns as op')
            ->join('open_return_details as opd', 'opd.open_return_id', '=', 'op.id')
            ->join('product_inventories as pi', 'pi.product_id', '=', 'opd.product_id')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('op.created_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('op.created_at', '>=', $this->formatDate($this->from));
            })
            ->groupBy('op.created_at')
            ->select(DB::raw('DATE(op.created_at) as date'), DB::raw('sum(opd.total_after_deduction*opd.qty) as total_open_return'), DB::raw('sum(pi.supply_price*opd.qty) as open_return_cos'))
            ->get();



        $period = CarbonPeriod::create($this->from, $this->to);

        // Iterate over the period
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $this->report = [];
        foreach ($dates as $d) {

            $found = $sale->where('date', $d);
            if ($found->isNotEmpty()) {
                foreach ($sale as $s)
                    $this->report[] = [
                        'date' => $d,
                        'total' => $s->total,
                        'total_after_disc' => $s->total_after_disc,
                        'sale_return' => $s->sale_return,
                        'cos' => $s->cos,
                        'no_of_sale' => $s->no_of_sale,
                        'unique_customers' => $s->unique_customers,
                        'no_of_items' => $s->no_of_items,
                    ];

            } else {

                $this->report[] = [
                    'date' => $d,
                    'total' => 0,
                    'total_after_disc' => 0,
                    'sale_return' => 0,
                    'cos' => 0,
                    'no_of_sale' => 0,
                    'unique_customers' => 0,
                    'no_of_items' => 0,
                ];
            }

        }

        foreach ($this->report as $key => $rep) {
            if ($sale_return->isNotEmpty() || $open_return->isNotEmpty()) {
                $this->report[$key]['sale_return'] = $sale_return->where('date', $rep['date'])->sum('return_total') + $open_return->where('date', $rep['date'])->sum('total_open_return');
                $this->report[$key]['cos'] = $this->report[$key]['cos'] - $sale_return->where('date', $rep['date'])->sum('return_cos') - $open_return->where('date', $rep['date'])->sum('open_return_cos');
            } else {
                $this->report[$key]['sale_return'] = 0;
            }
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
}