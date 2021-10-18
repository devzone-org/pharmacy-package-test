<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;

use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Hospital\Patient;
use Devzone\Ams\Models\Ledger;

class Transaction extends Component
{
    public $sale_id;
    public $referred_by;
    public $patient_name;
    public $on_credit = false;
    public $credit_limit;
    public $closing_balance;
    public $sale_at;
    public $sale_by;

    public $sales = [];
    public $refunds = [];
    public $first = [];

    public function mount($sale_id)
    {
        $this->sale_id = $sale_id;
        $sl = Sale::find($sale_id);
        $sale = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->where('s.id', $sale_id)
            ->select('sd.*', 'p.name as product_name', 's.patient_id', 'e.name as referred_by',
                'u.name as sale_by', 's.sale_at', 's.is_credit')
            ->get();
        $first = $sale->first();
        if (!empty($sl->refunded_id)) {
            $refund = Sale::from('sales as s')
                ->join('sale_refunds as sr', 'sr.refunded_id', '=', 's.id')
                ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
                ->join('products as p', 'p.id', '=', 'sr.product_id')
                ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
                ->join('users as u', 'u.id', '=', 's.sale_by')
                ->where('sr.sale_id', $sl->refunded_id)
                ->where('sr.refunded_id', $sale_id)
                ->select('sd.*', 'p.name as product_name', 's.patient_id', 'e.name as referred_by',
                    'u.name as sale_by', 's.sale_at', 's.is_credit','sr.refund_qty')
                ->get();
            $first = $refund->first();
            $this->refunds = $refund->toArray();
        }

        $this->first = $sl->toArray();
        $this->sales = $sale->toArray();
        $this->referred_by = $first['referred_by'];
        $this->on_credit = ($first['is_credit'] == 't') ? true : false;
        $this->sale_at = $first['sale_at'];
        $this->sale_by = $first['sale_by'];

        if (!empty($first['patient_id'])) {
            $patient = Patient::from('patients as p')
                ->where('p.id',$first['patient_id'])
                ->leftJoin('customers as c', 'c.id', '=', 'p.customer_id')
                ->select('p.name', 'p.mr_no', 'p.account_id', 'c.credit_limit')->first();

            $this->patient_name = $patient['mr_no'] . ' - ' . $patient['name'];
            $this->credit_limit = $patient['credit_limit'] ?? 0;

            if (!empty($patient['account_id'])) {
                $closing = Ledger::where('account_id', $patient['account_id'])
                    ->select(DB::raw('sum(debit-credit) as closing'))->first();
                $this->closing_balance = $closing['closing'];
            }
        }


    }

    public function render()
    {
        return view('pharmacy::livewire.sales.transaction');
    }


}
