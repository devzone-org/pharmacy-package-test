<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use App\Models\Hospital\AdmissionJobDetail;
use Carbon\Carbon;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Livewire\Component;
use Livewire\WithPagination;

class AdmissionPharmacy extends Component
{
    use Searchable, WithPagination;
    public $admission_no;
    public $patients;
    public $patient;
    public $from;
    public $to;



    public function render()
    {
        $admissions = AdmissionJobDetail::from('admission_job_details as ajd')
            ->join('admission_payment_details as apd', function ($q) {
                return $q->on('apd.admission_job_id', '=', 'ajd.id')
                    ->where('apd.medicines', 't');
            })
            ->join('admissions as a', 'a.id', '=', 'apd.admission_id')
            ->join('patients as p', 'p.id', '=', 'apd.patient_id')
            ->join('employees as e', 'e.id', '=', 'ajd.doctor_id')
            ->join('procedures as pro', 'pro.id', '=', 'ajd.procedure_id')

            ->when(!empty($this->admission_no), function ($q) {
                return $q->where('a.admission_no', 'like', '%' . $this->admission_no . '%');
            })
            ->when(!empty($this->patient), function ($q) {
                return $q->where('p.name', 'like', '%' . $this->patient . '%');
            })
            ->when(!empty($this->from) && !empty($this->to), function ($q) {
                return $q->where('a.admission_date', '>=', $this->formatDate($this->from))
                    ->where('a.admission_date', '<=', $this->formatDate($this->to));
            })
            ->when(!empty($this->from) && empty($this->to), function ($q) {
                return $q->whereDate('a.admission_date', $this->formatDate($this->from));
            })
            ->when(!empty($this->to) && empty($this->from), function ($q) {
                return $q->whereDate('a.admission_date', $this->formatDate($this->to));
            })
            ->where('ajd.medicines_included', 't')
            ->orderBy('a.id', 'DESC')
            ->select(
                'ajd.admission_id', 'ajd.procedure_id', 'ajd.schedule_date', 'ajd.schedule_time',
                'p.name as patient_name','p.mr_no as patient_mr', 'e.name as doctor_name', 'pro.name as procedure_name',
                'apd.medicines', 'apd.sale_id',
                'a.admission_no', 'a.admission_date', 'a.admission_time', 'a.checkout_date', 'a.checkout_time',
                'ajd.doctor_id'
            )
            ->paginate(20);
        return view('pharmacy::livewire.sales.admission-pharmacy', ['admissions' => $admissions]);
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d M Y', $date)
            ->format('Y-m-d');
    }


    public function search()
    {
        $this->resetPage();
    }
    public function resetSearch(){
        $this->reset('admission_no','from','to','patient');
        $this->resetPage();
    }
}
