<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleHourlyGraph extends Component
{
    public $range;
    public $from;
    public $to;
    public $date_range = false;
    public $report = [];
    public $labels = [];
    public $labels_value = [];
    public $char_data = [];
    public $char_data_value = [];
    public $hour_format = [];

    public function mount()
    {
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


    public function search()
    {
        $this->reset('labels', 'hour_format', 'report', 'char_data_value', 'char_data');
        for ($iHours = 0; $iHours <= 23; $iHours++) {
            $this->labels[] = $iHours;
            $this->hour_format[] = $iHours;
        }
        $this->report = Sale::from('sales as s')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->formatDate($this->from));
            })
            ->select(
                DB::raw('hour(s.sale_at) as hour'),
                DB::raw('sum(s.gross_total) as gross_total'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
            )
            ->groupBy('hour')
            ->get()
            ->toArray();


        foreach ($this->hour_format as $key => $r) {
            $get_data = collect($this->report)->where('hour', '=', $r)->first();
            if (!empty($get_data)) {
                $this->char_data[$key] = $get_data['no_of_sale'];
                $this->char_data_value[$key] = (int)$get_data['gross_total'];
            } else {
                $this->char_data[$key] = 0;
                $this->char_data_value[$key] = 0;
            }
        }

        $this->dispatchBrowserEvent('hour-summary', ['val' =>  ($this->char_data), 'vol' =>  ($this->char_data_value)]);
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.sale-hourly-graph');
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
