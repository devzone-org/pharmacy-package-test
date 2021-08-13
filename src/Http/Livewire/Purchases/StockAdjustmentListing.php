<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Livewire\Component;

class StockAdjustmentListing extends Component
{

    use Searchable;

    public $product_id;
    public $product_name;
    public $indicator;
    public $from;
    public $to;
    public function mount()
    {

    }

    public function render()
    {
        $stock = \Devzone\Pharmacy\Models\StockAdjustment::from('stock_adjustments as sa')
            ->join('products as p', 'p.id', '=', 'sa.product_id')
            ->join('users as u', 'u.id', '=', 'sa.added_by')
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('sa.product_id', $this->product_id);
            })
            ->when(!empty($this->indicator), function ($q) {
                return $q->where('sa.indicator', $this->indicator);
            })
            ->when(!empty($this->from), function ($q) {
                return $q->whereDate('sa.created_at', '>=',$this->from);
            })
            ->when(!empty($this->to), function ($q) {
                return $q->whereDate('sa.created_at', '<=',$this->to);
            })
            ->select('p.name', 'sa.indicator', 'sa.qty', 'sa.remarks', 'u.name as added_by', 'sa.voucher_no', 'sa.created_at')
            ->paginate(50);
        return view('pharmacy::livewire.purchases.stock-adjustment-listing', compact('stock'));
    }

    public function search(){

    }
}
