<?php


namespace Devzone\Pharmacy\Http\Livewire\Purchases;


use Devzone\Ams\Helper\GeneralJournal;
use Devzone\Ams\Helper\Voucher;
use Devzone\Ams\Models\ChartOfAccount;
use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\ProductInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockAdjustment extends Component
{
    use Searchable;

    public $error;
    public $adjustments = [];
    public $show_model = false;
    public $remarks;
    protected $listeners = ['emitProductId'];

    public function mount()
    {
        $this->searchable_emit_only = true;
    }

    public function render()
    {
        return view('pharmacy::livewire.purchases.stock-adjustment');
    }

    public function removeItem($key)
    {
        unset($this->adjustments[$key]);
    }

    public function updated($name, $value)
    {
        $array = explode('.', $name);
        if (count($array) == 3) {
            if ($array[0] == 'adjustments') {
                if ($array[2] == 'indicator' && $value == 'd' && $this->adjustments[$array[1]]['qty'] == 0) {
                    $this->adjustments[$array[1]]['indicator'] = 'i';
                } elseif ($array[2] == 'a_qty') {
                    if (empty($value)) {
                        $this->adjustments[$array[1]][$array[2]] = 1;
                    } elseif ($this->adjustments[$array[1]]['indicator'] == 'd' && $this->adjustments[$array[1]]['qty'] < $value) {
                        $this->adjustments[$array[1]][$array[2]] = 1;
                    }
                }
            }
        }
    }

    public function emitProductId()
    {
        $data = $this->searchable_data[$this->highlight_index];
        $check = collect($this->adjustments)->where('id', $data['id'])->all();

        if (empty($check)) {
            $data['a_qty'] = 1;
            $data['indicator'] = 'i';
            $this->adjustments[] = $data;
        } else {
            $key = array_keys($check)[0];
            $qty = $this->adjustments[$key]['a_qty'];
            $this->adjustments[$key]['a_qty'] = $qty + 1;
        }

    }

    public function proceed()
    {
        $this->show_model = true;
    }

    public function confirm()
    {
        $lock=Cache::lock('stock.adjustment.add',30);
        try {
            if ($lock->get()) {
                DB::beginTransaction();
                $decrease = 0;
                $increase = 0;
                $description = "";
                $this->error = "";
                if (empty($this->remarks)) {
                    throw new \Exception('Remarks are required.');
                }
                $vno = Voucher::instance()->voucher()->get();
                foreach ($this->adjustments as $a) {

                    $inventory = ProductInventory::where('product_id', $a['id'])
                        ->when(!empty($a['expiry']), function ($q) use ($a) {
                            return $q->where('expiry', $a['expiry']);
                        })->first();

                    if (empty($inventory)) {
                        $product = Product::find($a['id']);
                        if (empty($product)) {
                            throw new \Exception($a['item'] . ' not found.');
                        }
                        $inventory = ProductInventory::create([
                            'product_id' => $a['id'],
                            'qty' => 0,
                            'retail_price' => $product['retail_price'] / $product['packing'],
                            'supply_price' => $product['cost_of_price'] / $product['packing'],
                        ]);

                    }

                    $find = ProductInventory::find($inventory['id']);
                    \Devzone\Pharmacy\Models\StockAdjustment::create([
                        'product_id' => $find['product_id'],
                        'indicator' => $a['indicator'],
                        'qty' => $a['a_qty'],
                        'current_qty' => $a['qty'] ?? 0,
                        'remarks' => $this->remarks,
                        'added_by' => Auth::id(),
                        'voucher_no' => $vno
                    ]);

                    if ($a['indicator'] == 'i') {
                        $increase = $increase + ($a['a_qty'] * $find['supply_price']);
                        $description .= " [Increase - " . $a['item'] . " {$find['supply_price']} X {$a['a_qty']} = PKR" . ($a['a_qty'] * $find['supply_price']) . "/- ]";
                        $find->increment('qty', $a['a_qty']);
                    } else {
                        $decrease = $decrease + ($a['a_qty'] * $find['supply_price']);
                        $description .= " [Decrease - " . $a['item'] . " {$find['supply_price']} X {$a['a_qty']} = PKR" . ($a['a_qty'] * $find['supply_price']) . "/- ]";
                        $find->decrement('qty', $a['a_qty']);
                    }

                    InventoryLedger::create([
                        'product_id' => $find['product_id'],
                        'order_id' => $find['po_id'],
                        'increase' => $a['indicator'] == 'i' ? $a['a_qty'] : 0,
                        'decrease' => $a['indicator'] == 'd' ? $a['a_qty'] : 0,
                        'type' => 'adjustment',
                        'description' => $description
                    ]);
                }


                $inventory_account = ChartOfAccount::where('reference', 'pharmacy-inventory-5')->first();
                $cos_account = ChartOfAccount::where('reference', 'cost-of-sales-pharmacy-5')->first();


                $description = "Gross decrease inventory PKR" . $decrease . "/- " . "Gross increase inventory PKR" . $increase . "/- " . $description . ". by " . Auth::user()->name . " @ " . date('d M, Y h:i A');

                if ($decrease > 0) {
                    GeneralJournal::instance()->account($inventory_account['id'])->credit($decrease)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                    GeneralJournal::instance()->account($cos_account['id'])->debit($decrease)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }
                if ($increase > 0) {
                    GeneralJournal::instance()->account($cos_account['id'])->credit($increase)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                    GeneralJournal::instance()->account($inventory_account['id'])->debit($increase)->voucherNo($vno)
                        ->date(date('Y-m-d'))->approve()->description($description)->execute();
                }

                $this->reset(['adjustments', 'error', 'show_model', 'remarks']);
                DB::commit();
            }
            optional($lock)->release();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            DB::rollBack();
            optional($lock)->release();
        }
    }
}
