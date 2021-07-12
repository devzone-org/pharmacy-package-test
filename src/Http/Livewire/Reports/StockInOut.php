<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Livewire\Component;

class StockInOut extends Component
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
    public $norcotics;
    public $report = [];

    public function mount()
    {
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.stock-in-out');
    }

    public function search()
    {
        $this->reset('report');
        $products = Product::from('products as p')
            ->join('product_inventories as pi', function ($q) {
                return $q->on('pi.product_id', '=', 'p.id')
                    ->where('pi.qty', '>', '0')
                    ->where('pi.expiry', '<=', date('Y-m-d', strtotime('+3 months')));
            })
            ->join('purchases as pur', 'pur.id', '=', 'pi.po_id')
            ->join('suppliers as s', 's.id', '=', 'pur.supplier_id')
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
            ->when(!empty($this->norcotics), function ($q) {
                return $q->where('p.narcotics', $this->norcotics);
            })
            ->orderBy('p.id', 'ASC')
            ->select(
                'p.id', 'pi.id as pi_id', 'p.name as item', 'p.narcotics', 'm.name as manufacturer', 'c.name as category', 'pi.qty', 'pi.po_id', 'pi.expiry', 'r.name as rack',
                's.name as supplier_name',
            )
            ->get();
        foreach ($products as $key => $product) {
            $this->report[$key]['id'] = $product->id;
            $this->report[$key]['item'] = $product->item;
            $this->report[$key]['narcotics'] = $product->narcotics;
            $this->report[$key]['manufacturer'] = $product->manufacturer;
            $this->report[$key]['category'] = $product->category;
            $this->report[$key]['supplier'] = $product->supplier_name;
            $this->report[$key]['rack'] = $product->rack;
            $this->report[$key]['expiry'] = $product->expiry;
            $this->report[$key]['po_id'] = $product->po_id;
            $this->report[$key]['stock_in_hand'] = $product->qty;
            $last_sold = SaleDetail::from('sale_details as sd')
                ->join('sales as s', 's.id', '=', 'sd.sale_id')
                ->where('sd.product_inventory_id', $product->pi_id)
                ->select('s.sale_at')
                ->orderBy('sd.id', 'DESC')
                ->first();
            $this->report[$key]['last_sold'] = null;
            if (!empty($last_sold)) {
                $this->report[$key]['last_sold'] = $last_sold->sale_at;
            }

            $this->report[$key]['expired'] = false;
            if ($product->expiry <= date('Y-m-d')) {
                $this->report[$key]['expired'] = true;
            } elseif ($product->expiry > date('Y-m-d')) {
                $from = Carbon::parse(date('Y-m-d'));
                $to = Carbon::parse($product->expiry);
                $diff = $from->diff($to);
                $this->report[$key]['expiring_in'] = $diff->format("%m months, %d days");
            }
        }
    }

    public function resetSearch()
    {
        $this->reset('norcotics','product_id', 'product_name', 'rack_id', 'rack_name', 'category_id', 'category_name', 'manufacture_id', 'manufacture_name', 'supplier_id', 'supplier_name');
        $this->search();
    }
}