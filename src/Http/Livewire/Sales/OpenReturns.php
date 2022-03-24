<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Purchase;
use Devzone\Pharmacy\Models\PurchaseOrder;
use Devzone\Pharmacy\Models\Sale\OpenReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class OpenReturns extends Component
{
    use Searchable, WithPagination;

    public $product_id;
    public $product_id_id;
    public $product_name;

    public $voucher;
    public $from;
    public $to;


    public function mount()
    {

    }



    public function render()
    {
        $returns = OpenReturn::from('open_returns as or')
            ->join('users as u', 'u.id', '=', 'or.added_by')
            ->when(!empty($this->voucher), function ($q) {
                return $q->where('or.voucher', $this->voucher);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('or.created_at', '>=',$this->from);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('or.created_at', '<=',$this->to);
            })
            ->select('or.*', 'u.name as added_by')
            ->paginate(30);
        return view('pharmacy::livewire.sales.open-returns', ['returns' => $returns]);
    }

    public function search(){
        $this->resetPage();
        $this->product_id_id = $this->product_id;
    }

    public function resetSearch(){
        $this->reset(['product_id_id','product_id','product_name','from','to', 'voucher']);
        $this->resetPage();
    }
}
