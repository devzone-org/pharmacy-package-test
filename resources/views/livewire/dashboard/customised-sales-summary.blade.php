<div class="bg-white pt-6 mt-6 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Sales Summary</h2>
    </div>
    <div class="mt-6 flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="border-r w-20 px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                &nbsp;
                            </th>
                            @foreach($label as $l)
                                <th scope="col"
                                    class="@if(!$loop->last) border-r @endif w-1/6 px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    {{$l['label']}}
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Odd row -->
                        <tr class="bg-white">
                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Sale (PKR)
                            </td>
                            @foreach($label as $l)
                                @php
                                if ($type=='date'){
                                    $sale=collect($data)->where('date',$l['format'])->first();
                                }
                                elseif ($type=='week'){
                                    $sale=collect($data)->where('week',$l['format'])->first();
                                }
                                elseif ($type=='month'){
                                    $sale=collect($data)->where('month',$l['format'])->first();
                                }
                                @endphp
                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">
                                    {{!empty($sale['total_after_disc']) ? number_format($sale['total_after_disc'],2) : '-'}}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Even row -->
                        <tr class="bg-gray-50">
                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Sales Return (PKR)
                            </td>
                            @foreach($label as $l)
                                @php
                                    if ($type=='date'){
                                        $sale=collect($data)->where('date',$l['format'])->first();
                                    }
                                    elseif ($type=='week'){
                                        $sale=collect($data)->where('week',$l['format'])->first();
                                    }
                                    elseif ($type=='month'){
                                        $sale=collect($data)->where('month',$l['format'])->first();
                                    }
                                @endphp
                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">
                                    {{!empty($sale['return_total']) ? number_format($sale['return_total'],2) : '-'}}
                                </td>
                            @endforeach
                        </tr>
                        <!-- Odd row -->
                        <tr class="bg-white">
                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Net Sale (PKR)
                            </td>
                            @foreach($label as $l)
                                @php
                                    if ($type=='date'){
                                        $sale=collect($data)->where('date',$l['format'])->first();
                                    }
                                    elseif ($type=='week'){
                                        $sale=collect($data)->where('week',$l['format'])->first();
                                    }
                                    elseif ($type=='month'){
                                        $sale=collect($data)->where('month',$l['format'])->first();
                                    }
                                    $net_sale=$sale['total_after_disc']-$sale['return_total'];
                                @endphp
                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">
                                    {{!empty($net_sale) ? number_format($net_sale,2) : '-'}}
                                </td>
                            @endforeach
                        </tr>
                        <!-- Even row -->
                        <tr class="bg-gray-50">
                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                COS (PKR)
                            </td>
                            @foreach($label as $l)
                                @php
                                    if ($type=='date'){
                                        $sale=collect($data)->where('date',$l['format'])->first();
                                    }
                                    elseif ($type=='week'){
                                        $sale=collect($data)->where('week',$l['format'])->first();
                                    }
                                    elseif ($type=='month'){
                                        $sale=collect($data)->where('month',$l['format'])->first();
                                    }
                                @endphp
                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">
                                    {{!empty($sale['cos']) ? number_format($sale['cos']-$sale['return_cos'],2) : '-'}}
                                </td>
                            @endforeach
                        </tr>
                        <!-- Odd row -->
                        <tr class="bg-white">
                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Gross Profit (PKR)
                            </td>
                            @foreach($label as $l)
                                @php
                                    if ($type=='date'){
                                        $sale=collect($data)->where('date',$l['format'])->first();
                                    }
                                    elseif ($type=='week'){
                                        $sale=collect($data)->where('week',$l['format'])->first();
                                    }
                                    elseif ($type=='month'){
                                        $sale=collect($data)->where('month',$l['format'])->first();
                                    }
                                    $profit=($sale['total_after_disc']-$sale['return_total'])-($sale['cos']-$sale['return_cos'])
                                @endphp
                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">
                                    {{!empty($profit) ? number_format($profit,2) : '-'}}
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
