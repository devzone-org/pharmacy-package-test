<div class="max-w-3xl mx-auto    lg:max-w-7xl   lg:grid lg:grid-cols-12 lg:gap-4">
    <main class="col-span-12 ">
        <div class="lg:flex  lg:justify-between ">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl mb-3 font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    @if(empty($admission_id) && empty($procedure_id))
                        Refund Invoice # {{ $sale_id }}
                    @else
                        Inter Transfer IPD Medicine Receipt # {{ $sale_id }}
                    @endif
                </h2>
            </div>
            <div class="mt-5 flex lg:mt-0 lg:ml-4 ">
                <span class="ml-3">
                    @if($credit==false)
                        @if($type=='issue' || empty($admission_id))
                            <button type="button" wire:click="searchableOpenModal('product_id', 'product_name', 'item')"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Search Item (F1)
                        </button>
                        @endif
                    @endif
                </span>
                <span class="ml-3">
                  <button type="button" wire:click="saleComplete" wire:loading.attr="disabled"
                          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                      <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                           xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      @if(empty($admission_id) && empty($procedure_id))
                          Refund Sale (F2)
                      @else
                          @if($type=='refund')
                              Refund Transfer Medicines (F2)
                          @elseif($type=='issue')
                              Transfer More Medicines (F2)
                          @endif
                      @endif
                  </button>
                </span>
            </div>
        </div>
        @if(!empty($admission_id) && !empty($procedure_id))
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
                    <dd class="mt-1 text-lg font-medium text-gray-900">
                        {{ date('d M, Y') }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Issued By
                    </dt>
                    <dd class="mt-1 text-lg font-medium text-gray-900">
                        {{ Auth::user()->name }}
                    </dd>
                </div>
            </div>
        @else
            <div class="grid mb-3 bg-white gap-x-4 gap-y-8 grid-cols-5 shadow rounded-md p-3">
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Customer
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer_name ?? '-' }}
                    </dd>
                </div>
                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Referred By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $referred_by_name ?? '-' }}
                    </dd>
                </div>

                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Patient Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $patient_name ?? '-' }}
                    </dd>
                </div>

                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ date('d M, Y') }}
                    </dd>
                </div>

                <div class="">
                    <dt class="text-sm font-medium text-gray-500">
                        Refund By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ Auth::user()->name }}
                    </dd>
                </div>
            </div>
        @endif
        @if(!empty($success))
            <div class="rounded-md mb-5 bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/check-circle -->
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                             fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ $success }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" wire:click="$set('success','')"
                                    class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                <span class="sr-only">Dismiss</span>
                                <!-- Heroicon name: solid/x -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($error))
            <div class="rounded-md mb-5 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">

                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ $error }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" wire:click="$set('error','')"
                                    class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                <span class="sr-only">Dismiss</span>
                                <!-- Heroicon name: solid/x -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
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
                    @if(empty($admission_id) && empty($procedure_id))
                        <th scope="col"
                            class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                            Disc %
                        </th>
                    @endif
                    <th scope="col" title="Total After Disc"
                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                        Gross Total
                    </th>
                    <th scope="col"
                        class="w-12 cursor-pointer px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">

                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                @foreach($old_sales as $key => $s)
                    <tr>
                        <td class="px-2  text-center  border-r text-md font-medium text-gray-900">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-2  text-left   border-r text-md text-gray-500">
                            {{ $s['item'] }}
                        </td>
                        <td class="px-2   text-center   border-r  text-md text-gray-500">
                            {{$s['qty']}}

                        </td>
                        <td class="px-2  text-center  border-r text-md text-gray-500">
                            {{ number_format($s['retail_price'],2) }}
                        </td>
                        <td class="px-2   text-center border-r text-md text-gray-500">
                            {{ number_format($s['total'],2) }}
                        </td>
                        @if(empty($admission_id) && empty($procedure_id))
                            <td class="px-2  text-center border-r text-md text-gray-500">
                                {{ number_format($s['disc'],2) }}
                            </td>
                        @endif
                        <td class="px-2    text-center border-r text-md text-gray-500">
                            {{ number_format($s['total_after_disc'],2) }}
                        </td>
                        <td class="  w-12 cursor-pointer px-2 py-3   border-r text-center text-md font-medium text-red-700  tracking-wider">
                            @if($type=='refund')
                                <svg wire:click="refundEntry('{{ $key }}')" class="w-5 h-5 " fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            @else
                                &nbsp;
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if(!empty($refunds)  )
                    <tr class="">
                        <th scope="col" colspan="8"
                            class="w-10 px-3 py-3 text-left text-sm font-medium text-red-500   ">
                            <i>Refunds Entries</i>
                        </th>
                    </tr>
                @endif

                @foreach($refunds as $key => $s)
                    <tr>
                        <td class="px-2  text-center  border-r text-md font-medium text-gray-900">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-2  text-left   border-r text-md text-gray-500">
                            {{ $s['item'] }}
                        </td>
                        <td class="px-2   text-center   border-r  text-md text-gray-500">
                            @if(isset($s['restrict']))
                                {{ $s['qty'] }}
                            @else
                                <input type="number" wire:model.debounce.500ms="refunds.{{ $key }}.qty"
                                       onClick="this.select();"
                                       class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                       autocomplete="off">
                            @endif
                        </td>
                        <td class="px-2  text-center  border-r text-md text-gray-500">
                            {{ number_format($s['retail_price'],2) }}
                        </td>
                        <td class="px-2    text-center border-r text-md text-gray-500">
                            {{ number_format($s['total'],2) }}
                        </td>
                        @if(empty($admission_id) && empty($procedure_id))
                            <td class="px-2  text-center border-r text-md text-gray-500">
                                {{ number_format($s['disc'],2) }}
                            </td>
                        @endif
                        <td class="px-2   text-center border-r text-md text-gray-500">
                            {{ number_format($s['total_after_disc'],2) }}
                        </td>
                        <td class="  w-12 cursor-pointer px-2 py-3   border-r text-center text-md font-medium text-red-700  tracking-wider  ">
                            @if(isset($s['restrict']))
                                &nbsp;
                            @else
                                <svg wire:click="removeRefundEntry('{{ $key }}')" class="w-5 h-5 "
                                     fill="currentColor"
                                     viewBox="0 0 20 20"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                          clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if(!empty($sales))
                    <tr class="">
                        <th scope="col" colspan="8"
                            class="w-10 px-3 py-3 text-left text-sm font-medium text-green-500   ">
                            <i>New Entries</i>
                        </th>
                    </tr>
                @endif
                @foreach($sales as $key => $s)
                    <tr>
                        <td class="px-2  text-center  border-r text-md font-medium text-gray-900">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-2  text-left   border-r text-md text-gray-500">
                            {{ $s['item'] }}
                        </td>
                        <td class="px-2   text-left   border-r  text-md text-gray-500">
                            <input type="number" wire:model.debounce.500ms="sales.{{ $key }}.s_qty"
                                   onClick="this.select();"
                                   class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                   autocomplete="off">
                        </td>
                        <td class="px-2  text-center  border-r text-md text-gray-500">
                            <input type="number" wire:model.debounce.500ms="sales.{{ $key }}.retail_price"
                                   onClick="this.select();"
                                   class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                   autocomplete="off">

                        </td>
                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                            {{ number_format($s['total'],2) }}
                        </td>
                        @if(empty($admission_id) && empty($procedure_id))
                            <td class="px-2  text-center border-r text-md text-gray-500">
                                <input type="number" step="0.01" wire:model.debounce.500ms="sales.{{ $key }}.disc"
                                       onClick="this.select();"
                                       class="text-center p-0 focus:ring-0 block w-full   text-md border-0  "
                                       autocomplete="off">
                            </td>
                        @endif
                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                            {{ number_format($s['total_after_disc'],2) }}
                        </td>
                        <td class="  w-10 cursor-pointer px-2 py-3   border-r text-center text-md font-medium text-red-700  tracking-wider  ">
                            <svg wire:click="removeEntry('{{ $key }}')" class="w-5 h-5 " fill="currentColor"
                                 viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5" class="text-left p-2 text-sm text-red-600">
                        @if(!empty($admission_id) && !empty($procedure_id))
                            @if($hospital_info['transfer_medicine']=='cost_of_price')
                                Note: Medicines will be issued on Supply Price.
                            @else
                                Note: Medicines will be issued on Retail Price.
                            @endif
                        @else
                            &nbsp;
                        @endif
                    </th>
                </tr>
                <tr>
                    <th scope="col" colspan="5"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Invoice Total
                    </th>
                    <th scope="col" colspan="2"
                        class="w-12 px-2 py-2 border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format( collect($old_sales)->sum('total_after_disc') - collect($refunds)->where('restrict',true)->sum('total_after_disc') ,2) }}
                    </th>
                </tr>
                <tr class="bg-gray-50">
                    <th rowspan="@if(empty($admission_id) && empty($procedure_id)) 6 @else 3 @endif" colspan="2"
                        class="  border-r   bg-white text-md font-medium text-gray-500  tracking-wider">

                    </th>
                    <th scope="col" colspan="3"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        New Sub Total
                    </th>
                    <th scope="col" colspan="2"
                        class="w-12 cursor-pointer px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales)->sum('total'),2) }}
                    </th>
                </tr>
                @if(empty($admission_id) && empty($procedure_id))
                    <tr>
                        <th scope="col" colspan="3"
                            class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                            Discount(%) (F3)
                        </th>
                        <th scope="col" colspan="2"
                            class="w-12   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                            <input type="number" wire:model.debounce.500ms="discount" min="0" max="100"
                                   onClick="this.select();"
                                   id="discount"
                                   class="p-0 focus:ring-0 block w-full  text-md border-0 font-medium text-gray-500 text-center "
                                   autocomplete="off">
                        </th>
                    </tr>
                @endif
                <tr class="bg-gray-50">
                    <th scope="col" colspan="3"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Gross
                        Total @if(!$credit && env('ROUNDOFF_CHECK', false) && collect($sales)->sum('total_after_disc') >= env('MIMIMUM_ROUNDOFF_BILL', 50))
                            <br><span class="text-xs"> (After Round Off)</span> @endif
                    </th>
                    <th scope="col" colspan="2"
                        class="w-12 cursor-pointer px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        @if(!$credit && env('ROUNDOFF_CHECK', false) && collect($sales)->sum('total_after_disc') >= env('MIMIMUM_ROUNDOFF_BILL', 50))
                            {{ number_format(round(collect($sales)->sum('total_after_disc')/5)*5, 2) }}
                        @else
                            {{ number_format(collect($sales)->sum('total_after_disc'),2) }}
                        @endif

                    </th>
                </tr>
                <tr>
                    <th scope="col" colspan="3"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Refunded
                    </th>
                    <th scope="col" colspan="2"
                        class="w-12 px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
{{--                        @php--}}
{{--                            $round = 0;--}}
{{--                            if (empty(collect($refunds)->where('restrict',true)->sum('total_after_disc'))){--}}
{{--                                if (isset($refunds[0]['rounded_inc']) && !empty($refunds[0]['rounded_inc'])){--}}
{{--                                    $round = $refunds[0]['rounded_inc'];--}}
{{--                                }elseif (isset($refunds[0]['rounded_dec']) && !empty($refunds[0]['rounded_dec'])){--}}
{{--                                    $round = -1 * $refunds[0]['rounded_dec'];--}}
{{--                                }--}}
{{--                            }--}}

{{--                        @endphp--}}
                        @if(!$credit)
                            {{ number_format( collect($refunds)->sum('total_after_disc') - collect($refunds)->where('restrict',true)->sum('total_after_disc') ,2) }}
                        @elseif($credit)
                            ({{ number_format(collect($refunds)->sum('total_after_disc') - collect($refunds)->where('restrict',true)->sum('total_after_disc'),2) }}
                            )
                        @endif
                    </th>
                </tr>
{{--                @php--}}
{{--                    $rounded_off_sale_total = collect($sales)->sum('total_after_disc');--}}
{{--                        if(!$this->credit){--}}
{{--                            if (env('ROUNDOFF_CHECK', false) && collect($sales)->sum('total_after_disc') >= env('MIMIMUM_ROUNDOFF_BILL', 50)){--}}
{{--                                $rounded_off_sale_total = round(collect($sales)->sum('total_after_disc')/5)*5;--}}
{{--                            }--}}
{{--                            if (empty(collect($refunds)->where('restrict',true)->sum('total_after_disc'))){--}}
{{--                                $dif = ($rounded_off_sale_total) + (collect($refunds)->where('restrict',true)->sum('total_after_disc')) - (collect($refunds)->sum('total_after_disc')+$round);--}}
{{--                            }else{--}}
{{--                                $dif = ($rounded_off_sale_total) + (collect($refunds)->where('restrict',true)->sum('total_after_disc')) - (collect($refunds)->sum('total_after_disc'));--}}
{{--                            }--}}
{{--                        }--}}
{{--                        else{--}}
{{--                            $dif = ($rounded_off_sale_total) + (collect($refunds)->where('restrict',true)->sum('total_after_disc')) - (collect($refunds)->sum('total_after_disc'));--}}
{{--                        }--}}
{{--                @endphp--}}
                @php
                    if(!$this->credit && env('ROUNDOFF_CHECK', false) && collect($sales)->sum('total_after_disc') >= env('MIMIMUM_ROUNDOFF_BILL', 50)){
                                $rounded_off_sale_total = round(collect($sales)->sum('total_after_disc')/5)*5;
                                $dif = ($rounded_off_sale_total) + (collect($refunds)->where('restrict',true)->sum('total_after_disc')) - (collect($refunds)->sum('total_after_disc'));
                    }
                    else{
                        $dif = (collect($sales)->sum('total_after_disc')) + (collect($refunds)->where('restrict',true)->sum('total_after_disc')) - (collect($refunds)->sum('total_after_disc'));
                    }
                @endphp

                <tr class="bg-gray-50">
                    <th scope="col" colspan="3"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        @if($dif>0)
                            Receivable
                        @else
                            Payable
                        @endif
                        @if(!$credit && env('ROUNDOFF_CHECK', false) && collect($sales)->sum('total_after_disc') >= env('MIMIMUM_ROUNDOFF_BILL', 50))
                            <br><span class="text-xs"> (After Round Off)</span> @endif
                    </th>
                    <th scope="col" colspan="2"
                        class="w-12 px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        @if($dif>0)
                            {{ number_format($dif,2) }}
                        @else
                            ({{ number_format(abs($dif),2) }})
                        @endif
                    </th>
                </tr>
                <tr>
                    <th scope="col" colspan="3"
                        class="w-7 px-2 @if($credit==true) bg-red-50 @endif  border-r py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                        @if($credit==true)
                            <input id="credit" wire:model="credit" type="checkbox" disabled
                                   class="focus:ring-red-500 h-4 w-4 text-red-600 border-red-300 rounded">
                            <label for="credit" class="text-red-500 text-sm">On Credit</label>
                        @endif
                        <div class="ml-3 float-right">
                            Amount
                            @if($dif>0)
                                Received
                            @else
                                Paid
                            @endif
                        </div>


                    </th>
                    <th scope="col" colspan="2"
                        class="w-12 px-2 py-2 @if($credit==true) bg-red-50 @endif  border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        <input type="number" wire:model.debounce.300ms="received" onclick="this.select();" id="received"
                               wire:keydown.enter="saleComplete" @if($credit==true) disabled @endif
                               class="p-0 focus:ring-0 block w-full @if($credit==true) bg-red-50 @endif  text-md border-0 font-medium text-gray-500 text-center "
                               autocomplete="off">
                    </th>
                </tr>
                <tr class="bg-gray-50">
                    <th scope="col" colspan="2"
                        class="w-7 px-2 py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                    </th>
                    @if(!empty($admission_id) && !empty($procedure_id))

                        <td scope="col" colspan="@if($type=='issue') 4 @else 5 @endif"
                            class="w-10 bg-gray-50 border-0 text-center text-sm  text-gray-500  tracking-wider">
                            @if($type=='issue')
                                <div class="flex -m-1">
                                <span class="inline-flex items-center px-3 bg-gray-50 text-gray-500 text-xl font-medium">
                                  Handed over to
                                </span>
                                    <input type="text" wire:model.defer="handed_over" onClick="this.select();"
                                           class="flex-1 border-0 min-w-0 block w-full px-3 py-2 rounded-none focus:ring-0 text-xl placeholder-gray-500 placeholder-opacity-50"
                                           placeholder="Enter here">
                                </div>
                            @endif
                        </td>
                    @else
                        <th scope="col" colspan="3"
                            class="w-7 px-2 border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                            Change Due
                        </th>
                        <th scope="col" colspan="2"
                            class="w-10 cursor-pointer px-0 py-0 border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                            @if($dif>0)
                                {{$received != '' ? number_format($received-$dif,2) : 0}}
                            @else
                                0
                            @endif
                        </th>
                    @endif
                </tr>

                </tbody>
            </table>
        </div>


    </main>


    @include('pharmacy::include.searchable')
</div>

<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('focusInput', postId => {
            setTimeout(() => {
                document.getElementById('searchable_query').focus();
            }, 100);
        });
    });
    Livewire.on('printInvoice', (saleId, admissionId, procedureId) => {
        window.open('{{ url('pharmacy/print/sale')}}' + '/' + saleId, 'receipt-print', 'height=150,width=400');
        if (admissionId != null && procedureId != null) {
            window.location = '{{url('pharmacy/sales/refund')}}' + '/' + saleId + '?type=issue&admission_id=' + admissionId + '&procedure_id=' + procedureId;
        }
    });

    document.addEventListener("keydown", event => {
        if (event.keyCode == 112) {
            event.preventDefault();
            event.stopPropagation();
            window.livewire.emit('openSearch');
        }

        if (event.keyCode == 113) {
            event.preventDefault();
            event.stopPropagation();
            window.livewire.emit('saleComplete');
        }

        if (event.keyCode == 114) {
            event.preventDefault();
            event.stopPropagation();
            const input = document.getElementById('discount');
            input.focus();
            input.select();

        }

        if (event.keyCode == 115) {
            event.preventDefault();
            event.stopPropagation();
            const input = document.getElementById('received');
            input.focus();
            input.select();
        }

        if (event.keyCode == 116) {
            event.preventDefault();
            event.stopPropagation();
            const input = document.getElementById('remarks');
            input.focus();
            input.select();

        }
    });
</script>
