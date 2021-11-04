<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Illuminate\Support\Facades\DB;
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
    public $cos_rp;
    public $report = [];

    public function mount()
    {
        $this->zero_stock = 'f';
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
            ->leftJoin('product_inventories as pi', function ($q) {
                return $q->on('pi.product_id', '=', 'p.id');
            })
            ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
            ->when(!empty($this->product_id), function ($q) {
                return $q->where('p.id', $this->product_id);
            })
            ->when(!empty($this->manufacture_id), function ($q) {
                return $q->where('p.manufacture_id', $this->manufacture_id);
            })
            ->when(!empty($this->rack_id), function ($q) {
                return $q->where('p.rack_id', $this->rack_id);
            })
            ->when(!empty($this->category_id), function ($q) {
                return $q->where('p.category_id', $this->category_id);
            })
            ->when($this->zero_stock == 't', function ($q) {
                return $q->where('pi.qty', '>=', '0');
            })
            ->when($this->zero_stock == 'f', function ($q) {
                return $q->where('pi.qty', '>', '0');
            })
            ->groupBy('pi.product_id')
            ->groupBy('pi.supply_price')
            ->groupBy('pi.retail_price')
            ->groupBy('pi.batch_no')
            ->orderBy('p.name', 'ASC')
            ->select(
                'p.id', 'p.name as item', 'm.name as manufacturer', 'c.name as category', 'r.name as rack',
                DB::raw('sum(pi.qty) as stock_in_hand'),
                'pi.supply_price as cos', 'pi.retail_price', 'pi.batch_no'
            )
            ->get();

        $this->report = [];

        foreach ($products->toArray() as $key => $p) {
            if ($this->cos_rp == 't' && $p['cos'] < $p['retail_price']) {
                continue;
            }
            $data = $p;
            $data['total_stock_value'] = $p['stock_in_hand'] * $p['cos'];
            $data['total_retail_value'] = $p['stock_in_hand'] * $p['retail_price'];
            $data['gross_margin_pkr'] = $data['total_retail_value'] - $data['total_stock_value'];
            if ($data['total_retail_value'] > 0) {
                $data['gross_margin_per'] = 100 - (($data['total_stock_value'] / $data['total_retail_value']) * 100);

            } else {
                $data['gross_margin_per'] = 0;

            }
            $this->report[] = $data;
        }
    }

    public function resetSearch()
    {
        $this->reset('product_id', 'cos_rp', 'product_name', 'rack_id', 'rack_name', 'category_id', 'category_name', 'manufacture_id', 'manufacture_name');
        $this->search();
    }
}