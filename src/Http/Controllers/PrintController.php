<?php


namespace Devzone\Pharmacy\Http\Controllers;


use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital\Patient;
use Devzone\Ams\Models\Ledger;

class PrintController extends Controller
{
    public $referred_by;

    public $on_credit = false;


    public $sale_at;
    public $sale_by;

    public $sales = [];
    public $refunds = [];
    public $first = [];

    public function print(Request $request, $id)
    {

        $sale_id = $id;
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
                    'u.name as sale_by', 's.sale_at', 's.is_credit', 'sr.refund_qty')
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
        $patient = false;
        if (!empty($first['patient_id'])) {
            $patient = Patient::from('patients as p')
                ->leftJoin('customers as c', 'c.id', '=', 'p.customer_id')
                ->where('p.id',$first['patient_id'])
                ->select('p.name', 'p.mr_no', 'p.gender', 'p.father_husband_name', 'p.account_id', 'c.credit_limit')->first();


        }


        $print = [];
        $print['feed'] = "                               ";
        $print['app_name'] = env('APP_NAME');
        $print['address_1'] = env('RECEIPT_PRINTER_ADDRESS_1');
        $print['address_2'] = env('RECEIPT_PRINTER_ADDRESS_2');
        $print['developer'] = env('RECEIPT_PRINTER_DEVELOPER');
        $print['developer_phone'] = env('RECEIPT_PRINTER_DEVELOPER_PHONE');
        $print['invoice_no'] = 'SALES INVOICE #' . $id;
        if ($this->on_credit) {
            $print['invoice_no'] = 'CREDIT SALES INVOICE #' . $id;
        }

        $patient_name = !empty($patient) ? $patient['name'] : 'Walk in';
        $mr_no = !empty($patient) ? $patient['mr_no'] : 'Walk in';
        $husband_father = !empty($patient) ? $patient['father_husband_name'] : 'Walk in';
        if (!empty($patient)) {
            if ($patient['gender'] == 'f') {
                $print['gender'] =  'Female' ;
            } elseif ($patient['gender'] == 'm') {
                $print['gender'] =   'Male' ;
            } else {
                $print['gender'] =   'Others' ;
            }
        } else {
            $print['gender'] =   '-' ;
        }
        if (!empty($request['reprint']) && $request['reprint'] == true) {
            $print['reprint'] = 'Reprinted';
        } else {
            $print['reprint'] = '';
        }
        $print['patient_name'] = str_pad("Patient Name : ".$mr_no.' - ' . $patient_name.' / '.$print['gender'], 64, " ");

        $print['father_husband_name'] = str_pad("Father/ Husband Name : " . $husband_father, 64, " ");

        $print['sale_by'] = str_pad("Sale By : " . $this->sale_by.' @ '.date('d M Y h:i A', strtotime($this->sale_at)), 64, " ");


        $print['heading'] = str_pad("#", 3, " ") . str_pad("Item", 25, " ") . str_pad("Qty", 8, " ", STR_PAD_LEFT) . str_pad("Unit", 12, " ", STR_PAD_LEFT) . str_pad("Total", 16, " ", STR_PAD_LEFT);
        $inner = "";
        foreach ($this->sales as $key => $s) {
            $sr = str_pad(++$key, 3, " ");
            $item = substr($s['product_name'], 0, 25);
            $item = str_pad($item, 25, " ");
            $qty = str_pad($s['qty'], 8, " ", STR_PAD_LEFT);
            $retail = str_pad($s['retail_price'], 12, " ", STR_PAD_LEFT);
            $total = str_pad($s['total'], 16, " ", STR_PAD_LEFT);
            $inner .= $sr . $item . $qty .  $retail .  $total;
        }

        foreach ($this->refunds as $key => $s) {
            $sr = str_pad(++$key + count($this->sales), 3, " ");

            $item = substr('-'.$s['product_name'], 0, 25);
            $item = str_pad($item, 25, " ");
            $qty = str_pad('-'.$s['qty'], 8, " ", STR_PAD_LEFT);
            $retail = str_pad('-'.$s['retail_price'], 12, " ", STR_PAD_LEFT);
            $total = str_pad('-'.$s['total'], 16, " ", STR_PAD_LEFT);
            $inner .= $sr . $item . $qty .  $retail .  $total;
        }
        $print['inner'] = $inner;


        $print['footer'] = "               ";
        $print['sub_total'] = str_pad("Sale Sub Total", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['sub_total'], 2), 19, " ", STR_PAD_LEFT);
        $print['discount'] = str_pad("Discount (PKR)", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['sub_total'] - $this->first['gross_total'], 2), 19, " ", STR_PAD_LEFT);
        $print['gross_total'] = str_pad("Sale after Discount", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['gross_total'], 2), 19, " ", STR_PAD_LEFT);
        $refunded = 0;
        if (!empty($this->first['refunded_id'])) {
            $total_refund = \Devzone\Pharmacy\Models\Sale\SaleRefund::from('sale_refunds as sr')
                ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
                ->where('sr.sale_id', $this->first['refunded_id'])
                ->where('sr.refunded_id', $sale_id)
                ->select(\Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as refund'))
                ->first();

            $refunded = $total_refund['refund'];
        }

        $add_bracket = abs(number_format($refunded, 2));
        $add_bracket = '(' . $add_bracket . ')';
        $print['refund'] = str_pad("Sale Returns", 45, " ", STR_PAD_LEFT) .
            str_pad($add_bracket, 19, " ", STR_PAD_LEFT);

        if ($this->first['gross_total'] - $refunded > 0) {
            $net_sales = number_format(abs($this->first['gross_total'] - $refunded), 2);
        } else {
            $net_sales =
                '(' . number_format(abs($first['gross_total'] - $refunded), 2) . ')';

        }
        $print['net_total'] = str_pad("Net Sales", 45, " ", STR_PAD_LEFT) .
            str_pad($net_sales, 19, " ", STR_PAD_LEFT);

        $cash_refund = "-";
        $cash_refund_text= "Cash";
        if ($refunded - $this->first['gross_total'] > 0) {
            $cash_refund_text= "(Refund)";
            $cash_refund = '(' . number_format(abs($refunded - $this->first['gross_total']), 2) . ')';
        } else {
            if ($this->first['is_credit'] == 'f') {

                $cash_refund=     number_format(abs($refunded - $this->first['gross_total']), 2) ;
            }

        }

        $print['receive_amount'] = str_pad($cash_refund_text, 45, " ", STR_PAD_LEFT) .
            str_pad(  $cash_refund,  19, " ", STR_PAD_LEFT);

        $credit = "-";
        if($this->first['is_credit']=='t' && $cash_refund_text != "(Refund)"){
            $credit =number_format(abs($refunded - $this->first['gross_total']),2);
        }
        $print['note']="NOTE: Medicines will not be returned or changed after ".env("RETURN_POLICY_DAYS","3")." days.";
        $print['note1']="There will be no refund or change without the original bill.";
        $print['note2']="Refrigerator items will never be returned.";
        $print['change_returned'] = str_pad("Credit", 45, " ", STR_PAD_LEFT) .
            str_pad($credit, 19, " ", STR_PAD_LEFT);
        return view('pharmacy::print', compact(  'id', 'print'));
    }
}
