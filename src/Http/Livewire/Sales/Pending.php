<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\PendingSale;
use Devzone\Pharmacy\Models\Sale\PendingSaleDetail;

class Pending extends Component
{
    use    WithPagination;


    public $from;
    public $to;


    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-1 month'));
        $this->to = date('Y-m-d');
    }



    public function render()
    {
        $history = PendingSale::from("pending_sales as s")
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->leftJoin('pending_sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id');



            $history = $history->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->to);
            }) ;



        $history = $history->select(
            's.id',
            's.sub_total',
            's.gross_total',

            'u.name as sale_by',
            's.sale_at',




            'p.name as patient_name',
            'p.mr_no',
            'e.name as referred_by'
        )
            ->groupBy('sd.sale_id')
            ->orderBy('s.id', 'desc')->paginate(100);

        return view('pharmacy::livewire.sales.pending', ['history' => $history]);
    }

    public function search()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->resetPage();
    }

    public function delete($id){
        PendingSale::find($id)->delete();
        PendingSaleDetail::where('sale_id',$id)->delete();
        $this->search();
    }
}
