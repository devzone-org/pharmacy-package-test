<?php


namespace Devzone\Pharmacy\Http\Livewire\Refunds\Supplier;


use Devzone\Pharmacy\Models\Refunds\SupplierRefund;
use Devzone\Pharmacy\Models\Refunds\SupplierRefundDetail;
use Livewire\Component;

class View extends Component
{
    public $primary_id;
    public $refund;
    public $refund_details;


    public function mount($primary_id)
    {
        $this->primary_id = $primary_id;
        $this->refund = SupplierRefund::from('supplier_refunds as sr')
            ->join('suppliers as s', 's.id', '=', 'sr.supplier_id')
            ->leftJoin('chart_of_accounts as coa', 'coa.id', '=', 'sr.receive_in')
            ->join('users as c', 'c.id', '=', 'sr.created_by')
            ->leftJoin('users as a', 'a.id', '=', 'sr.approved_by')
            ->where('sr.id', $primary_id)
            ->select('s.name as supplier_name', 'sr.description', 'coa.name as account_name', 'sr.receiving_date'
                , 'c.name as created_by', 'a.name as approved_by', 'sr.is_receive', 'sr.created_at', 'sr.approved_at')
            ->first();

        $this->refund_details = SupplierRefundDetail::from('supplier_refund_details as srd')
            ->join('products as p', 'p.id', '=', 'srd.product_id')
            ->join('product_inventories as pi', 'pi.id', '=', 'srd.product_inventory_id')
            ->where('srd.supplier_refund_id', $primary_id)
            ->select('p.name as product_name', 'srd.po_id', 'srd.qty', 'pi.supply_price', 'pi.qty as available')
            ->get();


    }

    public function render()
    {
        return view('pharmacy::livewire.refunds.supplier.view');
    }
}
