<?php


namespace Devzone\Pharmacy\Http\Traits;


use App\Models\Hospital\Employees\Employee;
use App\Models\Hospital\Patient;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Models\Category;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\Manufacture;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Rack;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Searchable
{
    public $product_qty;
    public $value_set = false;

    public $searchable_query = '';
    public $searchable_emit_only = false;
    public $searchable_data = [];
    public $highlight_index = 0;
    public $searchable_modal = false;
    public $searchable_id;
    public $searchable_name;
    public $searchable_type;
    public $searchable_loading = false;
    public $searchable_column = [
        'manufacture' => ['name', 'contact', 'address'],
        'rack' => ['name', 'tier'],
        'category' => ['name'],
        'product' => ['name', 'generic', 'category', 'rack'],
        'supplier' => ['name', 'address', 'phone'],
        'pay_from' => ['name', 'code'],
        'receiving_account' => ['name', 'code'],
        'patient' => ['mr_no', 'name', 'phone'],
        'referred_by' => ['name'],
        'item' => ['item', 'qty', 'retail_price', 'manufacturer', 'rack', 'tier', 'packing'],
        'adjustment_items' => ['item', 'qty', 'expiry', 'packing'],
        'customer' => ['name', 'care_of', 'credit_limit']
    ];


    public function searchableOpenModal($id, $name, $type)
    {
        $this->searchableReset();
        $this->searchable_id = $id;
        $this->searchable_name = $name;
        $this->searchable_type = $type;
        $this->emit('focusInput');
        $this->searchable_modal = true;

    }

    public function searchableReset()
    {
        $this->searchable_modal = false;
        $this->searchable_id = '';
        $this->searchable_name = '';
        $this->highlight_index = 0;
        $this->searchable_query = '';
        $this->searchable_type = '';
        $this->searchable_data = [];
    }

    public function incrementHighlight()
    {
        if ($this->highlight_index === count($this->searchable_data) - 1) {
            $this->highlight_index = 0;
            return;
        }
        $this->highlight_index++;
    }

    public function decrementHighlight()
    {
        if ($this->highlight_index === 0) {
            $this->highlight_index = count($this->searchable_data) - 1;
            return;
        }
        $this->highlight_index--;
    }

    public function searchableSelection($key = null)
    {
        if (empty($this->searchable_data)) {
            return;
        }
        if (!empty($key)) {
            $this->highlight_index = $key;
        }
        if ($this->searchable_emit_only) {
            $this->emitSelf(Str::camel('emit_' . $this->searchable_id));
        } else {

            $data = $this->searchable_data[$this->highlight_index] ?? null;
            $this->{$this->searchable_id} = $data['id'];
            $this->{$this->searchable_name} = $data['name'];
            $this->emitSelf(Str::camel('emit_' . $this->searchable_id));
            $this->searchableReset();
        }

    }

    public function updatedSearchableQuery($value)
    {
        if (strlen($value) > 1) {
            $this->searchQuery($value);
        } else {
            $this->searchable_data = [];
        }
    }

    public function itemSalt($value)
    {

        if (strlen($value) > 1) {
            $this->searchQuery($value);
            $this->searchable_query = $value;
        } else {
            $this->searchable_data = [];
        }
    }

    private function searchQuery($value = null)
    {
        $this->highlight_index = 0;
        $this->searchable_loading = true;
        if ($this->searchable_type == 'manufacture') {
            $search = Manufacture::where('status', 't')
                ->when(!empty($value), function ($q) use ($value) {
                    return $q->where('name', 'LIKE', '%' . $value . '%');
                })
                ->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'pay_from' || $this->searchable_type == 'receiving_account') {
            $search = ChartOfAccount::where('status', 't')->where('type', 'Assets')
                ->whereIn('sub_account', [11, 12])
                ->when(!empty($value), function ($q) use ($value) {
                    return $q->where('name', 'LIKE', '%' . $value . '%');
                })
                ->get();

            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }


        }

        if ($this->searchable_type == 'product') {
            $search = Product::from('products as p')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->when(!empty($value), function ($q) use ($value) {
                    return $q->where('p.name', 'LIKE', '%' . $value . '%');
                })
                ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                ->select('p.id', 'p.name', 'p.salt as generic', 'c.name as category', 'p.control_medicine',
                    DB::raw("CONCAT(COALESCE(r.name,''),' ',COALESCE(r.tier,'')) AS rack")
                )->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'category') {
            $search = Category::where('status', 't')
                ->when(!empty($value), function ($q) use ($value) {
                    return $q->where('name', 'LIKE', '%' . $value . '%');
                })
                ->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'supplier') {
            $search = Supplier::where('status', 't')
                ->when(!empty($value), function ($q) use ($value) {
                    return $q->where('name', 'LIKE', '%' . $value . '%');
                })
                ->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'item') {
            $product_ids = [];
            $ids_ordered = "-1";
            if (env('SCOUT_SEARCH', false)) {
                $records = Product::search($value)->take(20)->get();
                if ($records->isNotEmpty()) {
                    $product_ids = $records->pluck('id')->toArray();
                    $ids_ordered = implode(',', $product_ids);
                }
            }
            $search = Product::from('products as p')
                ->leftJoin('product_inventories as pi', 'p.id', '=', 'pi.product_id')
                ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                ->leftJoin('manufactures as m', 'm.id', '=', 'p.manufacture_id');

            if (env('SCOUT_SEARCH', false)) {
                $search = $search->whereIn('p.id', $product_ids);
            } else {
                $search = $search->where(function ($q) use ($value) {
                    return $q->orWhere('p.name', 'LIKE', '%' . $value . '%')
                        ->orWhere('p.salt', 'LIKE', '%' . $value . '%');
                });
            }

            $search = $search->select('p.name as item', DB::raw('SUM(qty) as qty'),
                'pi.retail_price', 'p.retail_price as product_price', 'p.cost_of_price as product_supply_price', 'm.name as manufacturer', 'p.salt',
                'pi.supply_price', 'pi.id', 'p.packing', 'pi.product_id', 'p.type', 'p.discountable', 'p.max_discount', 'r.name as rack', 'r.tier', 'p.control_medicine')
                ->groupBy('p.id')
                ->groupBy('pi.retail_price');
            if (env('SCOUT_SEARCH', false)) {
                $search = $search->orderByRaw("FIELD(p.id, $ids_ordered)");
            } else {
                $search = $search->orderBy('qty', 'desc');
            }
            $search = $search->limit(20)->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'adjustment_items') {
            $search = Product::from('products as p')
                ->leftJoin('product_inventories as pi', 'p.id', '=', 'pi.product_id')
                ->where('pi.qty','>',0)
                ->where(function ($q) use ($value) {
                    return $q->orWhere('p.name', 'LIKE', '%' . $value . '%')
                        ->orWhere('p.salt', 'LIKE', '%' . $value . '%');
                })->select('p.name as item', DB::raw('SUM(qty) as qty'), 'p.id',
                    'pi.expiry', 'p.packing')
//                ->groupBy('p.id')
                ->groupBy('pi.expiry')->orderBy('qty', 'desc')->orderBy('p.name', 'asc')->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'referred_by') {
            $search = Employee::where(function ($q) use ($value) {
                return $q->orWhere('name', 'LIKE', '%' . $value . '%');
            })->select('name', 'id')->where('is_doctor', 't')->where('status', 't')->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'patient') {
            $search = Patient::where(function ($q) use ($value) {
                return $q->orWhere('name', 'LIKE', '%' . $value . '%')
                    ->orWhere('mr_no', 'LIKE', '%' . $value . '%')
                    ->orWhere('phone', 'LIKE', '%' . $value . '%');
            })->select('mr_no', 'name', 'phone', 'id', 'customer_id', 'account_id')->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }

        if ($this->searchable_type == 'rack') {
            $search = Rack::where('status', 't')
                ->where(function ($q) use ($value) {
                    return $q->orWhere('name', 'LIKE', '%' . $value . '%')
                        ->orWhere('tier', 'LIKE', '%' . $value . '%');
                })
                ->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }
        if ($this->searchable_type == 'customer') {
            $search = Customer::from('customers as c')
                ->join('employees as e', 'e.id', '=', 'c.employee_id')
                ->where(function ($q) use ($value) {
                    return $q->orWhere('c.name', 'LIKE', '%' . $value . '%');
                })->select('c.name', 'c.credit_limit', 'e.name as care_of', 'c.id')->get();
            if ($search->isNotEmpty()) {
                $this->searchable_data = $search->toArray();
            } else {
                $this->searchable_data = [];
            }
        }
        $this->searchable_loading = false;
    }

}
