<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="salesman" class="block text-sm font-medium text-gray-700">Salesman</label>
                    <select wire:model.defer="salesman_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($salemen as $saleman)
                            <option value="{{$saleman['id']}}">{{$saleman['name']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="doctor" class="block text-sm font-medium text-gray-700">Doctors</label>
                    <select wire:model="doctor_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                        @endforeach
                        <option value="walk">Walk in</option>
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

                <div class="{{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                    <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                    <input type="text" wire:model.lazy="from" autocomplete="off" id="from" readonly
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="{{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                    <input type="text" wire:model.lazy="to" autocomplete="off" id="to" readonly
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
                        <a href="{{'sale-transaction/export'}}?salesman_id={{$salesman_id}}&doctor_id={{$doctor_id}}&from={{$from}}&to={{$to}}"
                           target="_blank"
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
                    <div class="shadow border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <h3 class="text-md leading-6  text-center font-medium text-gray-900">Pharmacy Sales
                                Transaction
                                Report</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sr #
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Status
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sale Date
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Invoice #
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Doctor
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Patient
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sale (PKR)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Discount (PKR)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sale Return (PKR)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Net Sale (PKR)
                                    <br>
                                    (A)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Cash
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Credit
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    COS (PKR)
                                    <br>
                                    (B)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Gross Profit (PKR)<br> (A-B)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Gross Margin
                                    <br> (A-B)/A
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sold By
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report as $h)
                                <tr>
                                    <td title="" class="px-3 py-3 text-center  text-sm font-medium text-gray-500">
                                        {{ $loop->iteration  }}
                                    </td>
                                    <td title="Status" class="px-3 py-3 text-center  text-sm font-medium text-gray-500">

                                        @if( $h['is_credit'] == 't')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                On Credit
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                On Cash
                                            </span>
                                        @endif


                                        <br>
                                        @if($h['is_paid']=='t')
                                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Paid
                                            </span>
                                        @else

                                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Not Paid
                                            </span>

                                        @endif
                                    </td>
                                    <td title="Sale Date" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ date('d M, Y h:i A',strtotime($h['sale_at'])) }}
                                    </td>
                                    <td title="Invoice #" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        <a target="_blank"
                                           href="{{ url('pharmacy/sales/transaction/view').'/'.$h['id'] }}">
                                            {{ $h['id']  }}
                                        </a>
                                    </td>
                                    <td title="Doctor" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ !empty($h['doctor']) ? $h['doctor'] :'Walk in'  }}
                                    </td>
                                    <td title="Patient" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ !empty($h['patient_name']) ? $h['patient_name'] :'Walk in'  }}
                                    </td>
                                    <td title="Sale (PKR)" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ number_format($h['total'],2) }}
                                    </td>
                                    <td title="Discount (PKR)" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        ({{ number_format($h['total'] - $h['total_after_disc'],2) }})
                                    </td>
                                    <td title="Sale Return (PKR)" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        ({{ number_format($h['sale_return'],2) }})
                                    </td>
                                    <td title=" Net Sale (PKR)" class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{ number_format($h['total_after_disc']-$h['sale_return'],2) }}
                                    </td>
                                    <td title="Cash (PKR)" class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{ number_format($h['total_after_disc']-$h['sale_return'],2) }}
                                    </td>
                                    <td title="Credit (PKR)" class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{ number_format($h['total_after_disc']-$h['sale_return'],2) }}
                                    </td>
                                    <td title="COS (PKR)" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ number_format($h['cos'],2) }}
                                    </td>
                                    <td title="Gross Profit (PKR)" class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{number_format($h['total_after_disc']-$h['sale_return']-$h['cos'],2)}}
                                    </td>
                                    <td title="Gross Margin (%)" class="px-3 py-3  text-center text-sm text-gray-500">
                                        @php
                                            $total_after_disc=$h['total_after_disc']-$h['sale_return'];
                                            $total_after_disc=empty($total_after_disc) ? 1 : $total_after_disc
                                        @endphp

                                        {{number_format((($h['total_after_disc']-$h['sale_return']-$h['cos'])/$total_after_disc)*100,2)}}
                                        %
                                    </td>
                                    <td title="Sold By" class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{ $h['sale_by'] }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <th scope="col" colspan="6"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                </th>
                                <th title="Sale (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ number_format(collect($report)->sum('total'),2) }}
                                </th>
                                <th title="Discount (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    ({{ number_format(collect($report)->sum('total') - collect($report)->sum('total_after_disc'),2) }}
                                    )
                                </th>
                                <th title="Sale Return (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    ({{number_format(collect($report)->sum('sale_return'),2)}})
                                </th>
                                <th title="Net Sale (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ number_format(collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return'),2) }}
                                </th>

                                <th title="Cash (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ number_format(collect($report)->where('is_credit','f')->sum('total_after_disc')-collect($report)->where('is_credit','f')->sum('sale_return'),2) }}

                                </th>

                                <th title="Credit (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ number_format(collect($report)->where('is_credit','t')->sum('total_after_disc')-collect($report)->where('is_credit','t')->sum('sale_return'),2) }}

                                </th>
                                <th scope="col" title="COS (PKR)"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ number_format(collect($report)->sum('cos'),2) }}
                                </th>

                                <th title="Gross Profit (PKR)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{number_format(collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return')-collect($report)->sum('cos'),2)}}
                                </th>
                                @php
                                    $grand_total_after_disc=collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return');
                                       $grand_total_after_disc= empty($grand_total_after_disc) ? 1 :$grand_total_after_disc;
                                @endphp
                                <th title="Gross Margin (%)" scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{number_format(((collect($report)->sum('total_after_disc')-collect($report)->sum('sale_return')-collect($report)->sum('cos'))/$grand_total_after_disc)*100,2)}}
                                    %
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
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



