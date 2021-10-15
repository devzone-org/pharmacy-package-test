<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Carbon\Carbon;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Livewire\Component;
use Livewire\WithPagination;

class StockAdjustmentListing extends Component
{

    use Searchable,WithPagination;

    public $product_id;
    public $product_id_id;
    public $product_name;
    public $indicator;
    public $from;
    public $to;


    private function formatDate($date){
        return Carbon::createFromFormat('d M Y',$date)
            ->format('Y-m-d');
    }

    public function render()
    {
        $stock = \Devzone\Pharmacy\Models\StockAdjustment::from('stock_adjustments as sa')
            ->join('products as p', 'p.id', '=', 'sa.product_id')
            ->join('users as u', 'u.id', '=', 'sa.added_by')
            ->when(!empty($this->product_id_id), function ($q) {
                return $q->where('sa.product_id', $this->product_id_id);
            })
            ->when(!empty($this->indicator), function ($q) {
                return $q->where('sa.indicator', $this->indicator);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('sa.created_at', '>=',$this->formatDate($this->from));
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('sa.created_at', '<=',$this->formatDate($this->to));
            })
            ->select('p.name', 'sa.indicator', 'sa.qty', 'sa.remarks', 'u.name as added_by', 'sa.voucher_no', 'sa.created_at')
            ->paginate(50);
        return view('pharmacy::livewire.purchases.stock-adjustment-listing', compact('stock'));
    }

    public function search(){
        $this->product_id_id = $this->product_id;
    }

    public function resetSearch(){
        $this->reset(['product_id_id','product_id','product_name','from','to','indicator']);
        $this->resetPage();
    }


}
