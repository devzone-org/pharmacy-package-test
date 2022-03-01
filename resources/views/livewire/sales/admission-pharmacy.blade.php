<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Admission #</label>
                    <input type="text" wire:model.defer="admission_no" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Patient</label>
                    <input type="text" wire:model.defer="patient" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label for="from" class="block text-sm font-medium text-gray-700">From</label>
                    <input type="date" wire:model.defer="from" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">To</label>
                    <input type="date" wire:model.defer="to" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="flex flex-col">
        <div class="-my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow bg-white rounded-lg pb-1 border-b border-gray-200 ">
                    <div class=" py-6 px-4 space-y-6 sm:p-6 ">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Inter Transfer Medicines</h3>
                        </div>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                #
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Admission #
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Patient Name
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Procedure
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Amount(PKR)
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Issue
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Return
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Admitted At
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                Admission Status
                            </th>
                            <th scope="col" class="relative px-3 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($admissions as $admission)
                            <tr>
                                <td class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    {{ $admission->admission_no  }}
                                </td>
                                <td class="px-3 py-3  text-center text-sm text-gray-500">
                                    {{ $admission->patient_name  }}<br>
                                    {{$admission->patient_mr}}
                                </td>
                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    {{ $admission->procedure_name  }}
                                </td>
                                @php
                                    $amount = 0;
                                    $issued = 0;
                                    $returned = 0;
                                    $x = \Devzone\Pharmacy\Models\Sale\Sale::where('admission_id', $admission->admission_id)->where('procedure_id', $admission->procedure_id)->get();
                                    if ($x->isNotEmpty()){
                                        $amount = empty($x->sum('gross_total'))?0:$x->sum('gross_total');
                                        $issued = $x->whereNull('refunded_id')->count();
                                        $returned = $x->whereNotNull('refunded_id')->count();
                                    }
                                @endphp
                                @php
                                    $refunded = 0;
                                            $ref = $x->where('is_refund','t')->pluck('id')->toArray();

                                        $total_refund = \Devzone\Pharmacy\Models\Sale\SaleRefundDetail::from('sale_refund_details as sr')
                                                 ->join('sale_details as sd','sd.id','=','sr.sale_detail_id')
                                                 ->whereIn('sr.sale_id',$ref)
                                                 ->select(\Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as refund'))
                                            ->first();
                                     $refunded = $total_refund['refund'];
                                @endphp



                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    {{ number_format($amount-$refunded, 2)  }}
                                </td>
                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    {{$issued}}


                                </td>
                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    {{$returned}}
                                </td>
                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    {{ date('d M Y',strtotime($admission->admission_date)) }} {{date('H:i A',strtotime($admission->admission_time))}}
                                </td>
                                <td class="px-3 py-3  text-center text-sm text-gray-500">
                                    @if(!empty($admission->checkout_date))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                              Checked Out
                                            </span>
                                        <br>
                                        @ {{date('d M Y',strtotime($admission->checkout_date))}} {{date('H:i A',strtotime($admission->checkout_time))}}
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                               Checked In
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                    <div class="relative inline-block text-left" x-data="{open:false}">
                                        <div>
                                            <button type="button" x-on:click="open=true;" @click.away="open=false;"
                                                    class="  rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                                                    aria-expanded="true" aria-haspopup="true">
                                                <span class="sr-only">Open options</span>
                                                <!-- Heroicon name: solid/dots-vertical -->
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-show="open"
                                             class="origin-top-right absolute right-0 mt-2 w-56 z-10 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                             role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                             tabindex="-1" style="display: none;">
                                            <div class="py-1" role="none">

                                                @if(empty($admission->sale_id) && empty($admission->checkout_date))
                                                    <a href="{{url('pharmacy/sales/add?admission_id='.$admission->admission_id.'&procedure_id='.$admission->procedure_id.'&doctor_id='.$admission->doctor_id)}}"
                                                       class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                       role="menuitem" tabindex="-1">Issue Medicines</a>
                                                @endif
                                                @if(!empty($admission->sale_id))
                                                    <a href="{{url('pharmacy/sales/admissions/detail/'.$admission->sale_id.'?admission_id='.$admission->admission_id.'&procedure_id='.$admission->procedure_id.'&doctor_id='.$admission->doctor_id)}}"
                                                       class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                       role="menuitem" tabindex="-1">Details</a>
                                                    @if(empty($admission->checkout_date))
                                                        <a href="{{url('pharmacy/sales/add?admission_id='.$admission->admission_id.'&procedure_id='.$admission->procedure_id.'&doctor_id='.$admission->doctor_id)}}"
                                                           class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                           role="menuitem" tabindex="-1">Issue More</a>

                                                        {{--                                                        <a href="{{url('pharmacy/sales/refund/'.$admission->sale_id.'?type=issue&admission_id='.$admission->admission_id.'&procedure_id='.$admission->procedure_id.'&doctor_id='.$admission->doctor_id)}}"--}}
                                                        {{--                                                           class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"--}}
                                                        {{--                                                           role="menuitem" tabindex="-1">Issue More</a>--}}

                                                        {{--                                                        <a href="{{url('pharmacy/sales/refund/'.$admission->sale_id.'?type=refund&admission_id='.$admission->admission_id.'&procedure_id='.$admission->procedure_id.'&doctor_id='.$admission->doctor_id)}}"--}}
                                                        {{--                                                           class="text-red-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"--}}
                                                        {{--                                                           role="menuitem" tabindex="-1">Refund</a>--}}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if($admissions->hasPages())
                        <div class="bg-white border-t px-3 py-2">
                            {{ $admissions->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
