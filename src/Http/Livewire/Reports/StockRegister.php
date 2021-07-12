<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Livewire\Component;

class StockRegister extends Component
{
    use Searchable;

    public $product_id;
    public $product_name;
    public $manufacture_id;
    public $manufacture_name;
    public $rack_id;
    public $rack_name;
    public $category_id;
    public $category_name;
    public $zero_stock;
    public $report = [];

    public function mount()
    {
        $this->zero_stock='t';
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.stock-register');
    }

    public function search()
    {
        $this->reset('report');
        $products = Product::from('products as p')
            ->join('product_inventories as pi',function ($q){
                return $q->on('pi.product_id', '=', 'p.id');
            })
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('p.id',$this->product_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('p.manufacture_id',$this->manufacture_id);
            })
            ->when(!empty($this->rack_id), function ($q) {
                return $q->where('p.rack_id',$this->rack_id);
            })
            ->when(!empty($this->category_id), function ($q) {
                return $q->where('p.category_id',$this->category_id);
            })
            ->when(!empty($this->zero_stock), function ($q) {
                if ($this->zero_stock=='t'){
                    return $q->where('pi.qty','>=','0');
                }else{
                    return $q->where('pi.qty','>','0');
                }

            })
            ->groupBy('pi.product_id')
            ->orderBy('p.id', 'ASC')
            ->select(
                'p.id', 'p.name as item', 'p.narcotics', 'm.name as manufacturer', 'c.name as category', 'pi.barcode', 'r.name as rack',
            )
            ->get();
        foreach ($products as $key => $product) {
            $this->report[$key]['id'] = $product->id;
            $this->report[$key]['item'] = $product->item;
            $this->report[$key]['narcotics'] = $product->narcotics;
            $this->report[$key]['manufacturer'] = $product->manufacturer;
            $this->report[$key]['category'] = $product->category;
            $this->report[$key]['barcode'] = $product->barcode;
            $this->report[$key]['rack'] = $product->rack;
            $this->report[$key]['stock_in_hand'] = $product->inventories->sum('qty');
            $this->report[$key]['cos'] = $product->inventories->sum('supply_price') / $product->inventories->count();
            $this->report[$key]['total_retail_value'] = 0;
            $this->report[$key]['total_stock_value'] = 0;
            $this->report[$key]['batch_no']='';
            $this->report[$key]['expired_qty']=0;
            foreach ($product->inventories as $inv) {
                if ( $inv->qty>0){
                    $this->report[$key]['total_stock_value'] = $this->report[$key]['total_stock_value'] + ($inv->supply_price * $inv->qty);
                    $this->report[$key]['total_retail_value'] = $this->report[$key]['total_retail_value'] + ($inv->retail_price * $inv->qty);
                }

                $this->report[$key]['batch_no'] = !empty($this->report[$key]['batch_no']) ? ' , '.$inv->batch_no : $inv->batch_no;
                if ($inv->expiry>=date('Y-m-d')){
                    $this->report[$key]['expired_qty']=$this->report[$key]['expired_qty']+$inv->qty;
                }
            }
            $this->report[$key]['retail_price'] = $product->inventories->sum('retail_price') / $product->inventories->count();

            $this->report[$key]['discount'] = 0;
            foreach ($product->purchases_receive as $receive) {
                $inventory_qty=$product->inventories->where('po_id',$receive->purchase_id)->first()->qty;
                 $this->report[$key]['discount'] = $this->report[$key]['discount'] + (($receive->cost_of_price * $inventory_qty) - ($receive->after_disc_cost * $inventory_qty));
//                 $this->report[$key]['discount'] = $this->report[$key]['discount'] + (($receive->cost_of_price * $receive->qty) - ($receive->after_disc_cost * $receive->qty));
            }
            $this->report[$key]['gross_margin']=0;
            $this->report[$key]['gross_margin_percentage']=0;
            if ($this->report[$key]['total_stock_value'] > 0){
                $this->report[$key]['gross_margin'] = ($this->report[$key]['total_retail_value'] - $this->report[$key]['total_stock_value']) / $this->report[$key]['total_stock_value'];
                $this->report[$key]['gross_margin_percentage'] = (($this->report[$key]['total_retail_value'] - $this->report[$key]['total_stock_value']) / $this->report[$key]['total_stock_value']) * 100;
            }

        }
    }

    public function resetSearch(){
        $this->reset('product_id','product_name','rack_id','rack_name','category_id','category_name','manufacture_id','manufacture_name');
        $this->search();
    }
}