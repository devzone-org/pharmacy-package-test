<?php


namespace Devzone\Pharmacy\Http\Livewire\Reports;

use App\Models\Hospital\Employees\Employee;
use Devzone\Pharmacy\Models\Customer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;


class CustomerReceivables extends Component
{
    use WithPagination;

    public $per_page;
    public $all_employees = [];
    public $care_of;
    public $customer_name;
    public $customer_mrn;
    public $report = [];
    public $date_range = false;

    public function mount()
    {
        $this->all_employees = Employee::get(['id', 'name'])->toArray();
        $this->per_page = 20;
        $this->search();
    }

    public function render()
    {
        return view('pharmacy::livewire.reports.customer-receivables');
    }

    public function search()
    {
        $this->report = Customer::from('customers as c')
            ->join('ledgers as lg', 'lg.account_id', '=', 'c.account_id')
            ->leftJoin('employees as e', 'e.id', '=', 'c.employee_id')
            ->when(!empty($this->care_of), function ($q){
                return $q->where('c.employee_id', $this->care_of);
            })
            ->when(!empty($this->customer_name), function ($q){
                return $q->where('c.name', 'LIKE', '%' . $this->customer_name . '%');
            })
            ->when(!empty($this->customer_mrn), function ($q){
                return $q->where('c.name', 'LIKE', '%' . $this->customer_mrn . '%');
            })
            ->whereNotNull('lg.approved_at')
            ->groupBy('lg.account_id')
            ->select('c.name', 'e.name as care_of', DB::raw('sum(lg.debit-lg.credit) as rec'))->get();
//            ->paginate($this->per_page);

//        dd($this->report);


    }

    public function resetSearch()
    {
        $this->reset('customer_name', 'customer_mrn', 'care_of');
        $this->per_page = 20;
        $this->search();
    }

}