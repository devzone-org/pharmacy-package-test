<div class="bg-white pt-6 mt-6 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Top 10 Selling Products {{ucfirst($report_type)}}
            wise</h2>
    </div>
    <div class="mt-6 flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="border-r w-10 px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                &nbsp;#
                            </th>
                            <th scope="col"
                                class=" border-r px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col"
                                class=" border-r px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col"
                                class=" border-r px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                No of Sales
                            </th>
                            <th scope="col"
                                class=" border-r px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                Sale
                            </th>
                            <th scope="col"
                                class=" border-r px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                COS
                            </th>
                            <th scope="col"
                                class=" border-r px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                               Gross Profit
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Odd row -->
                        @foreach($data as $d)
                            <tr class="@if($loop->odd) bg-white @else bg-gray-50 @endif">
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$loop->iteration}}
                                </td>
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$d->name}}
                                </td>
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{!empty($d->supplier) ? $d->supplier : '-'}}
                                </td>
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{number_format($d->count_product)}}
                                </td>
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{number_format($d->total_after_refund,2)}}
                                </td>
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{number_format($d->cos,2)}}
                                </td>
                                <td class="border-r px-6 py-4 text-center whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{number_format($d->total_after_refund-$d->cos,2)}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
