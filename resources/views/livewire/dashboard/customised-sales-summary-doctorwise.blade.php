<div class="bg-white pt-6 mt-6 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Sales Summary Doctor Wise </h2>
    </div>
    <div class="mt-6 flex flex-col">
        <div class="-my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr class="border-b">
                            <th scope="col" rowspan="2"
                                class="border-r w-20 px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                Doctor
                            </th>
                            @foreach($label as $l)
                                <th scope="col" colspan="2"
                                    class="@if(!$loop->last) border-r @endif px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    {{$l['label']}}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($label as $i=>$l)
                                <th scope="col"
                                    class="@if(count($label)!=$i++) border-r @endif px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Sale
                                </th>
                                <th scope="col"
                                    class="@if(count($label)!=$i++) border-r @endif px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    # Sales
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(collect($data)->sortBy('doctor')->groupBy('referred_by') as $key=>$d)
                            <!-- Odd row -->
                            <tr class="@if($loop->odd) bg-white @else bg-gray-50 @endif">
                                <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium @if(empty($d->first()['doctor'])) text-yellow-600  @else text-gray-900 @endif">
                                    {{!empty($d->first()['doctor']) ? $d->first()['doctor'] : 'External Doctors'}}
                                </td>
                                @foreach($label as $index=>$l)
                                    @php
                                        if ($type=='date'){
                                            $sale=collect($data)->where('referred_by',$key)->where('date',$l['format'])->first();
                                        }
                                        elseif ($type=='week'){
                                            $sale=collect($data)->where('referred_by',$key)->where('week',$l['format'])->first();
                                        }
                                        elseif ($type=='month'){
                                            $sale=collect($data)->where('referred_by',$key)->where('month',$l['format'])->first();
                                        }
                                    @endphp
                                    <td class="@if(count($label)!=$index++) border-r @endif font-medium text-center whitespace-nowrap text-sm @if(empty($d->first()['doctor'])) text-yellow-600  @else text-gray-800 @endif ">
                                        {{!empty($sale) ? number_format($sale['total_after_disc']-$sale['return_total'],2) : '-'}}
                                    </td>
                                    <td class="@if(count($label)!=$index++) border-r @endif font-medium text-center whitespace-nowrap text-sm @if(empty($d->first()['doctor'])) text-yellow-600  @else text-gray-800 @endif">
{{--                                        {{!empty($sale) ? number_format(($sale['total_after_disc']-$sale['return_total'])-($sale['cos']-$sale['return_cos']),2) : '-'}}--}}
                                        {{!empty($sale) ? number_format(($sale['no_of_sale'])) : '-'}}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
