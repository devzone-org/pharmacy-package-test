<?php


namespace Devzone\Pharmacy\Http\Controllers;


use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
    public function print(Request $request, $id)
    {
//        if (SaleDetail::where('sale_id', $id)->exists()) {
//            $refund_with_sale = true;
//        } else {
//            $refund_with_sale = false;
//        }
//        $sales = Sale::from('sales as s')
//            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
//            ->leftJoin('sale_refunds as sr', 'sr.sale_detail_id', '=', 'sd.id')
//            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
//            ->leftJoin('patients as pt', 'pt.id', '=', 's.patient_id')
//            ->join('products as p', 'p.id', '=', 'sd.product_id')
//            ->join('users as u', 'u.id', '=', 's.sale_by')
//            ->where('s.id', $id)
//            ->select('p.name as item', DB::raw('sum(sd.qty) as sale_qty'), 'sd.retail_price', 'sd.disc'
//                , 's.sale_at', 's.remarks', 'pt.name as patient_name', 's.is_refund', 'u.name as sale_by',
//                DB::raw('sum(sr.refund_qty) as refund_qty'), 'e.name as referred_by',
//                'pt.name as patient_name', 'pt.mr_no', 'pt.father_husband_name', 'pt.gender', 'pt.phone'
//            )
//            ->groupBy('sd.product_id')
//            ->orderBy('sd.product_id')->get()->toArray();
        $new_sale_in_refund = Sale::from('sales as s')
            ->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as pt', 'pt.id', '=', 's.patient_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->where('s.id', $id)
            ->select('p.name as item',DB::raw('sum(sd.qty) as sale_qty'),'sd.retail_price', 'sd.disc',
                's.sale_at', 's.remarks', 'pt.name as patient_name', 's.is_refund', 'u.name as sale_by', 'e.name as referred_by',
                'pt.name as patient_name', 'pt.mr_no', 'pt.father_husband_name', 'pt.gender', 'pt.phone','s.receive_amount','s.payable_amount'
            )
            ->groupBy('sd.product_id')
//            ->groupBy('sd.retail_price')
            ->orderBy('sd.product_id')->get()->toArray();
        $refund_only = Sale::from('sales as s')
            ->join('sale_refund_details as sr', 'sr.refunded_id', '=', 's.id')
            ->join('sale_details as sd', 'sd.id', '=', 'sr.sale_detail_id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as pt', 'pt.id', '=', 's.patient_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->where('sr.refunded_id', $id)
            ->select('p.name as item', DB::raw('sum(sd.qty) as sale_qty'), 'sd.retail_price', 'sd.disc',
                's.sale_at', 's.remarks', 'pt.name as patient_name', 's.is_refund', 'u.name as sale_by', 'e.name as referred_by',
                DB::raw('sum(sr.refund_qty) as refund_qty'), 'pt.name as patient_name', 'pt.mr_no', 'pt.father_husband_name', 'pt.gender', 'pt.phone','s.receive_amount','s.payable_amount'
            )
            ->groupBy('sd.product_id')
//            ->groupBy('sd.retail_price')
            ->orderBy('sd.product_id')
            ->get()->toArray();
        $sales = array_merge($new_sale_in_refund, $refund_only);
        $sales_ref = [];
        foreach ($sales as $key => $s) {
            $array = [];
            $sales[$key]['total'] = $s['sale_qty'] * $s['retail_price'];
            $sales[$key]['total_after_disc'] = $sales[$key]['total'];
            if ($s['disc'] > 0) {
                $discount = round(($s['disc'] / 100) * $sales[$key]['total'], 2);
                $sales[$key]['total_after_disc'] = $sales[$key]['total'] - $discount;
            }
            $sales[$key]['refunded'] = false;
//            $sales_ref[] = $sales[$key];
            if (isset($s['refund_qty'])) {
                if ($s['refund_qty'] > 0) {
//                    if ($refund_with_sale == true) {
                        $sales[$key]['item'] = 'Returned - ' . $s['item'];
                        $sales[$key]['sale_qty'] = -$s['refund_qty'];
                        $sales[$key]['retail_price'] = -$s['retail_price'];
                        $sales[$key]['total'] = -round($s['retail_price'] * $s['refund_qty'], 2);
                        if ($s['disc'] > 0) {
                            $discount = round(($s['disc'] / 100) * abs($sales[$key]['total']), 2);
                            $sales[$key]['total_after_disc'] = -(abs($sales[$key]['total']) - $discount);
                        } else {
                            $sales[$key]['total_after_disc'] = -abs($sales[$key]['total']);
                        }
                        $sales[$key]['disc'] = $s['disc'];
//                    }
                    $sales[$key]['refunded'] = true;
                }else {
                    $sales[$key]['sale_qty'] = $s['refund_qty'];
                    $sales[$key]['retail_price'] = $s['retail_price'];
                    $sales[$key]['total'] = round($s['retail_price'] * $s['refund_qty'], 2);
                    if ($s['disc'] > 0) {
                        $discount = round(($s['disc'] / 100) * abs($sales[$key]['total']), 2);
                        $sales[$key]['total_after_disc'] = (abs($sales[$key]['total']) - $discount);
                    } else {
                        $sales[$key]['total_after_disc'] = abs($sales[$key]['total']);
                    }
                    $sales[$key]['disc'] = $s['disc'];
                }
            }
            $sales_ref[] = $sales[$key];
        }

        $sale = collect($sales_ref)->first();
        $print = [];
        $print['feed'] = "               ";
        $print['app_name'] = env('APP_NAME');
        $print['address_1'] = env('RECEIPT_PRINTER_ADDRESS_1');
        $print['address_2'] = env('RECEIPT_PRINTER_ADDRESS_2');
        $print['developer'] = env('RECEIPT_PRINTER_DEVELOPER');
        $print['developer_phone'] = env('RECEIPT_PRINTER_DEVELOPER_PHONE');
        $print['invoice_no'] = 'SALES INVOICE #' . $id;
        $patient_name = !empty($sale['patient_name']) ? $sale['patient_name'] : 'Walk in';
        $mr_no = !empty($sale['mr_no']) ? $sale['mr_no'] : 'Walk in';
        $husband_father = !empty($sale['father_husband_name']) ? $sale['father_husband_name'] : 'Walk in';
        if (!empty($sale['gender'])) {
            if ($sale['gender'] == 'f') {
                $print['gender'] = str_pad("Gender : " . 'Female', 48, " ");
            } elseif ($sale['gender'] == 'm') {
                $print['gender'] = str_pad("Gender : " . 'Male', 48, " ");
            } else {
                $print['gender'] = str_pad("Gender : " . 'Others', 48, " ");
            }
        } else {
            $print['gender'] = str_pad("Gender : " . 'Walk in', 48, " ");
        }
        if (!empty($request['reprint']) && $request['reprint'] == true) {
            $print['reprint'] = 'Reprinted';
        } else {
            $print['reprint'] = '';
        }
        $print['patient_name'] = str_pad("Patient Name : " . $patient_name, 48, " ");
        $print['patient_mr_no'] = str_pad("Mr# : " . $mr_no, 48, " ");
        $print['father_husband_name'] = str_pad("Father/ Husband Name : " . $husband_father, 48, " ");

        $print['sale_by'] = str_pad("Sale By : " . $sale['sale_by'], 48, " ");
        $print['sale_at'] = str_pad("Sale At : " . date('d M Y h:i A', strtotime($sale['sale_at'])), 48, " ",);

        $print['heading'] = str_pad("#", 3, " ") . str_pad("Item", 17, " ") . str_pad("Qty", 6, " ", STR_PAD_LEFT) . str_pad("Unit", 10, " ", STR_PAD_LEFT) . str_pad("Total", 12, " ", STR_PAD_LEFT);
        $inner = "";
        foreach ($sales_ref as $key => $s) {
            $sr = str_pad(++$key, 3, " ");
            $item = substr($s['item'], 0, 17);
            $item = str_pad($item, 17, " ");
            $qty = str_pad($s['sale_qty'], 6, " ", STR_PAD_LEFT);
            $retail = str_pad($s['retail_price'], 9, " ", STR_PAD_LEFT);
            $total = str_pad($s['total'], 11, " ", STR_PAD_LEFT);
            $inner .= $sr . $item . $qty . " " . $retail . " " . $total;
        }

        $collect = collect($sales_ref);
        $print['inner'] = $inner;
        $print['footer'] = str_pad("total Qty", 20, " ", STR_PAD_LEFT) . str_pad($collect->where('sale_qty','>','0')->sum('sale_qty'), 5, " ", STR_PAD_LEFT) . str_pad("", 20, " ", STR_PAD_LEFT);
        $print['sub_total'] = str_pad("Sale", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total'), 2), 15, " ", STR_PAD_LEFT);
        $print['discount'] = str_pad("Discount (PKR)", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total') - $collect->where('sale_qty', '>', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);
        $print['gross_total'] = str_pad("Net Sale", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);
        $add_bracket=abs(number_format($collect->where('sale_qty', '<', 0)->sum('total_after_disc'),2));
        $add_bracket='('.$add_bracket.')';
        $print['refund'] = str_pad("Returned", 33, " ", STR_PAD_LEFT) .
            str_pad($add_bracket, 15, " ", STR_PAD_LEFT);
        $print['net_total'] = str_pad("Net Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total_after_disc') + $collect->where('sale_qty', '<', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT);

        if(abs($collect->where('sale_qty','<','0')->sum('total_after_disc')) > $collect->where('sale_qty','>','0')->sum('total_after_disc')){
            $label='Paid';
        }else{
            $label='Received';
        }
        $print['receive_amount'] = str_pad($label, 33, " ", STR_PAD_LEFT) .
            str_pad($sale['receive_amount'], 15, " ", STR_PAD_LEFT);
        $print['change_returned'] = str_pad("Change Returned", 33, " ", STR_PAD_LEFT) .
            str_pad($sale['payable_amount'], 15, " ", STR_PAD_LEFT);
        return view('pharmacy::print', compact('sales_ref', 'sale', 'id', 'print'));
    }
}
