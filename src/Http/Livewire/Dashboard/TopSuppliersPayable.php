<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Ams\Models\Ledger;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopSuppliersPayable extends Component
{
    public $data=[];
    public function render()
    {
        $this->search();
        return view('pharmacy::livewire.dashboard.top-supplier-payables');
    }
    public function search(){
        $this->data=Supplier::from('suppliers as s')
            ->join('ledgers as l','l.account_id','=','s.account_id')
            ->where('l.is_approve','t')
            ->groupBy('l.account_id')
            ->select('s.name as supplier',
                DB::raw('sum(l.credit-l.debit) as total'),
            )
            ->orderBy('total','DESC')
            ->limit(5)
            ->get()->toArray();
    }

}