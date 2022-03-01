<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class StockReorderLevel extends Component
{
    use Searchable,WithPagination;

    public $product_id;
    public $product_name;

    public $manufacture_id;
    public $manufacture_name;
    public $type;
//    public $report = [];

    public function mount()
    {

    }

    public function render()
    {

        $report = [];
        $products = Product::from('products as p')
            ->join('product_inventories as pi', function ($q) {
                return $q->on('pi.product_id', '=', 'p.id');
            })
            ->Join('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('p.id', $this->product_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('p.manufacture_id', $this->manufacture_id);
            })
            ->when($this->type == 'reorder_level', function ($q) {
                return $q->where('p.reorder_level', '>', '0');
            })
            ->select(
                'p.id', 'p.name as item', 'p.type', 'p.reorder_level', 'p.reorder_qty', 'm.name as manufacturer',
            )
            ->groupBy('pi.product_id')
            ->orderBy('p.id', 'ASC')
            ->get();
        foreach ($products as $key => $product) {
            $report[$key]['id'] = $product->id;
            $report[$key]['item'] = $product->item;
            $report[$key]['type'] = $product->type;
            $report[$key]['manufacturer'] = $product->manufacturer;
            $report[$key]['stock_in_hand'] = $product->inventories->sum('qty');
            $report[$key]['reorder_level'] = $product->reorder_level;
            $report[$key]['reorder_qty'] = $product->reorder_qty;
        }

        $report = $this->paginate($report);


        return view('pharmacy::livewire.reports.stock-reorder-level', ['report' => $report]);
    }

    public function search()
    {
        $this->resetPage();

//        $this->reset('report');
//
//        $products = Product::from('products as p')
//            ->join('product_inventories as pi', function ($q) {
//                return $q->on('pi.product_id', '=', 'p.id');
//            })
//            ->Join('manufactures as m', 'm.id', '=', 'p.manufacture_id')
//            ->when(!empty($this->product_id), function ($q) {
//                return $q->where('p.id', $this->product_id);
//            })
//            ->when(!empty($this->manufacture_id), function ($q) {
//                return $q->where('p.manufacture_id', $this->manufacture_id);
//            })
//            ->when($this->type == 'reorder_level', function ($q) {
//                return $q->where('p.reorder_level', '>', '0');
//            })
//            ->select(
//                'p.id', 'p.name as item', 'p.type', 'p.reorder_level', 'p.reorder_qty', 'm.name as manufacturer',
//            )
//            ->groupBy('pi.product_id')
//            ->orderBy('p.id', 'ASC')
//            ->get();
//        foreach ($products as $key => $product) {
//            $this->report[$key]['id'] = $product->id;
//            $this->report[$key]['item'] = $product->item;
//            $this->report[$key]['type'] = $product->type;
//            $this->report[$key]['manufacturer'] = $product->manufacturer;
//            $this->report[$key]['stock_in_hand'] = $product->inventories->sum('qty');
//            $this->report[$key]['reorder_level'] = $product->reorder_level;
//            $this->report[$key]['reorder_qty'] = $product->reorder_qty;
//        }
//
//       $this->paginate($this->report);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

       return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }

    public function resetSearch()
    {
        $this->reset('product_id', 'product_name', 'manufacture_id', 'manufacture_name', 'type');
        $this->search();
    }

}