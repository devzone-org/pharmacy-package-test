<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="salesman" class="block text-sm font-medium text-gray-700">Return By</label>
                    <select wire:model.defer="salesman_id" id="salesman"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($salemen as $saleman)
                            <option value="{{$saleman['id']}}">{{$saleman['name']}}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label for="salesman" class="block text-sm font-medium text-gray-700">Date Range</label>
                    <select wire:model="range"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="seven_days">Last 7 Days</option>
                        <option value="thirty_days">Last 30 Days</option>
                        <option value="custom_range">Custom Range</option>
                    </select>
                </div>
                    <div class=" {{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                        <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                        <input type="text" wire:model.lazy="from" id="from" autocomplete="off" readonly
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="{{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                        <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                        <input type="text" wire:model.lazy="to" id="to" autocomplete="off" readonly
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                <div class="{{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                    <label for="" class="block text-sm font-medium text-gray-700">Sale From Time</label>
                    <input type="time" wire:model.lazy="time_from" autocomplete="off" id=""
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="{{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                    <label for="" class="block text-sm font-medium text-gray-700">Sale To Time</label>
                    <input type="time" wire:model.lazy="time_to" autocomplete="off" id=""
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
                    @if(!empty($report))
                    <a href="{{'sale-return-transaction/export'}}?salesman_id={{$salesman_id}}&from={{$from}}&to={{$to}}" target="_blank"
                       class="ml-3 disabled:opacity-30 bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none ">
                        Export.csv
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md ">

        <div class="flex flex-col">
            <div class="-my-2  sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow  border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center font-medium text-gray-900">Pharmacy Sales Return Transaction
                                Report</p>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sr #
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Status
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Return Date
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Original Sale Date
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Invoice #
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Patient
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Product Returned
                                </th>

                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Invoice Total Sale Value
                                </th>

                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Qty Returned
                                </th>

                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Qty Returned Value
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sale By
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Return By
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report as $h)
                                <tr>
                                    <td class="px-3 py-3 text-center  text-sm font-medium text-gray-500">
                                        {{ $loop->iteration  }}
                                    </td>
                                    <td title="Status" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                          Sales Return
                                        </span>
                                    </td>
                                    <td title="Return Date" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ date('d M, Y h:i A',strtotime($h['return_date'])) }}
                                    </td>
                                    <td title="Original Sale Date" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ date('d M, Y h:i A',strtotime($h['original_sale_date'])) }}
                                    </td>
                                    <td title="Invoice #" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        <a target="_blank" href="{{ url('pharmacy/sales/transaction/view').'/'.$h['invoice_no'] }}">
                                        {{ $h['invoice_no']  }}
                                    </td>
                                    <td title="Patient" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ !empty($h['patient_name']) ? $h['patient_name'] : 'Walk in' }}
                                        <br>
                                        {{ $h['mr_no'] }}
                                    </td>
                                    <td title="Product Returned" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ $h['product_name'] }}
                                    </td>
                                    <td title="Invoice Total Sale Value" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        PKR {{ number_format($h['original_invoice_total'],2) }}
                                    </td>
                                    <td title="Qty Returned" class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{ $h['refund_qty'] }}
                                    </td>
                                    <td title="Qty Returned Value" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        PKR {{ number_format($h['refund_value'],2) }}
                                    </td>

                                    <td title="Sale By" class="px-3 py-3 text-center  text-sm text-gray-500">
                                         {{ $h['sale_by'] }}
                                    </td>

                                    <td title="Return By" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ $h['return_by'] }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <th scope="col" colspan="8"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    {{ number_format(collect($report)->sum('refund_qty'),2) }}
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    PKR {{ number_format(collect($report)->sum('refund_value'),2) }}

                                </th>
                                <th scope="col" colspan="2"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                                </th>

                            </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<script>
    let from_date = new Pikaday({
        field: document.getElementById('from'),
        format: "DD MMM YYYY"
    });

    let to_date = new Pikaday({
        field: document.getElementById('to'),
        format: "DD MMM YYYY"
    });

    from_date.setDate(new Date('{{ $from }}'));
    to_date.setDate(new Date('{{ $to }}'));
</script>




