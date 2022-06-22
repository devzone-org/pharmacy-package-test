<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-10 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Patient</label>
                    <input type="text" readonly wire:model.defer="patient_name" wire:click="searchPatient"
                           autocomplete="off"
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
                        <a href="{{'customer-receivables/export'}}?patient_id={{$patient_id}}" target="_blank"
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
            <div class="-my-2 sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow  border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Customer Receivables Report</p>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period till {{ date('d M, Y',strtotime('today')) }}</p>
                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sr #
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Customer
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                In Care Of
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Credit Limit
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Total Receivable
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @if(!empty($report))
                                @foreach($report as $r)
                                    <tr class="">
                                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                            {{ $loop->iteration  }}
                                        </td>
                                        <td title="Products" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['name']}}
                                        </td>
                                        <td title="Products" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['care_of']}}
                                        </td>
                                        <td title="Qty Sold" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['credit_limit'], 2)}}
                                        </td>

                                        <td title="Sale (PKR)" class="px-3 py-3  text-center text-sm text-gray-500">
                                            {{number_format($r['total_receivable'], 2)}}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50">
                                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        Grand Total
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('credit_limit'), 2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('total_receivable'), 2)}}
                                    </th>

                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pharmacy::include.searchable')
</div>
