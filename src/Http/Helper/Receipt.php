<?php


namespace Devzone\Pharmacy\Http\Helper;


use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class Receipt
{
    public static function generate($sales, $sale_id)
    {
        $sale = collect($sales)->first();
        $connector = new WindowsPrintConnector(env('RECEIPT_PRINTER_NAME'));
        $printer = new Printer($connector);


        $logo = EscposImage::load(env('RECEIPT_PRINTER_LOGO'), false);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->bitImage($logo, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);


        /* Name of shop */
        $printer->feed();
        $printer->selectPrintMode();
        $printer->text(env('receipt_printer_address_1') . "\n");
        $printer->text(env('receipt_printer_address_2') . "\n");
        $printer->feed();


        /* Title of receipt */
        $printer->setEmphasis(true);
        $printer->text("SALES INVOICE #" . $sale_id . "\n");
        $printer->setEmphasis(false);
        $printer->feed();


        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Sale By : " . $sale['sale_by'] . " \n");
        $printer->text("Sale At : " . date('d M Y h:i A', strtotime($sale['sale_by'])) . " \n");
        $printer->feed();
        $printer->text("================================================\n");
        $printer->text(str_pad("Item", 20, " ") . str_pad("Qty", 6, " ", STR_PAD_LEFT) . str_pad("Unit", 10, " ", STR_PAD_LEFT) . str_pad("Total", 12, " ", STR_PAD_LEFT) . "\n");
        $printer->text("================================================\n");

        foreach ($sales as $key => $s) {

            $item = substr($s['item'], 0, 20);
            $item = str_pad($item, 20, " ");
            $qty = str_pad($s['sale_qty'], 6, " ", STR_PAD_LEFT);
            $retail = str_pad($s['retail_price'], 9, " ", STR_PAD_LEFT);
            $total = str_pad($s['total'], 11, " ", STR_PAD_LEFT);
            $printer->text($item . $qty . " " . $retail . " " . $total . "\n");
        }
        $printer->text("------------------------------------------------\n");
        $collect = collect($sales);

        $printer->text(str_pad("Sub Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total'), 2), 15, " ", STR_PAD_LEFT) . "\n");
        $printer->text(str_pad("Discount (PKR)", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total') - $collect->where('sale_qty', '>', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT) . "\n");
        $printer->text(str_pad("Gross Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT) . "\n");
        $printer->text(str_pad("Refunded", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '<', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT) . "\n");
        $printer->text(str_pad("Net Total", 33, " ", STR_PAD_LEFT) .
            str_pad(number_format($collect->where('sale_qty', '>', 0)->sum('total_after_disc') - $collect->where('sale_qty', '<', 0)->sum('total_after_disc'), 2), 15, " ", STR_PAD_LEFT) . "\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->feed('2');
        $printer->text(env('RECEIPT_PRINTER_TAGLINE'));
        $printer->feed('2');
        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        $printer->pulse();

        $printer->close();
    }
}
