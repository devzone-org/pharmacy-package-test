<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Models\ProductInventory;
use Livewire\Component;

class ExpiredProducts extends Component
{
    public $data=[];
    public $date;
    public function mount()
    {
        $this->date = date('Y-m-d',strtotime('-3 months'));
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.expired-products');
    }

    public function search()
    {
        $this->data=ProductInventory::from('product_inventories as pi')
            ->join('products as p','p.id','=','pi.product_id')
            ->leftJoin('sale_details as sd',function ($q){
                return $q->on('sd.product_id','=','p.id')
                    ->whereRaw('sd.id IN (select MAX(sd2.id) from sale_details as sd2 join products as p2 on p2.id = sd2.product_id group by p2.id)');
            })
            ->leftJoin('sales as sl','sl.id','=','sd.sale_id')
            ->leftJoin('purchases as pur','pur.id','=','pi.po_id')
            ->leftJoin('suppliers as s','s.id','=','pur.supplier_id')
            ->where('pi.expiry','<=',$this->date)
            ->select('p.id','p.name as product','s.name as supplier','pi.expiry','pi.qty','sd.id as sd','sl.sale_at')
            ->get()->toArray();
    }
}