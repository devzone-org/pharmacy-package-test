<?php


namespace Devzone\Pharmacy\Http\Livewire\Sales;


use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\InventoryLedger;
use Devzone\Pharmacy\Models\ProductInventory;
use Devzone\Pharmacy\Models\Sale\Sale;
use Devzone\Pharmacy\Models\Sale\SaleDetail;
use Devzone\Pharmacy\Models\Sale\SaleRefund;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Refund extends Component
{
    use Searchable;

    public $product_id;
    public $product_name;
    public $discount = 0;
    public $received = 0;
    public $payable = 0;
    public $referred_by_id;
    public $referred_by_name;
    public $patient_id;
    public $patient_name;
    public $remarks;
    public $success;
    public $error;
    public $old_sales = [];
    public $sales = [];

    public $refunds = [];
    public $sale_id;
    protected $listeners = ['openSearch', 'emitProductId', 'emitPatientId', 'emitReferredById', 'saleComplete'];

    public function mount($primary_id)
    {
        $this->sale_id = $primary_id;

        $this->searchable_emit_only = true;
        $this->old_sales = Sale::from('sales as s')->join('sale_details as sd', 'sd.sale_id', '=', 's.id')
            ->join('product_inventories as pi','pi.id','=','sd.product_inventory_id')
            ->leftJoin('employees as e', 'e.id', '=', 's.referred_by')
            ->leftJoin('patients as p', 'p.id', '=', 's.patient_id')
            ->join('products as pr', 'pr.id', '=', 'sd.product_id')
            ->where('s.id', $this->sale_id)
            ->select('sd.*', 'pr.name as item', 's.remarks', 's.receive_amount', 's.payable_amount', 's.sub_total', 's.gross_total'
              ,'pi.po_id'  , 's.patient_id', 's.referred_by', 'e.name as referred_by_name', 'p.name as patient_name')
            ->get()->toArray();


        foreach ($this->old_sales as $s) {
            $check = SaleRefund::where('sale_id', $s['sale_id'])->where('sale_detail_id', $s['id'])->first();
            if (!empty($check)) {
                $s['qty'] = $check['refund_qty'];
                $s['s_qty'] = $check['qty'];
                $s['restrict'] = true;
                $s['total'] = $s['qty'] * $s['retail_price'];
                $s['total_after_disc'] = $s['total'];
                if ($s['disc'] > 0) {
                    $discount = round(($s['disc'] / 100) * $s['total'], 2);
                    $s['total_after_disc'] = $s['total'] - $discount;
                }
                $this->refunds[] = $s;
            }
        }
    }

    public function emitReferredById()
    {
        $data = $this->searchable_data[$this->highlight_index];
        $this->referred_by_id = $data['id'];
        $this->referred_by_name = $data['name'];
        $this->searchableReset();
    }

    public function emitPatientId()
    {
        $data = $this->searchable_data[$this->highlight_index];
        $this->patient_id = $data['id'];
        $this->patient_name = $data['mr_no'] . ' - ' . $data['name'];
        $this->searchableReset();
    }

    public function emitProductId()
    {
        $data = $this->searchable_data[$this->highlight_index];
        if ($data['qty'] > 0) {
            $check = collect($this->sales)->where('id', $data['id'])->all();

            if (empty($check)) {
                $data['s_qty'] = 1;
                $data['disc'] = 0;
                $data['total'] = $data['retail_price'];
                $data['total_after_disc'] = $data['retail_price'];
                $this->sales[] = $data;
            } else {
                $key = array_keys($check)[0];
                if ($check[$key]['s_qty'] < $check[$key]['qty']) {
                    $qty = $this->sales[$key]['s_qty'];
                    $this->sales[$key]['s_qty'] = $qty + 1;
                    $this->sales[$key]['total'] = $this->sales[$key]['retail_price'] * $this->sales[$key]['s_qty'];
                    $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'];
                }
            }
        }
    }

    public function openSearch()
    {
        $this->searchableOpenModal('product_id', 'product_name', 'inventory');
    }

    public function render()
    {
        return view('pharmacy::livewire.sales.refund');
    }


    public function updated($name, $value)
    {
        $array = explode(".", $name);
        if ($array[0] == 'sales') {
            if (empty($value) || !is_numeric($value)) {
                $this->sales[$array[1]][$array[2]] = 0;
            }
            if (in_array($array[2], ['s_qty', 'retail_price', 'disc'])) {
                if ($this->sales[$array[1]]['s_qty'] > $this->sales[$array[1]]['qty']) {
                    $this->sales[$array[1]]['s_qty'] = $this->sales[$array[1]]['qty'];
                } else if ($this->sales[$array[1]]['s_qty'] < 0) {
                    $this->sales[$array[1]]['s_qty'] = 0;
                }
                $this->sales[$array[1]]['total'] = round($this->sales[$array[1]]['s_qty'] * $this->sales[$array[1]]['retail_price'], 2);

                if ($this->sales[$array[1]]['disc'] >= 0 || $this->sales[$array[1]]['disc'] <= 100) {
                    $discount = round(($this->sales[$array[1]]['disc'] / 100) * $this->sales[$array[1]]['total'], 2);
                    $this->sales[$array[1]]['total_after_disc'] = $this->sales[$array[1]]['total'] - $discount;
                }
            }
        }

        if ($array[0] == 'refunds') {
            if (empty($value) || !is_numeric($value)) {
                $this->refunds[$array[1]][$array[2]] = 0;
            }

            if (in_array($array[2], ['qty'])) {
                if ($this->refunds[$array[1]]['qty'] > $this->refunds[$array[1]]['s_qty']) {
                    $this->refunds[$array[1]]['qty'] = $this->refunds[$array[1]]['s_qty'];
                } else if ($this->refunds[$array[1]]['qty'] < 0) {
                    $this->refunds[$array[1]]['qty'] = 0;
                }
                $this->refunds[$array[1]]['total'] = round($this->refunds[$array[1]]['qty'] * $this->refunds[$array[1]]['retail_price'], 2);


                if ($this->refunds[$array[1]]['disc'] >= 0 || $this->refunds[$array[1]]['disc'] <= 100) {
                    $discount = round(($this->refunds[$array[1]]['disc'] / 100) * $this->refunds[$array[1]]['total'], 2);
                    $this->refunds[$array[1]]['total_after_disc'] = $this->refunds[$array[1]]['total'] - $discount;
                }

            }
        }

    }


    public function updatedDiscount($value)
    {
        if (empty($value) || !is_numeric($value)) {
            $this->discount = 0;
            $value = 0;
        }

        foreach ($this->sales as $key => $s) {
            if ($value >= 0 || $value <= 100) {
                $discount = round(($value / 100) * $this->sales[$key]['total'], 2);
                $this->sales[$key]['total_after_disc'] = $this->sales[$key]['total'] - $discount;
                $this->sales[$key]['disc'] = $value;
            }
        }

    }

    public function updatedReceived($value)
    {
        if (empty($value) || !is_numeric($value)) {
            $value = 0;
        }
        $this->payable = $value - collect($this->sales)->sum('total_after_disc');
    }

    public function removeEntry($key)
    {
        unset($this->sales[$key]);
    }

    public function removeRefundEntry($key)
    {
        unset($this->refunds[$key]);
    }

    public function refundEntry($key)
    {
        $old_sale = $this->old_sales[$key];

        $data = collect($this->refunds)->where('id', $old_sale['id'])->firstWhere('sale_id', $this->sale_id);

        if ($old_sale['qty'] > $data['qty']) {
            $old_sale['s_qty'] = $old_sale['qty'];
            $old_sale['qty'] = $old_sale['qty'] - $data['qty'];

            $old_sale['total'] = $old_sale['qty'] * $old_sale['retail_price'];
            $old_sale['total_after_disc'] = $old_sale['total'];
            if ($old_sale['disc'] > 0) {
                $discount = round(($old_sale['disc'] / 100) * $old_sale['total'], 2);
                $old_sale['total_after_disc'] = $old_sale['total'] - $discount;
            }
            $this->refunds[] = $old_sale;
        }
    }

    public function saleComplete()
    {
        try {
            if (empty($this->refunds)) {
                throw new \Exception('Refund invoice is empty.');
            }

            DB::beginTransaction();
            $refund = false;

            foreach ($this->refunds as $r) {
                if (isset($r['restrict'])) {
                    continue;
                }
                $refund_qty = SaleRefund::where('sale_id', $r['sale_id'])->where('sale_detail_id', $r['id'])
                    ->get();
                if ($refund_qty->isEmpty()) {
                    $refund_qty = 0;
                } else {
                    $refund_qty = $refund_qty->first()->refund_qty;
                }
                $detail = SaleDetail::find($r['id']);
                if ($detail['qty'] >= $refund_qty + $r['qty']) {
                    SaleRefund::updateOrCreate([
                        'sale_id' => $r['sale_id'],
                        'sale_detail_id' => $r['id']
                    ], [
                        'refund_qty' => $refund_qty + $r['qty']
                    ]);
                    InventoryLedger::create([
                        'product_id' => $r['product_id'],
                        'order_id' => $r['po_id'],
                        'increase' => $r['qty'],
                        'description' => "Refund on dated " . date('d M, Y') .
                            " against receipt #" . $this->sale_id
                    ]);
                } else {
                    throw new \Exception('ERROR: Refund qty is more than sale qty.');
                }
                $refund = true;
            }
            if ($refund) {
                Sale::find($this->sale_id)->update([
                   'is_refund' => 't'
                ]);
            }
            if (!empty($this->sales)) {
                foreach ($this->sales as $s) {
                    $inv = ProductInventory::where('product_id', $s['product_id'])
                        ->where('supply_price', $s['supply_price'])
                        ->where('qty', '>', 0)->get();

                    if ($inv->sum('qty') < $s['s_qty']) {
                        throw new \Exception('System does not have much inventory for the item ' . $s['item']);
                    }

                    $sale_qty = $s['s_qty'];
                    foreach ($inv as $i) {
                        if ($sale_qty > 0) {
                            $dec = 0;
                            $product_inv = ProductInventory::find($i->id);
                            if ($sale_qty <= $product_inv->qty) {
                                $dec = $sale_qty;
                                $product_inv->decrement('qty', $sale_qty);
                                InventoryLedger::create([
                                    'product_id' => $product_inv->product_id,
                                    'order_id' => $product_inv->po_id,
                                    'decrease' => $this->sale_qty,
                                    'description' => "Sale on dated " . date('d M, Y') .
                                        " against receipt #" . $this->sale_id
                                ]);
                                $sale_qty = 0;

                            }
                            if ($sale_qty > $product_inv->qty) {
                                $dec = $product_inv->qty;
                                $product_inv->decrement('qty', $dec);
                                InventoryLedger::create([
                                    'product_id' => $product_inv->product_id,
                                    'order_id' => $product_inv->po_id,
                                    'decrease' => $product_inv->qty,
                                    'description' => "Sale on dated " . date('d M, Y') .
                                        " against receipt #" . $this->sale_id
                                ]);
                                $sale_qty = $sale_qty - $dec;
                            }
                            $total = $s['retail_price'] * $dec;

                            $discount = round(($s['disc'] / 100) * $total, 2);
                            $after_total = $total - $discount;
                            SaleDetail::create([
                                'sale_id' => $this->sale_id,
                                'product_id' => $s['product_id'],
                                'product_inventory_id' => $i->id,
                                'qty' => $dec,
                                'supply_price' => $product_inv->supply_price,
                                'retail_price' => $s['retail_price'],
                                'total' => $total,
                                'disc' => $s['disc'],
                                'total_after_disc' => $after_total,
                            ]);

                        }
                    }
                }
            }

            $this->searchableReset();
            $this->success = 'Refund has been complete with receipt #' . $this->sale_id;
            DB::commit();
            return redirect()->to('pharmacy/sales/refund/' . $this->sale_id);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            DB::rollBack();
        }

    }

    public function resetAll()
    {
        $this->reset(['sales', 'referred_by_id', 'referred_by_name', 'success', 'patient_id', 'patient_name',
            'payable', 'received', 'remarks', 'discount', 'error']);
    }
}
