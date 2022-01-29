<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Ams\Models\Ledger;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonthwiseSalesSummary extends Component
{
    public $data = [];
    public $date;
    public $to;
    public $prev;
    public $prev_2;
    public $from;

    public function mount()
    {
        $this->to = Carbon::now();
        $this->from = $this->to->copy()->subMonth(2)->firstOfMonth();
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.monthwise-sales-summary');
    }

    public function search()
    {
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->select(
                's.sale_at',
                DB::raw('DATE_FORMAT(s.sale_at,"%Y%m") as month'),
                DB::raw('sum(sd.total_after_disc) as total'),
                DB::raw('count(distinct(s.id)) as no_of_sale'),
                DB::raw('sum(sr.refund_qty) as refund'),
                DB::raw('sum(sd.qty) as total_sale_qty'),
                DB::raw('sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price) as cos'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) as unit'),
                DB::raw('sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0)) as total_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) as total_after_refund'),
                DB::raw('sum(sd.total_after_disc) - (sum(sd.total_after_disc) / sum(sd.qty) * sum(coalesce(sr.refund_qty,0))) - (sum((sd.qty - coalesce(sr.refund_qty,0)) * sd.supply_price)) as total_profit')
            )
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->get()
            ->toArray();


        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->select (
                DB::raw('DATE_FORMAT(s.sale_at,"%Y%m") as month'),
                DB::raw('sum(sd.total) as total'),
                DB::raw('sum(sd.qty*sd.supply_price) as cos'),
                DB::raw('count(DISTINCT(s.id)) as no_of_sale'),
                DB::raw('sum(sd.total_after_disc) as total_after_disc'),
            )
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->get()
            ->toArray();


        $sale_return = SaleRefund::from('sale_refunds as sr')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->join('sales as s', 's.id', '=', 'sr.sale_id')
            ->whereBetween('s.sale_at', [$this->from, $this->to])
            ->select(
                DB::raw('DATE_FORMAT(s.sale_at,"%Y%m") as month'),
                DB::raw('sum((sd.total_after_disc/sd.qty)*sr.refund_qty) as return_total'),
                DB::raw('sum(sd.supply_price*sr.refund_qty) as return_cos')
            )
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->get()
            ->toArray();



        $purchase = Purchase::from('purchases as pur')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'pur.id')
            ->whereBetween('pr.created_at', [$this->from, $this->to])
            ->select(
                'pr.created_at',
                DB::raw('DATE_FORMAT(pr.created_at,"%Y%m") as month'),
                DB::raw('sum(pr.total_cost) as total'),

            )
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->get()
            ->toArray();

        $pharmacy_account = ChartOfAccount::where('reference', 'pharmacy-inventory-5')->first();

        $stock_closing_as_at = Ledger::where('account_id', $pharmacy_account->id)
            ->where('posting_date', '<=', $this->to)
            ->select(
                'posting_date',
                DB::raw('sum(debit) - sum(credit) as stock_closing'),
            )
            ->first()
            ->toArray();
        $this->prev = $this->to->copy()->subMonth(1)->endOfMonth();
        $this->prev_2 = $this->to->copy()->subMonth(2)->endOfMonth();
        $stock_closing_prev = Ledger::where('account_id', $pharmacy_account->id)
            ->where('posting_date', '<=', $this->prev)
            ->select(
                'posting_date',
                DB::raw('sum(debit) - sum(credit) as stock_closing'),
            )
            ->first()
            ->toArray();
        $stock_closing_prev2 = Ledger::where('account_id', $pharmacy_account->id)
            ->where('posting_date', '<=', $this->prev_2)
            ->select(
                'posting_date',
                DB::raw('sum(debit) - sum(credit) as stock_closing'),
            )
            ->first()
            ->toArray();
        for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                $closing = $stock_closing_as_at['stock_closing'];
                $date=$this->to;
            } elseif ($i == 1) {
                $closing = $stock_closing_prev['stock_closing'];
                $date=$this->prev;
            } elseif ($i == 2) {
                $closing = $stock_closing_prev2['stock_closing'];
                $date=$this->prev_2;
            }
            $total_sale = isset($sale[$i]) ? $sale[$i]['total_after_disc'] : 0;
            $total_return =  isset($sale_return[$i]) ? $sale_return[$i]['return_total'] : 0;
            $sale_cos = isset($sale[$i]) ? $sale[$i]['cos'] : 0;
            $return_cos = isset($sale_return[$i]) ? $sale_return[$i]['return_cos'] : 0;
            $this->data[$i] = [
                'date' => date('F Y',strtotime($date)),
                'month' => isset($sale[$i]) ? $sale[$i]['month'] : 0,
                'no_of_sale' => isset($sale[$i]) ? $sale[$i]['no_of_sale'] : 0,
                'total_after_refund' => $total_sale - $total_return,
                'cos' => $sale_cos - $return_cos,
                'total_refund' => $total_return,
                'total_profit' => ($total_sale - $total_return) - ($sale_cos - $return_cos),
                'purchase' => isset($purchase[$i]) ? $purchase[$i]['total'] : 0,
                'closing_balance' => $closing,
            ];
        }


    }
}
