<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use App\Models\Hospital\AdmissionJobDetail;
use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Livewire\Component;

class AdmissionPharmacy extends Component
{
    public function render()
    {
        $admissions = AdmissionJobDetail::from('admission_job_details as ajd')
            ->join('admission_payment_details as apd', function ($q) {
                return $q->on('apd.admission_job_id', '=', 'ajd.id')
                    ->where('apd.medicines','t');
            })
            ->join('admissions as a', 'a.id', '=', 'apd.admission_id')
            ->join('patients as p', 'p.id', '=', 'apd.patient_id')
            ->join('employees as e', 'e.id', '=', 'ajd.doctor_id')
            ->join('procedures as pro', 'pro.id', '=', 'ajd.procedure_id')
            ->where('ajd.medicines_included', 't')
            ->orderBy('a.id','DESC')
            ->select(
                'ajd.admission_id','ajd.procedure_id','ajd.schedule_date', 'ajd.schedule_time',
                'p.name as patient_name', 'e.name as doctor_name', 'pro.name as procedure_name',
                'apd.amount', 'apd.medicines', 'apd.sale_id',
                'a.admission_no', 'a.admission_date', 'a.admission_time', 'a.checkout_date', 'a.checkout_time',
            )
            ->paginate(10);
        return view('pharmacy::livewire.sales.admission-pharmacy',['admissions'=>$admissions]);
    }
}
