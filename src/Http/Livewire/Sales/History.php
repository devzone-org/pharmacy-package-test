<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Devzone\Pharmacy\Models\Sale\SaleRefundDetail;
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
        $details = [];
        if (!empty($this->product_id)) {
            $details = SaleDetail::where('product_id', $this->product_id)->get()->pluck('sale_id')->toArray();
            $refund_details = SaleRefundDetail::where('product_id', $this->product_id)->get()->unique('refunded_id')->pluck('refunded_id')->toArray();
            $details = array_unique(array_merge($details, $refund_details));
        }

        $history = Sale::from("sales as s")
            ->leftjoin('users as u', 'u.id', '=', 's.sale_by')
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
            })->when(!empty($details), function ($q) use ($details) {
                return $q->whereIn('s.id', $details);
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
            's.rounded_inc',
            's.rounded_dec',

            's.is_credit',
            's.is_paid',
            's.on_account',
            'p.name as patient_name',
            'p.mr_no',
            'e.name as referred_by'
        )
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
