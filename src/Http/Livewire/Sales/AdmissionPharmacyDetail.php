<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use App\Models\Hospital\AdmissionJobDetail;
use App\Models\Hospital\Admission;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Devzone\Pharmacy\Models\Sale\SaleRefundDetail;

use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class AdmissionPharmacyDetail extends Component
{
    use Searchable, WithPagination;

    public $admission_details;

    public $sale_id;
    public $admission_id;
    public $procedure_id;
    public $doctor_id;


    public function mount(Request $request, $sale_id)
    {
        $this->sale_id = $sale_id;
        $this->admission_id = $request->get('admission_id');
        $this->procedure_id = $request->get('procedure_id');
        $this->doctor_id = $request->get('doctor_id');

        $this->admission_details = AdmissionJobDetail::from('admission_job_details as ajd')
            ->join('admissions as a', 'a.id', '=', 'ajd.admission_id')
            ->join('patients as p', 'p.id', '=', 'a.patient_id')
            ->join('employees as e', 'e.id', '=', 'ajd.doctor_id')
            ->join('procedures as pro', 'pro.id', '=', 'ajd.procedure_id')
            ->where('ajd.admission_id', $this->admission_id)
            ->where('ajd.procedure_id', $this->procedure_id)
            ->select('a.admission_no', 'a.admission_date', 'a.admission_time', 'a.status', 'a.checkout_date', 'a.checkout_time',
                'p.name as patient_name', 'e.name as doctor_name', 'pro.name as procedure_name'
            )->get()->first();
    }


    public function render()
    {
//        $admissions = AdmissionJobDetail::from('admission_job_details as ajd')
//            ->join('admission_payment_details as apd', function ($q) {
//                return $q->on('apd.admission_job_id', '=', 'ajd.id')
//                    ->where('apd.medicines', 't');
//            })
//            ->join('admissions as a', 'a.id', '=', 'apd.admission_id')
//            ->join('patients as p', 'p.id', '=', 'apd.patient_id')
//            ->join('employees as e', 'e.id', '=', 'ajd.doctor_id')
//            ->join('procedures as pro', 'pro.id', '=', 'ajd.procedure_id')
//            ->leftJoin('sales as s', function ($q) {
//                return $q->on('s.admission_id', '=', 'apd.admission_id')
//                    ->on('s.procedure_id', '=', 'apd.procedure_id');
//            })
//            ->leftJoin('users as u', 'u.id', '=', 's.sale_by')
//            ->when(!empty($this->admission_no), function ($q) {
//                return $q->where('a.admission_no', 'like', '%' . $this->admission_no . '%');
//            })
//            ->when(!empty($this->patient), function ($q) {
//                return $q->where('p.name', 'like', '%' . $this->patient . '%');
//            })
//            ->when(!empty($this->from) && !empty($this->to), function ($q) {
//                return $q->where('a.admission_date', '>=', $this->from)
//                    ->where('a.admission_date', '<=', $this->to);
//            })
//            ->when(!empty($this->from) && empty($this->to), function ($q) {
//                return $q->whereDate('a.admission_date', $this->from);
//            })
//            ->when(!empty($this->to) && empty($this->from), function ($q) {
//                return $q->whereDate('a.admission_date', $this->to);
//            })
//            ->where('ajd.medicines_included', 't')
//            ->orderBy('a.id', 'DESC')
//            ->select(
//                'ajd.admission_id', 'ajd.procedure_id', 'ajd.schedule_date', 'ajd.schedule_time',
//                'p.name as patient_name', 'p.mr_no as patient_mr', 'e.name as doctor_name', 'pro.name as procedure_name',
//                's.gross_total as amount', 'apd.medicines', 'apd.sale_id',
//                'a.admission_no', 'a.admission_date', 'a.admission_time', 'a.checkout_date', 'a.checkout_time',
//                's.sale_at', 'u.name as sold_By', 'ajd.doctor_id'
//            )
//            ->paginate(10);


        $history = Sale::from("sales as s")
            ->leftjoin('users as u', 'u.id', '=', 's.sale_by')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->where('s.admission_id', $this->admission_id)
            ->where('s.procedure_id', $this->procedure_id)
            ->select(
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

        return view('pharmacy::livewire.sales.admission-pharmacy-detail', ['history' => $history]);
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
