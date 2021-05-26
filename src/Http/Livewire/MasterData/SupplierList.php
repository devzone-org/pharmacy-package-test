<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public $success = '';


    public function render()
    {
        $suppliers = Supplier::orderBy('id','desc')->paginate(20);

        return view('pharmacy::livewire.master-data.supplier-list', ['suppliers' => $suppliers]);
    }


}
