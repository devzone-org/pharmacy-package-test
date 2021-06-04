<?php

namespace Devzone\Pharmacy\Http\Livewire\Payments\Supplier;

use Devzone\Pharmacy\Http\Traits\Searchable;
use Devzone\Pharmacy\Models\Payments\SupplierPayment;
use Devzone\Pharmacy\Models\Payments\SupplierPaymentDetail;
use Devzone\Pharmacy\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Add extends Component
{
    use Searchable;

    public $supplier_id;
    public $supplier_name;
    public $pay_from_name;
    public $pay_from;
    public $payment_date;
    public $description;
    public $success;
    public $purchase_orders = [];
    public $selected_orders = [];
    protected $listeners = ['emitSupplierId'];

    protected $rules = [
        'supplier_id' => 'required|integer',
        'pay_from' => 'required|integer',
        'payment_date' => 'required|date',
        'description' => 'nullable|string'
    ];

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
    }


    public function render()
    {
        return view('pharmacy::livewire.payments.supplier.add');
    }

    public function create()
    {
        $this->validate();
        if (!empty($this->selected_orders)) {
            try {
                DB::beginTransaction();

                $id = SupplierPayment::create([
                    'supplier_id' => $this->supplier_id,
                    'description' => $this->description,
                    'pay_from' => $this->pay_from,
                    'payment_date' => $this->payment_date,
                    'added_by' => Auth::user()->id
                ])->id;

                foreach ($this->selected_orders as $o) {
                    SupplierPaymentDetail::create([
                        'supplier_payment_id' => $id,
                        'order_id' => $o
                    ]);
                }
                DB::commit();
                $this->success = 'Record has been added and need for approval.';
                $this->reset(['supplier_id','supplier_name','purchase_orders','selected_orders','description','pay_from','pay_from_name']);

            } catch (\Exception $e) {
                $this->addError('purchase_orders', $e->getMessage());
                DB::rollBack();
            }
        } else {
            $this->addError('purchase_orders', 'You have to select at least one order.');
        }


    }

    public function emitSupplierId()
    {
        $result = Purchase::from('purchases as p')
            ->join('purchase_receives as pr', 'pr.purchase_id', '=', 'p.id')
            ->where('p.supplier_id', $this->supplier_id)
            ->where('p.status', 'received')
            ->where('p.is_paid', 'f')
            ->select(
                'p.id', 'p.supplier_invoice', 'p.grn_no', 'p.delivery_date', DB::raw('sum(total_cost) as total_cost')
            )->orderBy('p.delivery_date')->groupBy('pr.purchase_id')->get();
        if ($result->isEmpty()) {
            $this->purchase_orders = [];
        } else {
            $this->purchase_orders = $result->toArray();
        }
    }
}