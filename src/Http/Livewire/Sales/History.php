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
    public $patient_id;
    public $patient_name;
    public $type;
    public $product_id;
    public $product_name;

    public function mount()
    {
        $this->from = date('Y-m-d', strtotime('-1 month'));
        $this->to = date('Y-m-d');
    }

    public function searchPatient()
    {
        $this->searchableOpenModal('patient_id', 'patient_name', 'patient');
    }

    public function render()
    {
        $history = Sale::from("sales as s")
            ->join('users as u', 'u.id', '=', 's.sale_by')
            ->leftJoin('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->whereNull('s.admission_id')
            ->whereNull('s.procedure_id');

        if (!empty($this->receipt)) {
            $history = $history->where('s.id', $this->receipt);
        } else {
            $history = $history->when(!empty($this->from), function ($q) {
                return $q->whereDate('s.sale_at', '>=', $this->from);
            })->when(!empty($this->to), function ($q) {
                return $q->whereDate('s.sale_at', '<=', $this->to);
            })->when(!empty($this->patient_id), function ($q) {
                return $q->where('s.patient_id', $this->patient_id);
            })->when(!empty($this->product_id), function ($q) {
                return $q->where('sd.product_id', $this->product_id);
            })
                ->when(!empty($this->type), function ($q) {
                if ($this->type == 'sale') {
                    return $q->whereNull('s.refunded_id')->where('s.is_credit', 'f');
                }
                if ($this->type == 'credit') {
                    return $q->whereNull('s.refunded_id')->where('s.is_credit', 't');
                }
                if ($this->type == 'refund') {
                    return $q->where('s.refunded_id', '>', 0);
                }
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

            's.is_credit',
            's.is_paid',
            's.on_account',
            'p.name as patient_name',
            'p.mr_no',
            'e.name as referred_by'
        )
            ->groupBy('sd.sale_id')
            ->orderBy('s.id', 'desc')->paginate(100);

        return view('pharmacy::livewire.sales.history', ['history' => $history]);
    }

    public function search()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->reset(['receipt', 'type', 'patient_id', 'patient_name', 'product_id', 'product_name']);
        $this->resetPage();

    }

}
