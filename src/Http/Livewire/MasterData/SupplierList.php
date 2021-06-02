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
    public $name;
    public $phone;

    public function render()
    {
        $suppliers = Supplier::when(!empty($this->name),function($q){
            return $q->where('name','LIKE','%'.$this->name.'%');
        })
            ->when(!empty($this->phone),function($q){
                return $q->where('phone','LIKE','%'.$this->phone.'%');
            })->orderBy('id','desc')->paginate(20);

        return view('pharmacy::livewire.master-data.supplier-list', ['suppliers' => $suppliers]);
    }


    public function search(){
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->reset(['name','phone']);
        $this->resetPage();
    }

}
