<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Http\Traits\DashboardDate;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomisedSalesSummaryUserwise extends Component
{
    use DashboardDate;

    public $data = [];
    public $prepare_data;
    public $count;

    public function mount()
    {
        $this->prepareDate();
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.customised-sales-summary-userwise');
    }

    public function search()
    {
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->groupBy('s.sale_by')
            ->select(
                's.sale_by', 'u.name as user',
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->when($this->type == 'month', function ($q) {
                return $q->groupBy('month')
                    ->orderBy('month');
            })
            ->when($this->type == 'week', function ($q) {
                return $q->groupBy('week')
                    ->orderBy('week');
            })
            ->when($this->type == 'date', function ($q) {
                return $q->groupBy('date')
                    ->orderBy('date');
            })
            ->get()
            ->toArray();
        $sale_return = SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->join('sales as s', 's.id', '=', 'sr.sale_id')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->groupBy('s.sale_by')
            ->select(
                'sd.sale_id',
                's.sale_by',
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos',
                )
            )
            ->when($this->type == 'month', function ($q) {
                return $q->groupBy('month')
                    ->orderBy('month');
            })
            ->when($this->type == 'week', function ($q) {
                return $q->groupBy('week')
                    ->orderBy('week');
            })
            ->when($this->type == 'date', function ($q) {
                return $q->groupBy('date')
                    ->orderBy('date');
            })
            ->get()
            ->toArray();

        foreach ($sale as $key => $s) {
            if ($this->type == 'month') {
                $first = collect($sale_return)->where('month', $s['month'])->where('sale_by', $s['sale_by'])->first();
                $sale[$key]['return_total'] = $first['return_total'];
                $sale[$key]['return_cos'] = $first['return_cos'];
                $sale[$key]['net_sale'] = $sale[$key]['total_after_disc'] - $first['return_total'];
            } elseif ($this->type == 'week') {
                $first = collect($sale_return)->where('week', $s['week'])->where('sale_by', $s['sale_by'])->first();
                $sale[$key]['return_total'] = $first['return_total'];
                $sale[$key]['return_cos'] = $first['return_cos'];
                $sale[$key]['net_sale'] = $sale[$key]['total_after_disc'] - $first['return_total'];
            } elseif ($this->type == 'date') {
                $first = collect($sale_return)->where('date', $s['date'])->where('sale_by', $s['sale_by'])->first();
                $sale[$key]['return_total'] = $first['return_total'];
                $sale[$key]['return_cos'] = $first['return_cos'];
                $sale[$key]['net_sale'] = $sale[$key]['total_after_disc'] - $first['return_total'];
            }
        }
        $this->data = collect($sale)->groupBy('sale_by')->toArray();
        $color=['#5bd6aa','#fcb37b','#5dc2df','#d9534f','#047857','#fca5a5'];
        foreach ($this->data as $key=>$d) {
            $first = collect($d)->first();
            $net_sale = collect($d)->pluck('net_sale')->toArray();
            $this->prepare_data[] = [
                'label' => $first['user'],
                'data' => $net_sale,
                'borderColor' => $color[$key],
                'backgroundColor' => $color[$key],
            ];
        }

    }
}