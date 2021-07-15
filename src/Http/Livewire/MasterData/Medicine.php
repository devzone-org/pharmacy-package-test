<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use App\Models\Hospital\Hospital;
use Livewire\Component;

class Medicine extends Component
{
    public $type;

    public function mount()
    {

        $hospital = Hospital::first();
        $this->type = $hospital['transfer_medicine'];
    }

    public function render()
    {
        return view('pharmacy::livewire.master-data.medicine');
    }

    public function updatedType($value)
    {
        Hospital::find(1)->update([
            'transfer_medicine' => $value
        ]);
    }


}
