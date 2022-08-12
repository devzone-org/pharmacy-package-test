<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-10 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Product</label>
                    <input wire:model="product_name" readonly
                           wire:click="searchableOpenModal('product_id', 'product_name', 'product')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
                        <a href="{{'product-details/export'}}?product_id={{$product_id}}" target="_blank"
                           class="ml-3 disabled:opacity-30 bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none ">
                            Export.csv
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md">

        <div class="flex flex-col">
            <div class="-my-2  sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow  border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Product Details Report
                                Report</p>
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
                                    Product
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Supplier
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    PO #
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Qty
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    COP (PKR)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Retail Price (PKR)
                                </th>

                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                   Total Cost
                                </th>
                                <th scope="col"
                                    class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">
                                    Date
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @if(!empty($report))
                                @foreach($report as $r)
                                    <tr class="">
                                        <td class="px-3 py-3 text-center   text-sm font-medium text-gray-500">
                                            {{ $loop->iteration  }}
                                        </td>
                                        <td title="Products" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['product_name']}}
                                        </td>
                                        <td title="Products" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['s_name'] ?? '-'}}
                                        </td>
                                        <td title="Products" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['id']}}
                                        </td>
                                        <td title="Qty Sold" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['qty'])}}
                                        </td>
                                        <td title="Qty Returned" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['cost_of_price'],2)}}
                                        </td>
                                        <td title="Net Qty" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['retail_price'],2)}}
                                        </td>
                                        <td title="Net Qty" class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['total_cost'],2)}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{date('d M Y h:i:s',strtotime($r['created_at']))}}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50">
                                    <th colspan="4" scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{number_format(collect($report)->sum('qty'))}}
                                    </th>
                                    <th colspan="4" scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
{{--<script>--}}
{{--    let from_date = new Pikaday({--}}
{{--        field: document.getElementById('from'),--}}
{{--        format: "DD MMM YYYY"--}}
{{--    });--}}

{{--    let to_date = new Pikaday({--}}
{{--        field: document.getElementById('to'),--}}
{{--        format: "DD MMM YYYY"--}}
{{--    });--}}

{{--    from_date.setDate(new Date('{{ $from }}'));--}}
{{--    to_date.setDate(new Date('{{ $to }}'));--}}
{{--</script>--}}




