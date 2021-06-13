<?php


namespace Devzone\Pharmacy\Http\Traits;


use App\Models\Hospital\Employees\Employee;
use App\Models\Hospital\Patient;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Models\Category;
use Devzone\Pharmacy\Models\Manufacture;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Rack;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Searchable
{
    public $searchable_query = '';
    public $searchable_emit_only = false;
    public $searchable_data = [];
    public $highlight_index = 0;
    public $searchable_modal = false;
    public $searchable_id;
    public $searchable_name;
    public $searchable_type;
    public $searchable_column = [
        'manufacture' => ['name', 'contact', 'address'],
        'rack' => ['name', 'tier'],
        'category' => ['name'],
        'product' => ['name', 'generic', 'category', 'rack'],
        'supplier' => ['name', 'address', 'phone'],
        'pay_from' => ['name', 'code'],
        'receiving_account' => ['name', 'code'],
        'patient' => ['mr_no','name', 'phone'],
        'referred_by' => ['name'],
        'inventory' => ['item', 'qty', 'retail_price', 'rack', 'tier']
    ];


    public function searchableOpenModal($id, $name, $type)
    {
        $this->searchable_modal = true;
        $this->searchable_id = $id;
        $this->searchable_name = $name;
        $this->searchable_type = $type;
        $this->emit('focusInput');
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

    public function updatedSearchableQuery($value)
    {
        if (strlen($value) > 1) {
            $this->highlight_index = 0;
            if ($this->searchable_type == 'manufacture') {
                $search = Manufacture::where('status', 't')->where('name', 'LIKE', '%' . $value . '%')->get();
                if ($search->isNotEmpty()) {
                    $this->searchable_data = $search->toArray();
                } else {
                    $this->searchable_data = [];
                }
            }

            if ($this->searchable_type == 'pay_from' || $this->searchable_type == 'receiving_account') {
                $search = ChartOfAccount::where('status', 't')->where('type', 'Assets')
                    ->whereIn('sub_account', [11, 12])->where('name', 'LIKE', '%' . $value . '%')->get();

                if ($search->isNotEmpty()) {
                    $this->searchable_data = $search->toArray();
                } else {
                    $this->searchable_data = [];
                }


            }

            if ($this->searchable_type == 'product') {
                $search = Product::from('products as p')
                    ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                    ->where('p.name', 'LIKE', '%' . $value . '%')
                    ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                    ->select('p.id', 'p.name', 'p.salt as generic', 'c.name as category',
                        DB::raw("CONCAT(COALESCE(`r.name`,''),' ',COALESCE(`r.tier`,'')) AS rack")
                    )->get();
                if ($search->isNotEmpty()) {
                    $this->searchable_data = $search->toArray();
                } else {
                    $this->searchable_data = [];
                }
            }

            if ($this->searchable_type == 'category') {
                $search = Category::where('status', 't')->where('name', 'LIKE', '%' . $value . '%')->get();
                if ($search->isNotEmpty()) {
                    $this->searchable_data = $search->toArray();
                } else {
                    $this->searchable_data = [];
                }
            }

            if ($this->searchable_type == 'supplier') {
                $search = Supplier::where('status', 't')->where('name', 'LIKE', '%' . $value . '%')->get();
                if ($search->isNotEmpty()) {
                    $this->searchable_data = $search->toArray();
                } else {
                    $this->searchable_data = [];
                }
            }

            if ($this->searchable_type == 'inventory') {
                $search = Product::from('products as p')
                    ->leftJoin('product_inventories as pi', 'p.id', '=', 'pi.product_id')
                    ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                    ->where(function ($q) use ($value) {
                        return $q->orWhere('p.name', 'LIKE', '%' . $value . '%')
                            ->orWhere('p.salt', 'LIKE', '%' . $value . '%');
                    })->select('p.name as item', DB::raw('SUM(qty) as qty'),
                        'pi.retail_price', 'pi.supply_price', 'pi.id', 'pi.product_id', 'r.name as rack', 'r.tier')
                    ->groupBy('pi.product_id')->groupBy('pi.supply_price')
                    ->groupBy('pi.retail_price')->orderBy('qty', 'desc')->get();
                if ($search->isNotEmpty()) {
                    $this->searchable_data = $search->toArray();
                } else {
                    $this->searchable_data = [];
                }
            }

            if ($this->searchable_type == 'referred_by') {
                $search = Employee::where(function ($q) use ($value) {
                    return $q->orWhere('name', 'LIKE', '%' . $value . '%')
                       ;
                })->select('name','id')->where('is_doctor','t')->where('status','t')->get();
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
                })->select('name', 'mr_no', 'phone','id')->get();
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
        } else {
            $this->searchable_data = [];
        }
    }


}
