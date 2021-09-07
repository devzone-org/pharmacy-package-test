<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HourlyTrends extends Component
{
    public $date;
    public $pre_to;
    public $to;
    public $from;
    public $type;
    public $label = [];
    public $labels = [];
    public $hour_format = [];
    public $label_plucked;
    public $display_date;

    public $data = [];
    public $report = [];
    public $dr_label;
    public $char_data = [];
    public $char_data_value = [];

    public function mount()
    {
        $this->type = 'date';
        $this->date = date('Y-m-d');

        $this->prepareDate();
    }

    public function prepareDate()
    {
        $this->pre_to = new Carbon($this->date);
        if ($this->type == 'date') {
            $this->reset('label');
            $this->from = $this->pre_to->copy();
            $this->to = $this->pre_to->copy()->endOfDay();
            $this->display_date = date('d M Y', strtotime($this->pre_to));
        } elseif ($this->type == 'week') {
            $this->reset('label');
            $this->from = $this->pre_to->copy()->startOfWeek();
            $this->to = $this->pre_to->copy()->endOfWeek();
            $this->display_date = date('d M Y', strtotime($this->from)) . ' - ' . date('d M Y', strtotime($this->pre_to));
        } elseif ($this->type == 'month') {
            $this->reset('label');
            $this->from = $this->pre_to->copy()->firstOfMonth();
            $this->to = $this->pre_to->copy()->endOfMonth();
            $this->display_date = date('M Y', strtotime($this->from));
        }

        $this->label_plucked = json_encode(collect($this->label)->pluck('label')->toArray());


    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.hourly-trends');
    }

    public function search()
    {

        $this->reset(['data', 'report', 'char_data', 'char_data_value','labels','hour_format']);
        for ($iHours = 0; $iHours <= 23; $iHours++) {
            $this->labels[] = $iHours;
            $this->hour_format[] = $iHours;
        }
        $this->report = Sale::from('sales as s')
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->to);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })
            ->select(
                DB::raw('hour(s.sale_at) as hour'),
                DB::raw('sum(s.gross_total) as gross_total'),
                DB::raw('count((s.id)) as no_of_sale'),
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

        $this->dispatchBrowserEvent('hour-summary', ['val' => ($this->char_data), 'vol' => ($this->char_data_value)]);
    }

    public function changeType($type)
    {
        $this->date = date('Y-m-d');
        $this->type = $type;
        $this->prepareDate();
    }

    public function changeDate($direction)
    {
        if ($direction == 'prev') {
            if ($this->type == 'date') {
                $this->date = date('Y-m-d', strtotime('-1 day', strtotime($this->date)));
            } elseif ($this->type == 'week') {
                $this->date = date('Y-m-d', strtotime('-1 week', strtotime($this->date)));
            } elseif ($this->type == 'month') {
                $this->date = date('Y-m-d', strtotime('-1 month', strtotime($this->date)));
            }

        } elseif ($direction == 'next') {
            if ($this->type == 'date') {
                $this->date = date('Y-m-d', strtotime('+1 day', strtotime($this->date)));
            } elseif ($this->type == 'week') {
                $this->date = date('Y-m-d', strtotime('+1 week', strtotime($this->date)));
            } elseif ($this->type == 'month') {
                $this->date = date('Y-m-d', strtotime('+1 month', strtotime($this->date)));
            }
        }
        $this->prepareDate();
    }
}
