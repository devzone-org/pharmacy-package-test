<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;



use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\ExpiryAdjustmentLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ExpiryAdjustmentListing extends Component
{
    use Searchable, WithPagination;

    public $all_users;
    public $product_id;
    public $product_id_id;
    public $product_name;
    public $from;
    public $to;
    public $added_by;


    public function mount()
    {
        $this->all_users = User::all();
    }

    public function render()
    {
        $expiry = ExpiryAdjustmentLog::from('expiry_adjustment_logs as eal')
            ->join('product_inventories as pi', 'pi.id', '=', 'eal.product_inventory_id')
            ->join('products as pro', 'pro.id', '=', 'pi.product_id')
            ->join('users as u', 'u.id', '=', 'eal.created_by')
            ->when(!empty($this->product_id_id), function ($q) {
                return $q->where('pi.product_id', $this->product_id_id);
            })
            ->when(!empty($this->added_by), function ($q) {
                return $q->where('eal.created_by', $this->added_by);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('eal.created_at', '>=',$this->from);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('eal.created_at', '<=',$this->to);
            })
            ->select('pro.name as product', 'eal.old_expiry', 'pi.po_id', 'eal.new_expiry', 'eal.remarks', 'u.name as added_by', 'eal.created_at')
            ->paginate(50);


        return view('pharmacy::livewire.purchases.expiry-adjustment-listing', ['expiry_adjustments'=>$expiry]);
    }

    public function search(){
        $this->product_id_id = $this->product_id;
        $this->resetPage();
    }

    public function resetSearch(){
        $this->reset(['product_id_id','product_id','product_name','from','to','added_by']);
        $this->resetPage();
    }

}
