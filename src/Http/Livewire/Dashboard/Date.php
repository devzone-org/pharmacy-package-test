<?php

namespace Devzone\Pharmacy\Http\Livewire\Dashboard;

use Illuminate\Support\Carbon;
use Livewire\Component;

class Date extends Component
{
    public $date;
    public $type;

    public function mount(){
        $this->display_date=date('d M Y');
        $this->type='week';
        $this->date=date('Y-m-d');
    }
    public function render()
    {
        return view('pharmacy::livewire.dashboard.date');
    }


}