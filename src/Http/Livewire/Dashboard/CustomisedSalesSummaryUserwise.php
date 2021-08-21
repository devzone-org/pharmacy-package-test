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
    public $result = [];
    public $count;

    public function mount()
    {
        $this->type='date';
        $this->date=date('Y-m-d');
        $this->prepareDate();
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.customised-sales-summary-userwise');
    }

    public function search()
    {
        $this->reset(['result', 'data']);
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
                $first = collect($sale_return)->where($this->type, $s[$this->type])->where('sale_by', $s['sale_by'])->first();
                $sale[$key]['return_total'] = $first['return_total'];
                $sale[$key]['return_cos'] = $first['return_cos'];
                $sale[$key]['net_sale'] = $sale[$key]['total_after_disc'] - $first['return_total'];
        }

        $user_name = array_unique(collect($sale)->pluck('user')->toArray());

        foreach ($this->label as $l) {
            foreach ($user_name as $us){
                $record = collect($sale)->where('user',$us)->where($this->type, $l['format'])->first();
                if (empty($record)) {
                    $this->data[] = [
                        "{$this->type}" => $l['format'],
                        "user" => $us,
                        'net_sale' => 0,

                    ];
                } else {
                    $this->data[] = [
                        "{$this->type}" => $l['format'],
                        'net_sale' => round($record['net_sale']),
                        "user" => $us,
                    ];
                }
            }
        }


        foreach(collect($this->data)->groupBy('user')->toArray() as $user => $details){

            $this->result[] = [
                'name' => $user,
                'data' => collect($details)->pluck('net_sale')->toArray()
            ];
        }
//dd($this->result);

        $this->result = json_encode($this->result);

        $this->dispatchBrowserEvent('userwise-sale', ['result' => $this->result, 'label' => $this->label_plucked]);
    }
}
