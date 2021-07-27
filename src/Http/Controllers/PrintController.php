<?php


namespace Devzone\Pharmacy\Http\Controllers;


use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
    public function print($id)
    {
        $sales = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as pt', 'pt.id', '=', 's.patient_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->where('s.id', $id)
            ->select('p.name as item', DB::raw('sum(sd.qty) as sale_qty'), 'sd.retail_price', 'sd.disc'
                , 's.sale_at', 's.remarks', 'pt.name as patient_name', 's.is_refund', 'u.name as sale_by',
                DB::raw('sum(sr.refund_qty) as refund_qty'), 'e.name as referred_by',
                'pt.name as patient_name','pt.mr_no','pt.father_husband_name','pt.gender','pt.phone'
            )
            ->groupBy('sd.product_id')
            ->orderBy('sd.product_id')->get()->toArray();
        $sales_ref = [];
        foreach ($sales as $key => $s) {
            $array = [];


            $sales[$key]['total'] = $s['sale_qty'] * $s['retail_price'];
            $sales[$key]['total_after_disc'] = $sales[$key]['total'];
            if ($s['disc'] > 0) {
                $discount = round(($s['disc'] / 100) * $sales[$key]['total'], 2);
                $sales[$key]['total_after_disc'] = $sales[$key]['total'] - $discount;
            }

            $sales_ref[] = $sales[$key];
            if ($s['refund_qty'] > 0) {
                $array['item'] = ' -' . $sales[$key]['item'];
                $array['sale_qty'] = -$sales[$key]['sale_qty'];
                $array['retail_price'] = -$sales[$key]['retail_price'];
                $array['total'] = -$sales[$key]['total'];
                $array['disc'] = -$sales[$key]['disc'];
                $array['total_after_disc'] = -$sales[$key]['total_after_disc'];
                $sales_ref[] = $array;
            }
        }

        $sale = collect($sales_ref)->first();
        $print = [];
        $print['feed'] = "               ";
        $print['address_1'] = env('RECEIPT_PRINTER_ADDRESS_1');
        $print['address_2'] = env('RECEIPT_PRINTER_ADDRESS_2');
        $print['developer'] = env('RECEIPT_PRINTER_DEVELOPER');
        $print['developer_phone'] = env('RECEIPT_PRINTER_DEVELOPER_PHONE');
        $print['invoice_no'] = 'SALES INVOICE #' . $id;
        $patient_name=!empty($sale['patient_name']) ? $sale['patient_name']: 'Walk in';
        $mr_no=!empty($sale['mr_no']) ? $sale['mr_no'] : 'Walk in';
        $husband_father=!empty($sale['father_husband_name']) ? $sale['father_husband_name'] : 'Walk in';
        if (!empty($sale['gender'])){
            if ($sale['gender']=='f'){
                $print['gender'] = str_pad("Gender : " . 'Female', 48, " ");
            }elseif ($sale['gender']=='m'){
                $print['gender'] = str_pad("Gender : " . 'Male', 48, " ");
            }
            else{
                $print['gender'] =str_pad("Gender : " . 'Others', 48, " ");
            }
        }
        else{
            $print['gender']=str_pad("Gender : " . 'Walk in', 48, " ");
        }
        $print['patient_name'] = str_pad("Patient Name : " . $patient_name, 48, " ");
        $print['patient_mr_no'] = str_pad("Mr# : " .$mr_no , 48, " ");
        $print['father_husband_name'] = str_pad("Father/ Husband Name : " . $husband_father, 48, " ");

        $print['sale_by'] = str_pad("Sale By : " . $sale['sale_by'], 48, " ");
        $print['sale_at'] = str_pad("Sale At : " . date('d M Y h:i A', strtotime($sale['sale_at'])), 48, " ",);

        $print['heading'] = str_pad("Item", 20, " ") . str_pad("Qty", 6, " ", STR_PAD_LEFT) . str_pad("Unit", 10, " ", STR_PAD_LEFT) . str_pad("Total", 12, " ", STR_PAD_LEFT);
        $inner = "";
        foreach ($sales_ref as $key => $s) {
            $item = substr($s['item'], 0, 20);
            $item = str_pad($item, 20, " ");
            $qty = str_pad($s['sale_qty'], 6, " ",STR_PAD_LEFT);
            $retail = str_pad($s['retail_price'], 9, " ",STR_PAD_LEFT);
            $total = str_pad($s['total'], 11, " ", STR_PAD_LEFT);
            $inner .= $item . $qty . " " . $retail . " " . $total;
        }

        $collect = collect($sales_ref);
        $print['inner'] = $inner;

        $print['sub_total'] = str_pad("Sub Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total'), 2), 15, " ", STR_PAD_LEFT);
        $print['discount'] = str_pad("Discount (PKR)", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total') - $collect->where('sale_qty', '>', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);
        $print['gross_total'] = str_pad("Gross Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);
        $print['refund'] = str_pad("Refunded", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '<', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);
        $print['net_total'] = str_pad("Net Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total_after_disc') + $collect->where('sale_qty', '<', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);
        return view('pharmacy::print', compact('sales_ref', 'sale', 'id', 'print'));
    }
}
