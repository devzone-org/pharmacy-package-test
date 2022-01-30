<div class="max-w-full  ">

    <div class=" flex  border-t bg-gray-100">


        <!-- Static sidebar for desktop -->
        <div class="  md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div class="flex flex-col flex-grow border-r border-gray-200   overflow-y-auto">
                    <div class="h-full   bg-white p-2 overflow-y-auto">
                        <div class=" mt-6">

                            <div>

                                <dl class="space-y-4 ">


                                    @if(empty($admission_id) && empty($procedure_id))
                                        <div wire:click="searchReferredBy" class="cursor-pointer   pt-2">
                                            <dt class="text-sm font-medium  text-gray-500   sm:flex-shrink-0">
                                                Referred By (F11)
                                            </dt>
                                            <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                                <p>
                                                    {{ $referred_by_name }}
                                                </p>
                                            </dd>
                                        </div>

                                        <div wire:click="searchPatient" class="cursor-pointer">
                                            <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:flex-shrink-0">
                                                Patient Name (F12)
                                            </dt>
                                            <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                                <p>
                                                    {{ $patient_name ?? 'Walk-in' }}
                                                </p>
                                            </dd>
                                        </div>

                                        @if(!$pending_sale)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                                    Sale On Credit
                                                </dt>
                                                <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                                    <p>
                                                        <input type="checkbox" wire:model="credit"
                                                               class="focus:ring-red-500 h-8 w-8 text-red-600 border-gray-300 rounded">

                                                    </p>
                                                </dd>
                                            </div>

                                            <div>
                                                <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                                    Credit Limit
                                                </dt>
                                                <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                                    <p>
                                                        PKR {{ number_format($customer_credit_limit) }}
                                                    </p>
                                                </dd>
                                            </div>

                                            <div>
                                                <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                                    Closing Balance
                                                </dt>
                                                <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                                    <p>
                                                        PKR {{ number_format($customer_previous_credit) }}
                                                    </p>
                                                </dd>
                                            </div>
                                        @endif
                                    @endif
                                    <div class=" border-t pt-2">
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Date
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                {{ date('d M, Y') }}
                                            </p>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Sale By
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                {{ Auth::user()->name }}
                                            </p>
                                        </dd>
                                    </div>
                                </dl>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="flex flex-col   flex-1 overflow-hidden">
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6 px-4">
                    <div class="lg:flex col-span-12  lg:justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl mb-3 font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                @if(empty($admission_id) && empty($procedure_id))
                                    @if($credit) Credit @endif Sale Invoice
                                @else
                                    Inter Transfer IPD Medicine
                                @endif
                            </h2>
                        </div>
                        <div class="mt-5 flex lg:mt-0 lg:ml-4 ">


                            <span class="ml-3">
                  <button type="button" wire:click="searchableOpenModal('product_id', 'product_name', 'item')"
                          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Search Item (F1)
                  </button>
                </span>
                            <span class="ml-3">
                  <button type="button" wire:click="saleComplete" wire:loading.attr="disabled"
                          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                      <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                           xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                    @if($admission)
                          Transfer Medicine (F2)
                      @else
                        @if($pending_sale)
                          Pending Sale
                          @else
                          Complete Sale
                            @endif
                              (F2)

                      @endif
                  </button>
                </span>

                        </div>

                    </div>

                    <main class="col-span-12">
                        @if(!empty($success))
                            <div class="rounded-md mb-5 bg-green-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <!-- Heroicon name: solid/check-circle -->
                                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20"
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
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20"
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

                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24"
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
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20"
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
{{--                                    <th scope="col"--}}
{{--                                        class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">--}}
{{--                                        Sale--}}
{{--                                    </th>--}}
{{--                                    <th scope="col"--}}
{{--                                        class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">--}}
{{--                                        Pending--}}
{{--                                    </th>--}}
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
                                        <th scope="col" title="Total After Disc"
                                            class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                            Disc PKR
                                        </th>
                                    @endif
                                    <th scope="col" title="Total After Disc"
                                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Gross Total
                                    </th>
{{--                                    <th scope="col"--}}
{{--                                        class="w-10 cursor-pointer px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">--}}

{{--                                    </th>--}}
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sales as $key => $s)
                                    <tr class="{{ !empty($s['type']) ? 'bg-red-50' : '' }}">
                                        <td class="px-2  text-center  border-r text-md font-medium text-gray-900">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-2 text-left border-r text-md text-gray-500">
                                            {{ $s['item'] }}
                                            @if(isset($s['required_qty']))
                                                @if(!empty($s['required_qty']))
                                                    <span
                                                            class="text-red-500 text-sm">(Required Quantity is {{$s['required_qty']}})</span>
                                                @endif
                                            @endif
                                        </td>
{{--                                        <td class="px-2   text-left   border-r  text-md text-gray-500">--}}
{{--                                            <label for="sale_type1" hidden></label>--}}
{{--                                            <input type="radio" wire:model="sales.{{ $key }}.sale_type" name="sale_type" id="sale_type1"--}}
{{--                                                   class="p-0 block  text-md text-center ">--}}
{{--                                            <br>--}}
{{--                                            <label for="sale_type2" hidden></label>--}}
{{--                                            <input type="radio" wire:model="sales.{{ $key }}.sale_type" name="sale_type" id="sale_type2"--}}
{{--                                                   class="p-0 block  text-md text-center ">--}}
{{--                                        </td>--}}
                                        <td class="px-2   text-left   border-r  text-md text-gray-500">
                                            <input type="number" wire:model.lazy="sales.{{ $key }}.s_qty"
                                                   onClick="this.select();"
                                                   class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                                   autocomplete="off">
                                        </td>
                                        <td class="px-2  text-center  border-r text-md text-gray-500">
                                            <input type="number" wire:model.lazy="sales.{{ $key }}.retail_price"
                                                   onClick="this.select();"
                                                   class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                                   autocomplete="off">

                                        </td>
                                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                                            {{ number_format($s['total'],2) }}
                                        </td>
                                        @if($admission==false)
                                            <td class="px-2  text-center border-r text-md text-gray-500">
                                                <input type="number" step="0.01" min="0" max="100" wire:model.lazy="sales.{{ $key }}.disc"
                                                       onClick="this.select();"
                                                       class="text-center p-0 focus:ring-0 block w-full   text-md border-0  "
                                                       autocomplete="off">
                                            </td>
                                            <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                                                {{ number_format($s['total'] - $s['total_after_disc'],2) }}
                                            </td>
                                        @endif
                                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                                            {{ number_format($s['total_after_disc'],2) }}
                                        </td>
                                        <td class="  w-10 cursor-pointer px-2 py-3   border-r text-center text-md font-medium text-red-700  tracking-wider  ">
                                            <svg wire:click="removeEntry('{{ $key }}')" class="w-5 h-5 "
                                                 fill="currentColor"
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
                                    <th scope="col" colspan="2"
                                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col"
                                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                                        {{ collect($sales)->sum('s_qty') }}
                                    </th>
                                    <th scope="col"
                                        class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                                    </th>
                                    <th scope="col"
                                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                        {{ number_format(collect($sales)->sum('total'),2) }}
                                    </th>
                                    @if($admission==false)
                                        <th scope="col"
                                            class="w-7 px-2  border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                                        </th>

                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format(collect($sales)->sum('total') - collect($sales)->sum('total_after_disc'),2) }}
                                        </th>
                                    @endif

                                    <th scope="col"
                                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                        {{ number_format(collect($sales)->sum('total_after_disc'),2) }}

                                    </th>
                                </tr>

                                <tr>
                                    <th colspan="5" class="text-left p-2 text-sm text-red-600">
                                        @if($admission)
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
                                <tr class="bg-gray-50">
                                    <th rowspan="{{ $admission==true? '2' : '5' }}"
                                        colspan="{{ $admission==true? '2' : '3' }}"
                                        class="  border-r   bg-white text-md font-medium text-gray-500  tracking-wider">

                        <textarea name="" cols="30" rows="5" id="remarks"
                                  class="p-0 focus:ring-0 block w-full border-0 text-md resize-none h-40  "></textarea>


                                    </th>
                                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                                        Sub Total
                                    </th>
                                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                                        {{ number_format(collect($sales)->sum('total'),2) }}
                                    </th>
                                </tr>
                                @if($admission==false)
                                    <tr>
                                        <th scope="col" colspan="4"
                                            class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                                            Discount (F3) (%)
                                        </th>
                                        <th scope="col" colspan="2"
                                            class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="number" wire:model.lazy="discount" min="0" max="100"
                                                   onClick="this.select();"
                                                   id="discount"
                                                   class="p-0 focus:ring-0 block w-full  text-xl border-0 font-medium text-gray-500 text-center "
                                                   autocomplete="off">
                                        </th>
                                    </tr>
                                @endif
                                <tr class="bg-gray-50">
                                    <th scope="col" colspan="{{ $admission==true? '3' : '4' }}"
                                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                                        Gross Total
                                    </th>
                                    <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                                        {{ number_format(collect($sales)->sum('total_after_disc'),2) }}
                                    </th>
                                </tr>
                                @if($admission==false && $credit == false)
                                    <tr class="bg-gray-50">
                                        <th scope="col" colspan="4"
                                            class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
{{--                                            After Round-off--}}
                                        </th>
                                        <th scope="col" colspan="2"
                                            class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                                            {{--{{ number_format(round(collect($sales)->sum('total_after_disc')/5)*5 ,2) }}--}}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col" colspan="4"
                                            class="w-7 px-2 text-left border-r py-2 @if($credit==true) bg-red-50 @endif text-xl font-medium text-gray-500  tracking-wider">

                                            <div class="ml-3 float-right">
                                                <span class=""> Received Amount (F4)</span>
                                            </div>
                                        </th>
                                        <th scope="col" colspan="2"
                                            class="w-10   px-2 py-2 @if($credit==true) bg-red-50 @endif  border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">

                                            <input type="number" wire:model.debounce.300ms="received"
                                                   onClick="this.select();"
                                                   id="received" @if($credit==true) disabled @endif

                                                   wire:keydown.enter="saleComplete"
                                                   class="p-0 focus:ring-0 block w-full @if($credit==true) bg-red-50 @endif  text-xl border-0 font-medium text-gray-500 text-center "
                                                   autocomplete="off">

                                        </th>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                                            class="w-7 px-2    py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                                            Remarks (F5)
                                        </th>

                                        <th scope="col" colspan="{{ $admission==true? '2' : '5' }}"
                                            class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                                            @if($credit==true && $received < collect($this->sales)->sum('total_after_disc'))
                                                On Account
                                            @else
                                                Change
                                            @endif

                                        </th>
                                        <th scope="col" colspan="{{ $admission==true? '1' : '2' }}"
                                            class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                                            {{ number_format($payable,2) }}
                                        </th>
                                    </tr>
                                @endif
                                @if($admission)
                                    <tr class="bg-gray-50">
                                        <th scope="col" colspan="2"
                                            class="w-7 px-2    py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                                            Remarks (F5)
                                        </th>
                                        <td scope="col" colspan="5"
                                            class="w-10 bg-gray-50 border-0 text-center text-sm  text-gray-500  tracking-wider">
                                            <div class="flex -m-1">

                                <span class="inline-flex items-center px-3 bg-gray-50 text-gray-500 text-xl font-medium">
                                  Handed over to
                                </span>
                                                <input type="text" wire:model.defer="handed_over"
                                                       onClick="this.select();"
                                                       class="flex-1 border-0 min-w-0 block w-full px-3 py-2 rounded-none focus:ring-0 text-xl placeholder-gray-500 placeholder-opacity-50"
                                                       placeholder="Enter here">
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>


                    </main>
                </div>
            </main>
        </div>
    </div>


    <div id="add_modal" x-data="{ open: @entangle('add_modal') }" x-cloak x-show="open"
         class="fixed z-50 inset-0 overflow-y-auto ">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-description="Background overlay, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
            <div x-show="open" x-description="Modal panel, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg  text-left
                    overflow-hidden shadow-xl transform transition-all
                    sm:my-8 sm:align-middle sm:max-w-xl sm:w-full  "
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class=" p-4">

                    <div class="mb-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add Patient</h3>
                    </div>
                    @if ($errors->any())
                        <div class="rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <!-- Heroicon name: x-circle -->
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        @php
                                            $count = count($errors->all());
                                        @endphp
                                        There {{ $count > 1 ? "were {$count} errors": "was {$count} error" }}
                                        with
                                        your submission
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">

                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(!empty($success))
                        <div class="rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <!-- Heroicon name: check-circle -->
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
                                        <button type="button" wire:click="$set('success', '')"
                                                class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                            <span class="sr-only">Dismiss</span>
                                            <!-- Heroicon name: x -->
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 20 20"
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
                    <div class="grid grid-cols-6 gap-6 @if(!empty($success)|| ($errors->any())) mt-2 @endif">
                        <div class="col-span-6 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">MR #</label>
                            <input wire:model="patient_mr" type="text" autocomplete="off" readonly
                                   class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Patient Name<span
                                        class="text-red-500">*</span></label>
                            <input wire:model="add_patient_name" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Father/Husband Name<span
                                        class="text-red-500">*</span></label>
                            <input wire:model="father_husband_name" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Contact #
                                @if($has_contact)<span
                                        class="text-red-500">*</span>@endif
                                @if($has_contact)
                                    <p wire:click="hasContact"
                                       class="float-right cursor-pointer text-sm text-red-500">
                                        Do not Have Phone?
                                    </p>
                                @else
                                    <p wire:click="hasContact"
                                       class="float-right cursor-pointer text-sm text-indigo-500">
                                        Have Phone?
                                    </p>
                                @endif
                                {{--                                @if(!$add_more_contact)--}}
                                {{--                                    <p class="float-right text-indigo-500 cursor-pointer" wire:click="addMoreContact">+--}}
                                {{--                                        Add More</p>--}}
                                {{--                                @else--}}
                                {{--                                    <p class="float-right text-indigo-500 cursor-pointer"--}}
                                {{--                                       wire:click="removeMoreContact">- Remove More</p>--}}
                                {{--                                @endif--}}
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input wire:model="patient_contact" type="text" autocomplete="off"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full  pr-12 sm:text-sm border-gray-300 rounded-md">
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <label class="sr-only">Contact</label>
                                    <select wire:model="patient_contact_whatsApp"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md">
                                        <option value="t">WhatsApp</option>
                                        <option value="f">None</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{--                        @if($add_more_contact)--}}
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Contact# 2</label>
                            <input wire:model="patient_contact_2" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Landline#</label>
                            <input wire:model="patient_contact_3" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        {{--                        @endif--}}
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Gender<span
                                        class="text-red-500">*</span></label>
                            <select wire:model="patient_gender"
                                    class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value=""></option>
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                                <option value="o">Other</option>
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Age (Years)</label>
                            <input wire:model.debounce.1000ms="patient_age" type="number" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Date of birth</label>
                            <input wire:model.lazy="patient_dob" type="date" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Doctor</label>
                            <select wire:model="patient_doctor"
                                    class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value=""></option>
                                @foreach($doctors as $doctor)
                                    <option value="{{$doctor['id']}}">{{$doctor['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Registration Date</label>
                            <input wire:model="patient_registration_date" type="date" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <input wire:model="patient_city" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Referred By</label>
                            <input wire:model="patient_referred_by" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-6 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea wire:model="patient_address" name="about" rows="3"
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                        </div>
                    </div>
                    <div class="py-3  text-right ">
                        <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                @click="open = false">
                            Close
                        </button>
                        <button type="button" wire:click="addPatient"
                                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-900 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                            Add
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div x-data="{ open: @entangle('customer_modal') }" x-cloak x-show="open"
         class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-description="Background overlay, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
            <div x-show="open" x-description="Modal panel, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg  text-left
                    overflow-hidden shadow-xl transform transition-all
                    sm:my-8 sm:align-middle sm:max-w-xl sm:w-full  "
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class=" p-4">

                    <div class="mb-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Patient Credit Details</h3>
                    </div>
                    <form wire:submit.prevent="addCreditor">
                        <div class="grid grid-cols-6 gap-6 ">

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Patient Name</label>
                                <input type="text" wire:model="patient_name" readonly
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Care of</label>
                                <select wire:model.defer="customers.care_of"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value=""></option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e['id'] }}">{{ $e['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('customers.care_of')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Credit Limit</label>
                                <input type="number" wire:model.defer="customers.credit_limit"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('customers.credit_limit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                        <div class="py-3 mt-3 text-right ">
                            <button type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    @click="open = false">
                                Close
                            </button>
                            <button type="submit"
                                    class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-900 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                                Add
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
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
            window.location = '{{url('pharmacy/sales/view')}}' + '/' + saleId + '?admission_id=' + admissionId + '&procedure_id=' + procedureId;
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
        // if (event.keyCode == 121) {
        //     event.preventDefault();
        //     event.stopPropagation();
        //     window.livewire.emit('searchCustomer');
        // }
        if (event.keyCode == 122) {
            event.preventDefault();
            event.stopPropagation();
            window.livewire.emit('searchReferredBy');
        }
        if (event.keyCode == 123) {
            event.preventDefault();
            event.stopPropagation();
            window.livewire.emit('searchPatient');
        }

    });
</script>
