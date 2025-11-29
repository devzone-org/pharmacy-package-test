<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use Carbon\Carbon;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Livewire\Component;
use Livewire\WithPagination;

class StockNearExpiry extends Component
{
    use Searchable, WithPagination;

    protected $paginationTheme = 'tailwind';

    public $product_id;
    public $product_name;
    public $manufacture_id;
    public $manufacture_name;
    public $rack_id;
    public $rack_name;
    public $category_id;
    public $category_name;
    public $supplier_id;
    public $supplier_name;
    public $type;
    public $expiry_date;
    public $report = [];

    public function mount()
    {
        $this->expiry_date = date('d M Y', strtotime('+3 months'));
    }

    public function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)->format('Y-m-d');
    }

    public function updating($field)
    {
        $this->resetPage(); // reset page on filter change
    }


    public function render()
    {
        $products = Product::from('products as p')
            ->join('product_inventories as pi', function ($q) {
                $q->on('pi.product_id', '=', 'p.id')
                    ->where('pi.qty', '>', 0)
                    ->where('pi.expiry', '<=', $this->formatDate($this->expiry_date));
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
            ->when(!empty($this->supplier_id), function ($q) {
                return $q->where('pur.supplier_id', $this->supplier_id);
            })
            ->when(!empty($this->type), function ($q) {
                return $q->where('p.type', $this->type);
            })
            ->select(
                'p.*','pi.id as pi_id','pi.qty','pi.po_id','pi.expiry',
                'm.name as manufacturer','c.name as category','s.name as supplier_name',
                'r.name as rack'
            )
            ->orderBy('p.id', 'ASC')
            ->paginate(15);

        $pi_ids = $products->pluck('pi_id')->toArray();

        $lastSold = SaleDetail::from('sale_details as sd')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->whereIn('sd.product_inventory_id', $pi_ids)
            ->select('sd.product_inventory_id', 's.sale_at')
            ->orderByDesc('sd.id')
            ->get()
            ->groupBy('product_inventory_id')
            ->map(function ($items) {
                return $items->first()->sale_at;
            });

        // --- Add computed fields to each product ---
        foreach ($products as $product) {
            $product->last_sold = $lastSold[$product->pi_id] ?? null;
            $product->expired = $product->expiry <= date('Y-m-d');

            if (!$product->expired) {
                $diff = Carbon::now()->diff(Carbon::parse($product->expiry));
                $product->expiring_in = $diff->format('%m months %d days');
            } else {
                $product->expiring_in = 'Already Expired';
            }
        }

        return view('pharmacy::livewire.reports.stock-near-expiry', [
            'products' => $products
        ]);
    }



    public function resetSearch()
    {
        $this->reset('product_id', 'product_name', 'rack_id', 'rack_name', 'category_id', 'category_name', 'manufacture_id', 'manufacture_name', 'supplier_id', 'supplier_name');
        $this->search();
    }
}