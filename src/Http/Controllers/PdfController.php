<?php

namespace Devzone\Pharmacy\Http\Controllers;

use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;


class PdfController
{

    public function PoDownloadPDF(Request $request){

        $purchase_id = $request->purchase_id ;

        $purchase = Purchase::from('purchases as p')
            ->join('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->join('users as c', 'c.id', '=', 'p.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'p.approved_by')
            ->where('p.id', $purchase_id)
            ->select(
                'p.id', 'p.supplier_invoice', 'p.grn_no', 'p.is_paid', 'p.delivery_date', 'p.status', 'p.supplier_id', 'p.approved_at', 'p.advance_tax', 'p.created_at',
                's.name as supplier_name', 'a.name as approved_by', 'c.name as created_by', 'p.description')
            ->first();
        if ($purchase->status == 'received') {
            $details = \Devzone\Pharmacy\Models\PurchaseReceive::from('purchase_receives as pr')
                ->join('products as p', 'p.id', '=', 'pr.product_id')
                ->where('pr.purchase_id', $purchase_id)
                ->select('pr.*', 'p.name', 'p.salt')
                ->get();
        } else {
            $details = PurchaseOrder::from('purchase_orders as po')
                ->join('products as p', 'p.id', '=', 'po.product_id')
                ->where('po.purchase_id', $purchase_id)
                ->select('po.*', 'p.name', 'p.salt','p.packing',DB::raw('(po.qty/p.packing) as quantity'))
                ->get();
        }
        $purchase_receive = \Devzone\Pharmacy\Models\PurchaseReceive::where('purchase_id', $purchase_id)
            ->select(DB::raw('sum(total_cost) as total_receive'))
            ->groupBy('purchase_id')
            ->first();
        $supplier_payment_details = SupplierPaymentDetail::from('supplier_payment_details as spd')
            ->join('supplier_payments as sp', 'sp.id', '=', 'spd.supplier_payment_id')
            ->join('users as u', 'u.id', '=', 'sp.added_by')
            ->leftJoin('users as us', 'us.id', '=', 'sp.approved_by')
            ->where('spd.order_id',$purchase_id)
            ->select('sp.created_at', 'sp.approved_at', 'u.name as added_by', 'us.name as approved_by')
            ->first();

        $code = env('CLIENT_CODE');

        $path = url(env('CLIENT_LOGO', '/images/default.png'));
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);




        $pdf = PDF::loadView('pharmacy::pdf.purchase-order.purchases-view', compact('purchase_id','purchase', 'purchase_receive',  'supplier_payment_details', 'details','code','logo'));

        return $pdf->download('PO-view.pdf');


    }

}