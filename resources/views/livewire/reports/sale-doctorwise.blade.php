<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-10 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Doctor</label>
                    <select wire:model.defer="doctor"
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($doctors as $docto)
                            <option value="{{$docto['id']}}">{{$docto['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Department</label>
                    <select wire:model.defer="department"
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($departments as $departmen)
                            <option value="{{$departmen['id']}}">{{$departmen['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Date Range</label>
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

                <div class=" {{$date_range ? 'block ' : 'hidden '}} col-span-8 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                    <input type="text" wire:model.lazy="to" id="to" autocomplete="off" readonly
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
                    @if(!empty($report))

                        <a href="{{'sale-doctorwise/export'}}?doctor_id={{$doctor}}&department={{$department}}&from={{$from}}&to={{$to}}"
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
            <div class="-my-2 sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Sale Doctor wise
                                Report</p>
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
                                    Doctor
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Department
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sales (PKR)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Discount (PKR)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Sales Return (PKR)
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Net Sales (PKR)<br>
                                    (A)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    COS (PKR)<br>
                                    (B)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Gross Profit (PKR)
                                    <br>(A-B)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Gross Margin (%)<br>
                                    (A-B)/A
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    # of Sales
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @if(!empty($report))
                                @foreach($report as $r)
                                    <tr>
                                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                            {{ $loop->iteration  }}
                                        </td>
                                        <td title="Doctor" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{!empty($r['doctor_name']) ? $r['doctor_name'] : 'Walk in'}}
                                        </td>
                                        <td title="Department" class="px-3 py-3  text-center text-sm text-gray-500">
                                            {{!empty($r['department_name'])  ? $r['department_name'] : '-'}}
                                        </td>
                                        <td title="Sales (PKR)" class="px-3 py-3  text-center text-sm text-gray-500">
                                            {{number_format($r['total'],2)}}
                                        </td>
                                        <td title="Discount (PKR)" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            ({{number_format($r['total']-$r['total_after_disc'],2)}})
                                        </td>
                                        <td title="Sales Return (PKR)"
                                            class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['total_refund'],2)}}
                                        </td>
                                        <td title="Net Sales (PKR)"
                                            class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['total_after_refund'],2)}}
                                        </td>
                                        <td title="COS (PKR)" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['cos'],2)}}
                                        </td>
                                        <td title="Gross Profit (PKR)"
                                            class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['total_after_refund']-$r['cos'],2)}}
                                        </td>
                                        <td title="Gross Margin (%)"
                                            class="px-3 py-3 text-center  text-sm text-gray-500">
                                            @if(empty(round($r['total_after_refund'], 2)))
                                                0 %
                                            @else
                                                {{number_format((($r['total_after_refund']-$r['cos'])/round($r['total_after_refund'], 2)) * 100, 2)}}
                                                %
                                            @endif
                                        </td>
                                        <td title="# of Sales" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['no_of_sale']}}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50">
                                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('total'),2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        ({{number_format(collect($report)->sum('total')-collect($report)->sum('total_after_disc'),2)}}
                                        )
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('total_refund'),2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('total_after_refund'),2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('cos'),2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('total_after_refund')-collect($report)->sum('cos'),2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        @php
                                            $gross_margin=((collect($report)->sum('total_after_refund')-collect($report)->sum('cos'))/collect($report)->sum('total_after_refund'))*100;
                                        @endphp
                                        {{number_format($gross_margin,2)}} %
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('no_of_sale'))}}
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




