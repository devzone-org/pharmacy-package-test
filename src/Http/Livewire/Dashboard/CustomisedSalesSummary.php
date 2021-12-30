<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Http\Traits\DashboardDate;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomisedSalesSummary extends Component
{
    use DashboardDate;

    public $data = [];
    public $result = [];

    public function mount()
    {
        $this->type = 'date';
        $this->date = date('Y-m-d');
        $this->prepareDate();

    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.customised-sales-summary');
    }

    public function search()
    {
        $this->reset(['result', 'data']);
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->select(
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
            ->select(
                'sd.sale_id',
                DB::raw('DATE(s.sale_at) as date'),
                DB::raw('MONTH(s.sale_at) as month'),
                DB::raw('WEEK(s.sale_at) as week'),
                DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos')
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
            $first = collect($sale_return)->where($this->type, $s[$this->type])->first();
            $sale[$key]['return_total'] = $first['return_total'] ?? 0;
            $sale[$key]['net_sale'] = $sale[$key]['total_after_disc'] - ($first['return_total'] ?? 0);
            $sale[$key]['return_cos'] = $first['return_cos'] ?? 0;
            $sale[$key]['net_cos'] = $sale[$key]['cos'] - ($first['return_cos'] ?? 0);
            $sale[$key]['gross_profit'] = $sale[$key]['net_sale'] - $sale[$key]['net_cos'];
        }

        foreach ($this->label as $l) {
            $record = collect($sale)->where($this->type, $l['format'])->first();
            if (empty($record)) {
                $this->data[] = [
                    "{$this->type}" => $l['format'],
                    'cos' => 0,
                    'net_sale' => 0,
                    'gross_profit' => 0
                ];
            } else {
                $this->data[] = [
                    "{$this->type}" => $l['format'],
                    'cos' => round($record['net_cos']),
                    'net_sale' => round($record['net_sale']),
                    'gross_profit' => round($record['gross_profit'])
                ];
            }
        }


        $this->result[] = [
            'name' => 'Sale',
            'data' => collect($this->data)->pluck('net_sale')->toArray()
        ];
        $this->result[] = [
            'name' => 'Cost of Sale',
            'data' => collect($this->data)->pluck('cos')->toArray()
        ];
        $this->result[] = [
            'name' => 'Gross Profit',
            'data' => collect($this->data)->pluck('gross_profit')->toArray()
        ];
        $this->result = json_encode($this->result);

        $this->dispatchBrowserEvent('sale-summary', ['result' => $this->result, 'label' => $this->label_plucked]);
    }

}
