<div class="max-w-3xl mx-auto mt-5   lg:max-w-7xl   lg:grid lg:grid-cols-12 lg:gap-4">

    <main class="col-span-12 ">


        <div class="lg:flex  lg:justify-between  ">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl mb-3 font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    @if($admission)
                        Inter Transfer IPD
                    @endif
                    Receipt #{{ $sale_id }}
                </h2>

            </div>
            <div class="mt-5 flex lg:mt-0 lg:ml-4 ">
                <span class="ml-3">
                   <a class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      href="javascript:void(0);"
                      onclick="window.open('{{ url('pharmacy/print/sale/').'/'.$sale_id }}','receipt-print','height=150,width=400');">
                       Print Sale
                   </a>
                </span>
            </div>

        </div>

        @if($admission)
            <div class="grid mb-3  bg-white gap-x-4 gap-y-8 grid-cols-5   shadow rounded-md p-3">
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Referred By
                    </dt>
                    <dd class="mt-1 text-lg font-medium text-gray-900">
                        {{ $admission_details['doctor'] ?? '-' }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Patient Name
                    </dt>
                    <dd class="mt-1 text-lg font-medium text-gray-900">
                        {{  $admission_details['mr_no'].' - '.$admission_details['name'] ?? 'Walk-in' }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Admission # - Procedure
                    </dt>
                    <dd class="mt-1 text-lg font-medium text-gray-900">
                        {{  $admission_details['admission_no'] ?? '' }} - {{$admission_details['procedure_name']}}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Date
                    </dt>
                    <dd class="mt-1 text-xl font-medium text-gray-900">
                        {{ $sale_at }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Sale By
                    </dt>
                    <dd class="mt-1 text-xl font-medium text-gray-900">
                        {{ $sale_by }}
                    </dd>
                </div>
            </div>
        @else
            <div class="grid mb-3  bg-white gap-x-4 gap-y-8 grid-cols-4   shadow rounded-md p-3">
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Referred By
                    </dt>
                    <dd class="mt-1 text-xl font-medium text-gray-900">
                        {{ $referred_by_name ?? '-' }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Patient Name
                    </dt>
                    <dd class="mt-1 text-xl font-medium text-gray-900">
                        {{ $patient_name ?? 'Walk-in' }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Date
                    </dt>
                    <dd class="mt-1 text-xl font-medium text-gray-900">
                        {{ $sale_at }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Sale By
                    </dt>
                    <dd class="mt-1 text-xl font-medium text-gray-900">
                        {{ $sale_by }}
                    </dd>
                </div>
            </div>
        @endif


        <div class="bg-white  overflow-hidden  shadow rounded-lg">
            <table class="min-w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                        #
                    </th>
                    <th scope="col"
                        class="  px-2   border-r py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                        Item
                    </th>
                    <th scope="col"
                        class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                        Qty
                    </th>
                    <th scope="col"
                        class="w-28 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                        Unit Price
                    </th>
                    <th scope="col"
                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                        Total
                    </th>
                    @if($admission==false)
                        <th scope="col"
                            class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                            Disc %
                        </th>
                        <th scope="col" title="Discount PKR"
                            class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                            Disc PKR
                        </th>
                    @endif
                    <th scope="col" title="Total After Disc"
                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                        Gross Total
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sales_ref as $key => $s)
                    <tr class="{{ $s['refunded'] == true ? 'bg-red-50':'' }}">
                        <td class="px-2 py-2 text-center  border-r text-md font-medium text-gray-900">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-2 text-left border-r text-md text-gray-500">
                            {{ $s['item'] }}
                        </td>
                        <td class="px-2 text-center border-r text-md text-gray-500">
                            {{ $s['sale_qty'] }}
                        </td>
                        <td class="px-2 text-center border-r text-md text-gray-500">
                            {{ number_format($s['retail_price'],2) }}
                        </td>
                        <td class="px-2 text-center border-r text-md text-gray-500">
                            {{ number_format($s['total'],2) }}
                        </td>
                        @if($admission==false)
                            <td class="px-2 text-center border-r text-md text-gray-500">
                                {{ number_format($s['disc'],2)  }}
                            </td>
                            <td class="px-2 text-center border-r text-md text-gray-500">
                                {{ number_format($s['total'] - $s['total_after_disc'],2)  }}
                            </td>
                        @endif
                        <td class="px-2   text-center border-r text-md text-gray-500">
                            {{ number_format( $s['total_after_disc'],2)  }}
                        </td>

                    </tr>
                @endforeach
                <tr>
                    <th scope="col" colspan="2"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Total
                    </th>
                    <th scope="col"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ collect($sales_ref)->sum('sale_qty') }}
                    </th>
                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                    </th>
                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                        {{ number_format(collect($sales_ref)->sum('total'),2) }}
                    </th>
                    @if($admission==false)
                        <th scope="col"
                            class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                        </th>

                        <th scope="col"
                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                            {{ number_format(collect($sales_ref)->sum('total') - collect($sales_ref)->sum('total_after_disc'),2) }}

                        </th>
                    @endif
                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                        {{ number_format(collect($sales_ref)->sum('total_after_disc'),2) }}
                    </th>
                </tr>
                <tr>
                    <th class="p-2">&nbsp;</th>
                </tr>
                <tr class="bg-gray-50">
                    <th rowspan="{{ $admission==true? '7' : '7' }}" colspan="{{ $admission==true? '2' : '3' }}"
                        class="  border-r   bg-white text-md font-medium text-gray-500  tracking-wider">
                        @if(!empty($refund_against))
                            <span class="text-base text-indigo-500">
                                <a target="_blank" href="{{ url('pharmacy/sales/view/') }}/{{$refund_against}}">
                                    Refunded Against  Receipt # {{$refund_against}}
                                </a>
                            </span>
                        @else
                            &nbsp;
                        @endif

{{--                        <textarea name="" cols="30" rows="5" id="remarks" wire:model.defer="remarks"--}}
{{--                                  class="p-0 focus:ring-0 block w-full border-0 text-md resize-none h-40  "></textarea>--}}

                    </th>
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Sale
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales_ref)->where('sale_qty','>','0')->sum('total'),2) }}
                    </th>
                </tr>
                @if($admission==false )
                    <tr>
                        <th scope="col" colspan="4"
                            class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                            Discount (PKR)
                        </th>
                        <th scope="col" colspan="2"
                            class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                            {{ number_format(collect($sales_ref)->where('sale_qty','>','0')->sum('total') - collect($sales_ref)->where('sale_qty','>','0')->sum('total_after_disc'),2) }}
                        </th>
                    </tr>
                @endif
                <tr class="bg-gray-50">
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Net Sale
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales_ref)->where('sale_qty','>','0')->sum('total_after_disc'),2) }}
                    </th>
                </tr>
                <tr>
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Returned
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        ({{ number_format(abs(collect($sales_ref)->where('sale_qty','<','0')->sum('total_after_disc')),2) }})
                    </th>
                </tr>
                <tr class="bg-gray-50">
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Net Total
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(abs(collect($sales_ref)->sum('total_after_disc')),2) }}
                    </th>
                </tr>
                <tr>
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Cash Received
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales_ref)->first()['receive_amount'],2) }}
                    </th>
                </tr>
                <tr class="bg-gray-50">
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Change Returned
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales_ref)->first()['payable_amount'],2) }}
                    </th>
                </tr>

                <tr class="bg-gray-50">
                    @if($admission==false)
                        <th scope="col" colspan="2"
                            class="w-7 px-2    py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                            Remarks
                        </th>
                    @endif
                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        &nbsp;
                    </th>
                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        &nbsp;
                    </th>
                </tr>


                </tbody>
            </table>
            @if($admission)
                <div>
                    <p class="p-2">Handed over to

                        @foreach($handed_over as $ho)
                            {{ $ho['handed_over_to'] }}
                            @if(!$loop->last) ,
                            @endif
                        @endforeach

                    </p>
                </div>
            @endif

        </div>


    </main>


</div>
