<?php


namespace Devzone\Pharmacy\Http\Controllers;


use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital\Patient;
use Devzone\Ams\Models\Ledger;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

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

        if (!empty(env('PRINT_LOCAL'))) {
//            $this->localPrint($request, $id);
            $print = $this->localPrint($request, $id);
//            return view('pharmacy::print-close');
            return view('pharmacy::print-sale', compact('print'));

        } else {
            $print = $this->onlinePrint($request, $id);
            return view('pharmacy::print', compact('id', 'print'));
        }

    }

    private function localPrint($request, $id)
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
                ->join('sale_refund_details as sr', 'sr.refunded_id', '=', 's.id')
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
                ->where('p.id', $first['patient_id'])
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
        $print['license_no'] = 'LICENSE #' . env('RECEIPT_LICENSE_NO', '');

        if ($this->on_credit) {
            $print['invoice_no'] = 'CREDIT SALES INVOICE #' . $id;
        }

        $patient_name = !empty($patient) ? $patient['name'] : 'Walk in';
        $mr_no = !empty($patient) ? $patient['mr_no'] : 'Walk in';
        $husband_father = !empty($patient) ? $patient['father_husband_name'] : 'Walk in';
        if (!empty($patient)) {
            if ($patient['gender'] == 'f') {
                $print['gender'] = 'Female';
            } elseif ($patient['gender'] == 'm') {
                $print['gender'] = 'Male';
            } else {
                $print['gender'] = 'Others';
            }
        } else {
            $print['gender'] = '-';
        }
        if (!empty($request['reprint']) && $request['reprint'] == true) {
            $print['reprint'] = 'Reprinted';
        } else {
            $print['reprint'] = '';
        }
        $print['patient_name'] = str_pad("Patient Name : " . $mr_no . ' - ' . $patient_name . ' / ' . $print['gender'], 64, " ");

        $print['father_husband_name'] = str_pad("Father/ Husband Name : " . $husband_father, 64, " ");

        $print['sale_by'] = str_pad("Sale By : " . $this->sale_by . ' @ ' . date('d M Y h:i A', strtotime($this->sale_at)), 64, " ");


//        $print['heading'] = str_pad("#", 3, " ") . str_pad("Item", 25, " ") . str_pad("Qty", 8, " ", STR_PAD_LEFT) . str_pad("Unit", 12, " ", STR_PAD_LEFT) . str_pad("Total", 16, " ", STR_PAD_LEFT);
        $print['heading'] = "<p>" . "<span style='display:inline-block; width: 5%;'>" . '#' . "</span>" . "<span style='display:inline-block; width: 40%; text-align: left'>" . 'Item' . "</span>" . "<span style='display:inline-block; width: 15%; text-align: center '>" . 'Qty' . "</span>" . " <span style='display:inline-block;width: 15%; text-align: center'>" . 'Unit' . "</span>" . " <span style='display:inline-block;width: 20%; text-align: right'>" . 'Total' . "</span></p>";

        $inner = "";
        foreach ($this->sales as $key => $s) {
            $product = preg_replace("/[^A-Za-z0-9\s]/", "", $s['product_name']);
            $sr = str_pad(++$key, 3, " ");
            $item = substr($product, 0, 25);
            $item = str_pad($item, 25, " ");
            $qty = str_pad($s['qty'], 8, " ", STR_PAD_LEFT);
            $retail = str_pad($s['retail_price'], 12, " ", STR_PAD_LEFT);
            $total = str_pad($s['total'], 16, " ", STR_PAD_LEFT);
//            $inner .= $sr . $item . $qty . $retail . $total;

            $inner .= "<p>" . "</span>" . "<span style='display:inline-block; width: 5%; text-align: center'>" . $sr . "</span>" . "<span style='display:inline-block; width: 40%; white-space: nowrap;  text-overflow: ellipsis !important; overflow: hidden;'>" . $item . "</span>" . "<span style='display:inline-block; width: 15%; text-align: center'>" . $qty . "</span>" . "<span style='display:inline-block; width: 18%; text-align: center'>" . $retail . "</span>" . " <span style='display:inline-block;width: 18%; text-align: right'>" . $total . "</span></p>";

        }

        foreach ($this->refunds as $key => $s) {
            $sr = str_pad(++$key + count($this->sales), 3, " ");
            $product = preg_replace("/[^A-Za-z0-9\s]/", "", $s['product_name']);
            $item = substr('-' . $product, 0, 25);
            $item = str_pad($item, 25, " ");
            $qty = str_pad('-' . $s['refund_qty'], 8, " ", STR_PAD_LEFT);
            $retail = str_pad('-' . $s['retail_price'], 12, " ", STR_PAD_LEFT);
            $total = str_pad('-' . $s['refund_qty'] * $s['retail_price'], 16, " ", STR_PAD_LEFT);
            $inner .= $sr . $item . $qty . $retail . $total;

            $inner .= "<p>" . "</span>" . "<span style='display:inline-block; width: 5%; text-align: center'>" . $sr . "</span>" . "<span style='display:inline-block; width: 40%; white-space: nowrap;  text-overflow: ellipsis !important; overflow: hidden;'>" . $item . "</span>" . "<span style='display:inline-block; width: 15%; text-align: center'>" . $qty . "</span>" . "<span style='display:inline-block; width: 18%; text-align: center'>" . $retail . "</span>" . " <span style='display:inline-block;width: 18%; text-align: right'>" . $total . "</span></p>";

        }
        //Round off
        $val = 0;
        if (!empty($this->first['rounded_inc'])) {
            $val = $this->first['rounded_inc'];
        } elseif (!empty($this->first['rounded_dec'])) {
            $val = -1 * $this->first['rounded_dec'];
        }
        //

        $print['inner'] = $inner;


        $print['footer'] = "               ";
        $print['sub_total'] = str_pad("Sale Sub Total", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['sub_total'], 2), 19, " ", STR_PAD_LEFT);
        $print['discount'] = str_pad("Discount (PKR)", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['sub_total'] - $this->first['gross_total'], 2), 19, " ", STR_PAD_LEFT);
        $print['gross_total'] = str_pad("Sale after Discount", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['gross_total'] + $val, 2), 19, " ", STR_PAD_LEFT);
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


        $add_bracket = number_format(abs($refunded), 2);
        $add_bracket = '(' . $add_bracket . ')';
        $print['refund'] = str_pad("Sale Returns", 45, " ", STR_PAD_LEFT) .
            str_pad($add_bracket, 19, " ", STR_PAD_LEFT);

        //After Round-off
        $after_roundoff = 0;
        $after_roundoff = $refunded - ($this->first['gross_total'] + $val);
        if ($this->first['is_credit'] != 'f') {
            $after_roundoff = $refunded - $this->first['gross_total'];
        }
        //

        if ($this->first['gross_total'] - $refunded > 0) {
            $net_sales = number_format(abs($after_roundoff), 2);
        } else {
            $net_sales = '(' . number_format(abs($after_roundoff), 2) . ')';

        }
        $print['net_total'] = str_pad("Net Sales", 45, " ", STR_PAD_LEFT) .
            str_pad($net_sales, 19, " ", STR_PAD_LEFT);

        $cash_refund = "-";
        $cash_refund_text = "Cash";


        $print['sub_total'] = "<p>" . "<span style='display:inline-block; width: 75%;!important; text-align: right'>" . 'Sale Sub Total' . "</span>" . "<span style='display:inline-block; width: 25%; text-align: right'>" . number_format($this->first['sub_total'], 2) . "</span>";
        $print['discount'] = "<p>" . "<span style='display:inline-block; width: 75%;!important; text-align: right'>" . 'Discount (PKR)' . "</span>" . "<span style='display:inline-block; width: 25%; text-align: right'>" . number_format($this->first['sub_total'] - $this->first['gross_total'], 2) . "</span>";
        $print['gross_total'] = "<p>" . "<span style='display:inline-block; width: 75%;!important; text-align: right'>" . 'Sale after Discount' . "</span>" . "<span style='display:inline-block; width: 25%; text-align: right'>" . number_format($this->first['gross_total'] + $val, 2) . "</span>";
        $print['refund'] = "<p>" . "<span style='display:inline-block; width: 75%;!important; text-align: right'>" . 'Sale Returns' . "</span>" . "<span style='display:inline-block; width: 25%; text-align: right'>" . $add_bracket . "</span>";
        $print['net_total'] = "<p>" . "<span style='display:inline-block; width: 75%;!important; text-align: right'>" . 'Net Sales' . "</span>" . "<span style='display:inline-block; width: 25%; text-align: right'>" . number_format($net_sales, 2) . "</span>";

        if ($after_roundoff > 0) {
            $cash_refund_text = "(Refund)";
            $cash_refund = '(' . number_format(abs($after_roundoff), 2) . ')';
        } else {
            if ($this->first['is_credit'] == 'f') {

                $cash_refund = number_format(abs($after_roundoff), 2);
            }

        }

        $print['receive_amount'] = str_pad($cash_refund_text, 45, " ", STR_PAD_LEFT) .
            str_pad($cash_refund, 19, " ", STR_PAD_LEFT);

        $credit = "-";
        if ($this->first['is_credit'] == 't' && $cash_refund_text != "(Refund)") {
            $credit = number_format(abs($refunded - $this->first['gross_total']), 2);
        }
        $print['note'] = "NOTE: Medicines will not be returned or changed after " . env("RETURN_POLICY_DAYS", "3") . " days.";
        $print['note1'] = "There will be no refund or change without the original bill.";
        $print['note2'] = "Refrigerator items will never be returned.";
        $print['change_returned'] = str_pad("Credit", 45, " ", STR_PAD_LEFT) .
            str_pad($credit, 19, " ", STR_PAD_LEFT);

        return $print;
        $connector = new WindowsPrintConnector(config('app.printer_model'));
//        $connector = new WindowsPrintConnector('POS-80C1');
        $printer = new Printer($connector);


        $printer->feed();
//        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_A);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->text($print['app_name']);
        $printer->setFont(Printer::FONT_B);
        $printer->feed(2);
        $printer->setTextSize(1, 1);
        $printer->text($print['address_1']);
        $printer->feed();
        $printer->text($print['address_2']);
        $printer->feed(2);
        if (!empty(env('RECEIPT_LICENSE_NO'))) {
            $printer->text($print['license_no']);
            $printer->feed(2);
        }
        $printer->text($print['invoice_no']);
        $printer->feed();
        $printer->text($print['reprint']);
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text($print['patient_name']);
        $printer->text($print['father_husband_name']);
        $printer->text($print['sale_by']);


        $printer->text("================================================================");
        $printer->text($print['heading']);
        $printer->text("================================================================");
        $printer->text($print['inner']);
        $printer->text("----------------------------------------------------------------");

        $printer->text($print['sub_total']);
        $printer->text($print['discount']);

        $printer->text($print['gross_total']);
        $printer->text($print['refund']);
        $printer->text($print['net_total']);
        $printer->feed(2);

        $printer->text($print['receive_amount']);
        $printer->text($print['change_returned']);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("-----------------------");
        $printer->feed();

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text($print['note']);
        $printer->text($print['note2']);
        $printer->feed(2);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($print['developer'] . " " . $print['developer_phone']);

        $printer->feed(2);

        $printer->cut();
        $printer->pulse();

        $printer->close();

    }

    private function onlinePrint($request, $id)
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
                ->join('sale_refund_details as sr', 'sr.refunded_id', '=', 's.id')
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
                ->where('p.id', $first['patient_id'])
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
        $print['license_no'] = 'LICENSE #' . env('RECEIPT_LICENSE_NO', '');

        if ($this->on_credit) {
            $print['invoice_no'] = 'CREDIT SALES INVOICE #' . $id;
        }

        $patient_name = !empty($patient) ? $patient['name'] : 'Walk in';
        $mr_no = !empty($patient) ? $patient['mr_no'] : 'Walk in';
        $husband_father = !empty($patient) ? $patient['father_husband_name'] : 'Walk in';
        if (!empty($patient)) {
            if ($patient['gender'] == 'f') {
                $print['gender'] = 'Female';
            } elseif ($patient['gender'] == 'm') {
                $print['gender'] = 'Male';
            } else {
                $print['gender'] = 'Others';
            }
        } else {
            $print['gender'] = '-';
        }
        if (!empty($request['reprint']) && $request['reprint'] == true) {
            $print['reprint'] = 'Reprinted';
        } else {
            $print['reprint'] = '';
        }
        $print['patient_name'] = str_pad("Patient Name : " . $mr_no . ' - ' . $patient_name . ' / ' . $print['gender'], 64, " ");

        $print['father_husband_name'] = str_pad("Father/ Husband Name : " . $husband_father, 64, " ");

        $print['sale_by'] = str_pad("Sale By : " . $this->sale_by . ' @ ' . date('d M Y h:i A', strtotime($this->sale_at)), 64, " ");


        $print['heading'] = str_pad("#", 3, " ") . str_pad("Item", 25, " ") . str_pad("Qty", 8, " ", STR_PAD_LEFT) . str_pad("Unit", 12, " ", STR_PAD_LEFT) . str_pad("Total", 16, " ", STR_PAD_LEFT);
        $inner = "";
        foreach ($this->sales as $key => $s) {
            $product = preg_replace("/[^A-Za-z0-9\s]/", "", $s['product_name']);
            $sr = str_pad(++$key, 3, " ");
            $item = substr($product, 0, 25);
            $item = str_pad($item, 25, " ");
            $qty = str_pad($s['qty'], 8, " ", STR_PAD_LEFT);
            $retail = str_pad($s['retail_price'], 12, " ", STR_PAD_LEFT);
            $total = str_pad($s['total'], 16, " ", STR_PAD_LEFT);
            $inner .= $sr . $item . $qty . $retail . $total;
        }

        foreach ($this->refunds as $key => $s) {
            $sr = str_pad(++$key + count($this->sales), 3, " ");
            $product = preg_replace("/[^A-Za-z0-9\s]/", "", $s['product_name']);
            $item = substr('-' . $product, 0, 25);
            $item = str_pad($item, 25, " ");
            $qty = str_pad('-' . $s['refund_qty'], 8, " ", STR_PAD_LEFT);
            $retail = str_pad('-' . $s['retail_price'], 12, " ", STR_PAD_LEFT);
            $total = str_pad('-' . $s['refund_qty'] * $s['retail_price'], 16, " ", STR_PAD_LEFT);
            $inner .= $sr . $item . $qty . $retail . $total;
        }
        //Round off
        $val = 0;
        if (!empty($this->first['rounded_inc'])) {
            $val = $this->first['rounded_inc'];
        } elseif (!empty($this->first['rounded_dec'])) {
            $val = -1 * $this->first['rounded_dec'];
        }
        //

        $print['inner'] = $inner;


        $print['footer'] = "               ";
        $print['sub_total'] = str_pad("Sale Sub Total", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['sub_total'], 2), 19, " ", STR_PAD_LEFT);
        $print['discount'] = str_pad("Discount (PKR)", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['sub_total'] - $this->first['gross_total'], 2), 19, " ", STR_PAD_LEFT);
        $print['gross_total'] = str_pad("Sale after Discount", 45, " ", STR_PAD_LEFT) .
            str_pad(number_format($this->first['gross_total'] + $val, 2), 19, " ", STR_PAD_LEFT);
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


        $add_bracket = number_format(abs($refunded), 2);
        $add_bracket = '(' . $add_bracket . ')';
        $print['refund'] = str_pad("Sale Returns", 45, " ", STR_PAD_LEFT) .
            str_pad($add_bracket, 19, " ", STR_PAD_LEFT);

        //After Round-off
        $after_roundoff = 0;
        $after_roundoff = $refunded - ($this->first['gross_total'] + $val);
        if ($this->first['is_credit'] != 'f') {
            $after_roundoff = $refunded - $this->first['gross_total'];
        }
        //

        if ($this->first['gross_total'] - $refunded > 0) {
            $net_sales = number_format(abs($after_roundoff), 2);
        } else {
            $net_sales = '(' . number_format(abs($after_roundoff), 2) . ')';

        }
        $print['net_total'] = str_pad("Net Sales", 45, " ", STR_PAD_LEFT) .
            str_pad($net_sales, 19, " ", STR_PAD_LEFT);

        $cash_refund = "-";
        $cash_refund_text = "Cash";


        if ($after_roundoff > 0) {
            $cash_refund_text = "(Refund)";
            $cash_refund = '(' . number_format(abs($after_roundoff), 2) . ')';
        } else {
            if ($this->first['is_credit'] == 'f') {

                $cash_refund = number_format(abs($after_roundoff), 2);
            }

        }

        $print['receive_amount'] = str_pad($cash_refund_text, 45, " ", STR_PAD_LEFT) .
            str_pad($cash_refund, 19, " ", STR_PAD_LEFT);

        $credit = "-";
        if ($this->first['is_credit'] == 't' && $cash_refund_text != "(Refund)") {
            $credit = number_format(abs($refunded - $this->first['gross_total']), 2);
        }
        $print['note'] = "NOTE: Medicines will not be returned or changed after " . env("RETURN_POLICY_DAYS", "3") . " days.";
        $print['note1'] = "There will be no refund or change without the original bill.";
        $print['note2'] = "Refrigerator items will never be returned.";
        $print['change_returned'] = str_pad("Credit", 45, " ", STR_PAD_LEFT) .
            str_pad($credit, 19, " ", STR_PAD_LEFT);

        return $print;
    }
}
