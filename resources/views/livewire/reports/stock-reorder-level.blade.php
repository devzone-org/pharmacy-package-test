<div>
    <div class="mb-5 shadow sm:rounded-md ">
        <div class="flex flex-col">
            <div class="-my-2 sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Stock Reorder Level Report</p>
                            @if(!empty($report))
                                <p class="flex justify-end">
                                <a href="{{'stock-reorder-level/export'}}" target="_blank"
                                   class="ml-3 disabled:opacity-30 bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none ">
                                    Export.csv
                                </a>
                                </p>
                            @endif
                        </div>
                        <table class="min-w-full  divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                #
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Item
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Manufacturer
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Type
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Stock in Hand
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Reorder Level
                                </th>
                                <th scope="col" class="sticky top-0 z-10 px-3 py-3 text-center text-sm font-medium bg-gray-50 bg-opacity-75 text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8   ">

                                Reorder Qty
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report as $r)
                                <tr class="@if($r['stock_in_hand'] < $r['reorder_level']) bg-red-50 hover:bg-red-100 @else hover:bg-gray-50 @endif ">
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{$loop->iteration}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['item']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['manufacturer']) ? $r['manufacturer'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3  text-center text-sm text-gray-500">
                                        @if($r['type']=='s') Sound alike @elseif($r['type']=='l') Look alike @else - @endif
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['stock_in_hand']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['reorder_level']) ? $r['reorder_level'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['reorder_qty']) ?$r['reorder_qty'] :  '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <th scope="col" colspan="4"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-900">
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{number_format(collect($report)->sum('stock_in_hand'))}}
                                </th>
                                <th scope="col" colspan="2"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-900">
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
<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('focusInput', postId => {
            setTimeout(() => {
                document.getElementById('searchable_query').focus();
            }, 50);
        })
    });
</script>
