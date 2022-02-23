<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\ExpiryAdjustmentLog;
use Devzone\Pharmacy\Models\ProductInventory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ExpiryAdjustment extends Component
{
    use Searchable;

    public $error;
    public $adjustments = [];
    public $show_model = false;
    public $remarks;
    protected $listeners = ['emitProductId'];

    protected $rules = [
        'adjustments.*.new_expiry' => 'required|date'
    ];

    protected $validationAttributes = [
        'adjustments.*.new_expiry' => 'New Expiry'
    ];

    public function mount()
    {
        $this->searchable_emit_only = true;
    }

    public function render()
    {
        return view('pharmacy::livewire.purchases.expiry-adjustment');
    }

    public function removeItem($key)
    {
        unset($this->adjustments[$key]);
    }

    public function updated($name, $value)
    {
        $this->resetErrorBag();
        $array = explode('.', $name);
        if (count($array) == 3) {
            if ($array[0] == 'adjustments') {
                if ($array[2] == 'new_expiry' && $value == $this->adjustments[$array[1]]['expiry']) {
                    $this->adjustments[$array[1]]['new_expiry'] = null;
                    $this->addError('error', 'New expiry cannot be equal to old expiry!');
                }
            }
        }
    }

    public function emitProductId()
    {
        $data = $this->searchable_data[$this->highlight_index];
        $pi = ProductInventory::where('product_id', $data['id'])->get(['id', 'product_id', 'expiry', 'po_id'])->toArray();


        if (!empty($pi)) {
            foreach ($pi as $key => $p) {
                $check = collect($this->adjustments)->where('id', $p['id'])->all();
                if (empty($check)) {
                    $p['new_expiry'] = null;
                    $p['item'] = $data['item'];
                    $this->adjustments[] = $p;
                }
            }

        }

    }

    public function proceed()
    {
        $this->validate();
        $this->show_model = true;
    }

    public function confirm()
    {
        try {
            DB::beginTransaction();
            $description = "";
            $this->error = "";

            if (empty($this->remarks)) {
                $this->error = "Remarks are required.";
                throw new \Exception();
            }

            foreach ($this->adjustments as $a) {

                $inventory = ProductInventory::find($a['id']);

                if (empty($inventory)) {
                    throw new \Exception($a['item'] . ' inventory record for PO# ' . $a['po_id'] . ' not found.');
                } else {
                    $log = ExpiryAdjustmentLog::create([
                        'product_inventory_id' => $a['id'],
                        'old_expiry' => $a['expiry'],
                        'new_expiry' => $a['new_expiry'],
                        'created_by' => auth()->id(),
                        'remarks' => $this->remarks
                    ]);

                    $inventory = $inventory->update([
                        'expiry' => $a['new_expiry']
                    ]);

                }
            }


            $this->reset(['adjustments', 'error', 'show_model', 'remarks']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if (!empty($e->getMessage())) {
                $this->addError('exception', $e->getMessage());
            }
        }
    }
}
