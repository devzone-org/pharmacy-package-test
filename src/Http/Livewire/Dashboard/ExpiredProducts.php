<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Pharmacy\Http\Traits\DashboardDate;
use Devzone\Pharmacy\Models\ProductInventory;
use Livewire\Component;

class ExpiredProducts extends Component
{
    public $data=[];
    public $date;
    public function mount()
    {
        $this->date = '2021-08-10';
    }

    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.expired-products');
    }

    public function search()
    {
        $expired=ProductInventory::from('product_inventories as pi')
            ->join('products as p','p.id','=','pi.product_id')
            ->where('pi.expiry','<=',$this->date)
            ->get();
        dd($expired);

    }
}