<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="salesman" class="block text-sm font-medium text-gray-700">Salesman</label>
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
                @if($date_range)
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
                @endif
                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>

                    <button type="button" wire:click="resetSearch"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Sales Return Transaction
                                Report</p>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Sr #
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Return Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Original Sale Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Invoice #
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Patient
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Product Returned
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Invoice Total Sale Value
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Qty Returned
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Qty Returned Value
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report as $h)
                                <tr>
                                    <td class="px-3 py-3 text-center  text-sm font-medium text-gray-500">
                                        {{ $loop->iteration  }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ date('d M, Y h:i A',strtotime($h['return_date'])) }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ date('d M, Y h:i A',strtotime($h['sale_at'])) }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ $h['sale_id']  }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ !empty($h['patient_name']) ? $h['patient_name'] : 'Walk in' }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ $h['product_name'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        PKR {{ number_format($h['total'],2) }}
                                    </td>
                                    <td class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{ $h['refund_qty'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        PKR {{ number_format($h['return_total'],2) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <th scope="col" colspan="7" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    {{ number_format(collect($report)->sum('refund_qty'),2) }}
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    PKR {{ number_format(collect($report)->sum('return_total'),2) }}

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