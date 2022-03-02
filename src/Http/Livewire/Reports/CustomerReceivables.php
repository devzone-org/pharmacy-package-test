<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use App\Models\Hospital\Department;
use App\Models\Hospital\Employees\Employee;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Customer;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerReceivables extends Component
{
    use Searchable, WithPagination;

    public $patient_id;
    public $patient_name;
    public $hide_add_patient_searchable = true;

    public function mount()
    {
        $this->search();
    }

    public function render(){

        $report = Customer::from('customers as cus')
            ->join('patients as pat', 'pat.customer_id', '=', 'cus.id')
            ->join('ledgers as ld', 'ld.account_id', '=', 'cus.account_id')
            ->leftjoin('employees as emp', 'emp.id', '=', 'cus.employee_id')
            ->when(!empty($this->patient_id), function ($q) {
                return $q->where('pat.id', $this->patient_id);
            })
            ->groupBy('cus.name')
            ->select('cus.name', 'cus.credit_limit', 'emp.name as care_of', DB::raw('(SUM(ld.debit)-SUM(ld.credit)) as total_receivable'))
            ->paginate(50);

        return view('pharmacy::livewire.reports.customer-receivables' ,['report'=> $report]);
    }

    public function searchPatient()
    {
        $this->searchableOpenModal('patient_id', 'patient_name', 'patient');
    }

    public function resetSearch()
    {
        $this->reset(['patient_id', 'patient_name']);
        $this->search();
    }
    
    public function search()
    {
        $this->resetPage();

//        $this->report = Customer::from('customers as cus')
//            ->join('patients as pat', 'pat.customer_id', '=', 'cus.id')
//            ->join('ledgers as ld', 'ld.account_id', '=', 'cus.account_id')
//            ->leftjoin('employees as emp', 'emp.id', '=', 'cus.employee_id')
//            ->when(!empty($this->patient_id), function ($q) {
//                return $q->where('pat.id', $this->patient_id);
//            })
//            ->groupBy('cus.name')
//            ->select('cus.name', 'cus.credit_limit', 'emp.name as care_of', DB::raw('(SUM(ld.debit)-SUM(ld.credit)) as total_receivable'))
//            ->get()->toArray();
    }

}