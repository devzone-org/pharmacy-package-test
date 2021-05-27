<?php


namespace Devzone\Pharmacy\Http\Traits;


use Devzone\Pharmacy\Models\Category;
use Devzone\Pharmacy\Models\Manufacture;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Rack;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;

trait Searchable
{
    public $searchable_query = '';
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
        'supplier'  => ['name','address']
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
        $data = $this->searchable_data[$this->highlight_index] ?? null;
        $this->{$this->searchable_id} = $data['id'];
        $this->{$this->searchable_name} = $data['name'];
        $this->searchableReset();
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

            if ($this->searchable_type == 'product') {
                $search = Product::from('products as p')
                    ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                    ->where('p.name', 'LIKE', '%' . $value . '%')
                    ->leftJoin('racks as r', 'r.id', '=', 'p.rack_id')
                    ->select('p.id','p.name', 'p.salt as generic', 'c.name as category',
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