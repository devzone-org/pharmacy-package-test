<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopSellingProducts extends Component
{

    public $date;
    public $pre_to;
    public $to;
    public $from;
    public $type;
    public $label=[];
    public $label_plucked;
    public $display_date;


    public $report_type;
    public $data = [];

    public function mount($report_type)
    {
        $this->report_type = $report_type;
        $this->date = date('Y-m-d');
        $this->type = 'date';
        $this->prepareDate();

    }

    public function render()
    {

        $this->search();
        return view('pharmacy::livewire.dashboard.top-selling-products-revenuewise');
    }

    public function search()
    {
        if ($this->report_type == 'revenue') {
            $sale = DB::select("SELECT
                    sum(sd.total_after_disc) AS total,
                    p. `name`,s.`name` AS supplier,
                    count('sd.product_id') AS count_product,
                    sum(sr.refund_qty) AS refund,
                    sum(sd.qty) AS total_sale_qty,
                    sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos,
                    sum(sd.total_after_disc) / sum(sd.qty) AS unit,
                    sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) AS total_refund,
                    sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) AS total_after_refund,
                    sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) AS total_profit
                    FROM
                    sale_details AS sd
                    LEFT JOIN sale_refunds AS sr ON sd.id = sr.sale_detail_id
                    JOIN product_inventories AS pv ON pv.id = sd.product_inventory_id
                    LEFT JOIN purchases AS pur ON pur.id = pv.po_id
                    LEFT JOIN suppliers AS s ON s.id = pur.supplier_id
                    JOIN products AS p ON p.id = sd.product_id
                    where sd.created_at BETWEEN '" . $this->from . "' AND '" . $this->to . "'

                GROUP BY
                    sd.product_id
                ORDER BY
                    total_after_refund DESC
                LIMIT 5;");
        } elseif ($this->report_type == 'profit') {
            $sale = DB::select("SELECT
                    sum(sd.total_after_disc) AS total,
                    p. `name`,s.`name` AS supplier,
                    count('sd.product_id') AS count_product,
                    sum(sr.refund_qty) AS refund,
                    sum(sd.qty) AS total_sale_qty,
                    sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos,
                    sum(sd.total_after_disc) / sum(sd.qty) AS unit,
                    sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) AS total_refund,
                    sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) AS total_after_refund,
                    sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) AS total_profit
                    FROM
                    sale_details AS sd
                    LEFT JOIN sale_refunds AS sr ON sd.id = sr.sale_detail_id
                    JOIN product_inventories AS pv ON pv.id = sd.product_inventory_id
                    LEFT JOIN purchases AS pur ON pur.id = pv.po_id
                    LEFT JOIN suppliers AS s ON s.id = pur.supplier_id
                    JOIN products AS p ON p.id = sd.product_id
                    where sd.created_at BETWEEN '" . $this->from . "' AND '" . $this->to . "'

                GROUP BY
                    sd.product_id
                ORDER BY
                    total_profit DESC
                LIMIT 5;");
        }
        $this->data = $sale;
    }




    public function prepareDate(){
        $this->pre_to=new Carbon($this->date);
        if ($this->type=='date'){
            $this->reset('label');
            $this->from=$this->pre_to->copy();
            $this->to=$this->pre_to->copy();
            $this->display_date=date('d M Y',strtotime($this->pre_to));
        }elseif ($this->type=='week'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->startOfWeek();
            $this->to=$this->pre_to->copy()->endOfWeek();
            $this->display_date=date('d M Y',strtotime($this->from)).' - '.date('d M Y',strtotime($this->pre_to));
        }elseif ($this->type=='month'){
            $this->reset('label');
            $this->from=$this->pre_to->copy()->firstOfMonth();
            $this->to=$this->pre_to->copy()->endOfMonth();
            $this->display_date=date('M Y',strtotime($this->from));
        }

        $this->label_plucked=json_encode(collect($this->label)->pluck('label')->toArray());


    }
    public function changeType($type){
        $this->date = date('Y-m-d');
        $this->type=$type;
        $this->prepareDate();
    }

    public function changeDate($direction){
        if ($direction=='prev'){
            if ($this->type=='date'){
                $this->date=date('Y-m-d', strtotime('-1 day', strtotime($this->date)));
            }elseif ($this->type=='week'){
                $this->date=date('Y-m-d', strtotime('-1 week', strtotime($this->date)));
            }elseif ($this->type=='month'){
                $this->date=date('Y-m-d', strtotime('-1 month', strtotime($this->date)));
            }

        }elseif($direction=='next'){
            if ($this->type=='date'){
                $this->date=date('Y-m-d', strtotime('+1 day', strtotime($this->date)));
            }elseif ($this->type=='week'){
                $this->date=date('Y-m-d', strtotime('+1 week', strtotime($this->date)));
            }elseif ($this->type=='month'){
                $this->date=date('Y-m-d', strtotime('+1 month', strtotime($this->date)));
            }
        }
        $this->prepareDate();
    }
}
