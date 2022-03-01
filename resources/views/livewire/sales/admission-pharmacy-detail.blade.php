<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Admission Detail</h3>
            </div>
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Admission #</dt>
                    <dd>{{$admission_details->admission_no}}</dd>
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Admission Date & Time</dt>
                    <dd>{{date('d M, Y', strtotime($admission_details->admission_date))}} {{date('h:i A', strtotime($admission_details->admission_time))}}</dd>
                </div>


                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Checkout Date & Time</dt>
                    @if(!empty($admission_details->checkout_date))
                        <dd>{{date('d M, Y', strtotime($admission_details->checkout_date))}} {{date('h:i A', strtotime($admission_details->checkout_time))}}</dd>
                    @else
                        <dd>-</dd>
                    @endif
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Procedure Name</dt>
                    <dd>{{$admission_details->procedure_name}}</dd>
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Doctor Name</dt>
                    <dd>{{$admission_details->doctor_name}}</dd>
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Patient Name</dt>
                    <dd>{{$admission_details->patient_name}}</dd>
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <dt class="block font-medium text-gray-700">Status</dt>
                    @if($admission_details->status == 't')
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                        </dd>
                    @else
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        </dd>
                    @endif
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
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Issue History</h3>

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
                                    Handed To
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

                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Issue
                                            </span>


                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Refund
                                            </span>
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
                                        @php
                                            $issue_to= \Devzone\Pharmacy\Models\Sale\SaleIssuance::where('sale_id',$h->id)->get();
                                        @endphp
                                        @if($issue_to->isNotEmpty())
                                            {{ $issue_to->first()->handed_over_to }}
                                        @endif
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
                        @if(!empty($history) && $history->hasPages())
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
