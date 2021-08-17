<div class="bg-white pt-6 mt-6 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Sales Summary Salesman Wise </h2>
    </div>
    <div class="overflow-hidden">
        <canvas class="p-10 " id="chartBarUserwise"></canvas>
    </div>
<!-- Chart bar -->
    <script>
        var labels = "{{$label_plucked}}";
        labels = JSON.parse(labels.replace(/&quot;/g, '"'));
        var userWise="{{ json_encode( $prepare_data)}}";
        userWise = JSON.parse(userWise.replace(/&quot;/g, '"'));
        const dataUserwise = {
            labels: labels,
            datasets: userWise
        };

        const configUserwise = {
            type: 'bar',
            data: dataUserwise,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 16,
                                weight: 'Bold',
                            }
                        }
                    },
                    title: {
                        display: false,
                        text: 'Bar Chart'
                    },

                }
            },
        };


        var chartBar = new Chart(
            document.getElementById('chartBarUserwise'),
            configUserwise
        );
    </script>
{{--    <div class="mt-6 flex flex-col">--}}
{{--        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">--}}
{{--            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">--}}
{{--                <div class="overflow-hidden border-t border-gray-200">--}}
{{--                    <table class="min-w-full divide-y divide-gray-200">--}}
{{--                        <thead class="bg-gray-50">--}}
{{--                        <tr class="border-b">--}}
{{--                            <th scope="col" rowspan="2"--}}
{{--                                class="border-r w-20 px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">--}}
{{--                                Salesmen--}}
{{--                            </th>--}}
{{--                            @foreach($label as $l)--}}
{{--                                <th scope="col" colspan="2"--}}
{{--                                    class="@if(!$loop->last) border-r @endif w-1/6 px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">--}}
{{--                                    {{$l['label']}}--}}
{{--                                </th>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            @foreach($label as $i=>$l)--}}
{{--                                <th scope="col"--}}
{{--                                    class="@if(count($label)!=$i++) border-r @endif w-1/6 px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">--}}
{{--                                    Sale--}}
{{--                                </th>--}}
{{--                                <th scope="col"--}}
{{--                                    class="@if(count($label)!=$i++) border-r @endif w-1/6 px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">--}}
{{--                                    Profit--}}
{{--                                </th>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @foreach(collect($data)->groupBy('sale_by') as $key=>$d)--}}
{{--                        <!-- Odd row -->--}}
{{--                        <tr class="@if($loop->odd) bg-white @else bg-gray-50 @endif">--}}
{{--                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">--}}
{{--                                {{$d->first()['user']}}--}}
{{--                            </td>--}}
{{--                            @foreach($label as $index=>$l)--}}
{{--                                @php--}}
{{--                                    if ($type=='date'){--}}
{{--                                        $sale=collect($data)->where('sale_by',$key)->where('date',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='week'){--}}
{{--                                        $sale=collect($data)->where('sale_by',$key)->where('week',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='month'){--}}
{{--                                        $sale=collect($data)->where('sale_by',$key)->where('month',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                @endphp--}}
{{--                                <td class="@if(count($label)!=$index++) border-r @endif font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($sale) ? number_format($sale['total_after_disc']-$sale['return_total'],2) : '-'}}--}}
{{--                                </td>--}}
{{--                                <td class="@if(count($label)!=$index++) border-r @endif font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($sale) ? number_format(($sale['total_after_disc']-$sale['return_total'])-($sale['cos']-$sale['return_cos']),2) : '-'}}--}}
{{--                                </td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        @endforeach--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
