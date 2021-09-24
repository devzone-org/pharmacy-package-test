<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use Searchable, WithPagination;

    public $receipt;
    public $from;
    public $to;

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-1 month'));
        $this->to = date('Y-m-d');
    }

    public function render()
    {
        $history = Sale::from("sales as s")
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->whereNull('admission_id')
            ->whereNull('procedure_id');

        if (!empty($this->receipt)) {
            $history = $history->where('s.id', $this->receipt);
        } else {
            $history = $history->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->to);
            });
        }

        $history = $history->select(
            's.id',
            's.sub_total',
            's.gross_total',
            's.refunded_id',
            'u.name as sale_by',
            's.sale_at',
            's.is_refund',
            's.receive_amount',
            's.payable_amount',
            's.refunded_id',
            'p.name as patient_name',
            'e.name as referred_by'
        )->orderBy('s.id', 'desc')->paginate(50);

        return view('pharmacy::livewire.sales.history', ['history' => $history]);
    }

    public function search()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->reset(['receipt']);
        $this->resetPage();

    }

}
