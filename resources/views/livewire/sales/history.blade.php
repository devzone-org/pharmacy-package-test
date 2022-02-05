<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Receipt #</label>
                    <input type="text" wire:model.defer="receipt" id="receipt" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Product</label>
                    <input wire:model="product_name" readonly
                           wire:click="searchableOpenModal('product_id', 'product_name', 'product')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Patient</label>
                    <input type="text" readonly wire:model.defer="patient_name" wire:click="searchPatient"
                           autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                    <input type="date" wire:model.defer="from" id="from" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                    <input type="date" wire:model.defer="to" id="to" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select type="text" wire:model.defer="type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value=""></option>
                        <option value="sale">Normal Sale</option>
                        <option value="credit">Credit Sale</option>
                        <option value="refund">Refunded</option>
                    </select>
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search" wire:loading.attr="disabled"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <div wire:loading wire:target="search">
                            Searching ...
                        </div>
                        <div wire:loading.remove wire:target="search">
                            Search
                        </div>
                    </button>

                    <button type="button" wire:click="resetSearch" wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Sale History</h3>
                                <a href="{{ url("pharmacy/sales/add") }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Sale
                                </a>
                            </div>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    #
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Status
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Receipt #
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Sale at
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Patient
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Referred by
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Sub Total
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Discount
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Net Sale
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Sale Returns
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Cash / (Refund)
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Credit
                                </th>


                                <th scope="col" class="relative px-3 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($history as $h)
                                <tr>
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration + ( $history->firstItem() - 1)   }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if(empty($h->refunded_id))
                                            @if(!empty($h->on_account) && $h->on_account > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                On Credit
                                            </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                On Cash
                                            </span>
                                            @endif

                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Refund
                                            </span>
                                        @endif
                                        <br>
                                        @if($h->is_paid=='t')
                                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Paid
                                            </span>
                                        @else
                                            @if(empty($h->refunded_id))
                                                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Not Paid
                                            </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $h->id }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ date('d M, y h:i A',strtotime($h->sale_at)) }} <br>
                                        {{ $h->sale_by }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if(!empty($h->patient_name))
                                            {{ $h->patient_name }}
                                            <br>
                                            {{ $h->mr_no }}
                                        @else
                                            Walk in
                                        @endif

                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $h->referred_by }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->sub_total,2) }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->sub_total - $h->gross_total,2) }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-500">
                                        {{ number_format($h->gross_total,2) }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-500">
                                        @php
                                            $refunded = 0;
                                        @endphp
                                        @if(!empty($h->refunded_id))
                                            @php
                                                $total_refund = \Devzone\Pharmacy\Models\Sale\SaleRefundDetail::from('sale_refund_details as sr')
                                                         ->join('sale_details as sd','sd.id','=','sr.sale_detail_id')
                                                         ->where('sr.sale_id',$h->refunded_id)
                                                         ->where('sr.refunded_id',$h->id)
                                                         ->select(\Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as refund'))
                                             ->first();

                                             $refunded = $total_refund['refund'];
                                            @endphp
                                            {{ number_format($refunded,2) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    @php
                                        $val = 0;
                                        $after_roundoff = 0;
                                        if (!empty($h->rounded_inc)){
                                            $val = $h->rounded_inc;
                                        }elseif (!empty($h->rounded_dec)){
                                            $val = -1 * $h->rounded_dec;
                                        }
                                        $after_roundoff = ($h->gross_total + $val);
                                    @endphp

                                    <td class="px-3 py-3 text-sm text-gray-500">
                                        @if($refunded - $h->gross_total > 0)
                                            @if($h->is_credit == 'f')
                                                ({{ number_format(abs($refunded - $after_roundoff),2) }})
                                            @elseif($h->is_credit != 'f')
                                                ({{ number_format(abs($refunded - $h->gross_total),2) }})
                                            @endif

                                        @else
                                            @if($h->is_credit == 'f')
                                                {{ number_format(abs($refunded - $after_roundoff),2) }}
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-500">
                                        @if($h->is_credit == 't')
                                            {{ number_format(abs($refunded - $h->gross_total),2) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="px-3 py-3   text-right text-sm font-medium">
                                        <div class="flex flex-row-reverse">
                                            @can('12.refund-sale')
                                                @if($h->gross_total > 0)
                                                    <a class="text-red-600 cursor-pointer  " target="_blank"
                                                       href="{{ url('pharmacy/sales/refund/') }}/{{$h->id}}?type=refund">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd"
                                                                  d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z"
                                                                  clip-rule="evenodd"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                            @endcan
                                            @can('12.reprint-sale')
                                                <a class="text-indigo-600 cursor-pointer " href="javascript:void(0);"
                                                   onclick="window.open('{{ url('pharmacy/print/sale/').'/'.$h->id }}','receipt-print','height=150,width=400');">
                                                    <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd"
                                                              d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                </a>
                                            @endcan

                                            <a class="text-green-600 cursor-pointer  " target="_blank"
                                               href="{{ url('pharmacy/sales/transaction/view') }}/{{$h->id}}">
                                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                    <path fill-rule="evenodd"
                                                          d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        @if($history->hasPages())
                            <div class="bg-white border-t px-3 py-2">
                                {{ $history->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>


    </div>
    @include('pharmacy::include.searchable')
</div>
