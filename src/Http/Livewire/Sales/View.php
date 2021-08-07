<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;

use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleIssuance;
use Illuminate\Support\Facades\DB;
use Livewire\Component;


class View extends Component
{


    public $sale_id;
    public $sale_by;
    public $sale_at;
    public $discount = 0;
    public $received = 0;
    public $payable = 0;
    public $referred_by_id;
    public $referred_by_name;
    public $patient_id;
    public $patient_name;
    public $remarks;
    public $success;
    public $error;
    public $sales = [];
    public $sales_ref = [];

    public $admission = false;
    public $admission_details = [];
    public $handed_over;


    public function mount($sale_id, $admission_id = null, $procedure_id = null)
    {
        $this->sale_id = $sale_id;

        $this->sales = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as pt', 'pt.id', '=', 's.patient_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->where('s.id', $sale_id)
            ->select('p.name as item', DB::raw('sum(sd.qty) as sale_qty'), 'sd.retail_price', 'sd.disc'
                , 's.sale_at', 's.remarks', 'pt.name as patient_name', 's.is_refund', 'u.name as sale_by',
                DB::raw('sum(sr.refund_qty) as refund_qty'), 'e.name as referred_by')
            ->groupBy('sd.product_id')
            ->groupBy('sd.retail_price')
            ->orderBy('sd.product_id')->get()->toArray();

        foreach ($this->sales as $key => $s) {
            $array = [];
            if ($key == 0) {
                $this->referred_by_name = $s['referred_by'];
                $this->sale_at = date('d M, Y h:i A', strtotime($s['sale_at']));
                $this->sale_by = $s['sale_by'];
                $this->patient_name = $s['patient_name'];
                $this->remarks = $s['remarks'];
            }

            $this->sales[$key]['total'] = $s['sale_qty'] * $s['retail_price'];
            $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'];
            if ($s['disc'] > 0) {
                $discount = round(($s['disc'] / 100) * $this->sales[$key]['total'], 2);
                $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'] - $discount;
            }

            $this->sales_ref[] = $this->sales[$key];
            if ($s['refund_qty'] > 0) {
                $array['item'] = 'Refunded - ' . $this->sales[$key]['item'];
                $array['sale_qty'] = -$this->sales[$key]['sale_qty'];
                $array['retail_price'] = -$this->sales[$key]['retail_price'];
                $array['total'] = -$this->sales[$key]['total'];
                $array['disc'] = -$this->sales[$key]['disc'];
                $array['total_after_disc'] = -$this->sales[$key]['total_after_disc'];
                $this->sales_ref[] = $array;
            }
        }

        if (!empty($admission_id) && !empty($procedure_id)) {

            $this->admission_details = \App\Models\Hospital\Admission::from('admissions as a')
                ->join('patients as p', 'p.id', '=', 'a.patient_id')
                ->leftJoin('employees as e', 'e.id', '=', 'a.doctor_id')
                ->where('a.id', $admission_id)
                ->select('p.mr_no', 'p.name', 'a.admission_no', 'e.name as doctor')->first()
                ->toArray();

            $procedure_name = \App\Models\Hospital\Procedure::where('id', $procedure_id)->first('name');
            $sale_handed_over = SaleIssuance::where('sale_id', $this->sale_id)->get();
            $this->handed_over = $sale_handed_over->toArray();
            $this->admission_details['procedure_name'] = $procedure_name->name;
            $this->admission = true;
        }

    }


    public function render()
    {
        return view('pharmacy::livewire.sales.view');
    }

    public function printSale()
    {

//        $ip=$_SERVER['REMOTE_ADDR'];
//        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//
//        dd($ip,$hostname);
        //Receipt::generate($this->sales_ref, $this->sale_id);
    }
}
