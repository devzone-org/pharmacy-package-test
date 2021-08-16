<div class="bg-white pt-6 mt-6 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Sales Summary</h2>
    </div>
    <div class="overflow-hidden">
        <canvas class="p-10 " id="chartBar"></canvas>
    </div>
{{--    @dd(collect($data)->pluck('total_after_disc')->toArray())--}}
    <!-- Chart bar -->
    <script>
        const DATA_COUNT = 7;
        {{--const max={{collect($data)->max('total_after_disc')}};--}}
        // const NUMBER_CFG = {count: DATA_COUNT, min: 0, max: max};

        var labels = "{{$label_plucked}}";

        labels = JSON.parse(labels.replace(/&quot;/g,'"'));
        {{--var sale ="{{collect($data)->pluck('total_after_disc')}}";--}}
        {{--sale = JSON.parse(sale.replace(/&quot;/g,'"'));--}}
        {{--var sale_refund ="{{collect($data)->pluck('return_total')}}";--}}
        {{--sale_refund = JSON.parse(sale_refund.replace(/&quot;/g,'"'));--}}
        var net_sale ="{{collect($data)->pluck('net_sale')}}";
        net_sale = JSON.parse(net_sale.replace(/&quot;/g,'"'));
        var cos ="{{collect($data)->pluck('net_cos')}}";
        cos = JSON.parse(cos.replace(/&quot;/g,'"'));
        var gross_profit ="{{collect($data)->pluck('net_cos')}}";
        gross_profit = JSON.parse(gross_profit.replace(/&quot;/g,'"'));
        const data = {
            labels: labels,
            datasets: [
                // {
                //     label: 'Sale',
                //     data:sale,
                //     borderColor: 'rgb(255, 99, 132)',
                //     backgroundColor: 'rgb(255, 59, 100)',
                // },
                // {
                //     label: 'Sale Return',
                //     data:sale_refund,
                //     // data: Utils.numbers(NUMBER_CFG),
                //     borderColor: 'rgb(75, 192, 192)',
                //     backgroundColor: 'rgb(75, 159, 100)',
                // },
                {
                    label: 'Sale',
                    data:net_sale,
                    // data: Utils.numbers(NUMBER_CFG),
                    borderColor: '#5bd6aa',
                    backgroundColor: '#5bd6aa',
                },
                {
                    label: 'COS',
                    data:cos,
                    // data: Utils.numbers(NUMBER_CFG),
                    borderColor: '#fcb37b',
                    backgroundColor: '#fcb37b',
                },
                {
                    label: 'Gross Profit',
                    data:gross_profit,
                    // data: Utils.numbers(NUMBER_CFG),
                    borderColor: '#5dc2df',
                    backgroundColor: '#5dc2df',
                },

            ]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 16,
                                weight:'Bold',
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
            document.getElementById('chartBar'),
            config
        );
    </script>
{{--    <div class="mt-6 flex flex-col">--}}
{{--        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">--}}
{{--            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">--}}
{{--                <div class="overflow-hidden border-t border-gray-200">--}}
{{--                    <table class="min-w-full divide-y divide-gray-200">--}}
{{--                        <thead class="bg-gray-50">--}}
{{--                        <tr>--}}
{{--                            <th scope="col"--}}
{{--                                class="border-r w-20 px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">--}}
{{--                                &nbsp;--}}
{{--                            </th>--}}
{{--                            @foreach($label as $l)--}}
{{--                                <th scope="col"--}}
{{--                                    class="@if(!$loop->last) border-r @endif w-1/6 px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">--}}
{{--                                    {{$l['label']}}--}}
{{--                                </th>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        <!-- Odd row -->--}}
{{--                        <tr class="bg-white">--}}
{{--                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">--}}
{{--                                Sale (PKR)--}}
{{--                            </td>--}}
{{--                            @foreach($label as $l)--}}
{{--                                @php--}}
{{--                                if ($type=='date'){--}}
{{--                                    $sale=collect($data)->where('date',$l['format'])->first();--}}
{{--                                }--}}
{{--                                elseif ($type=='week'){--}}
{{--                                    $sale=collect($data)->where('week',$l['format'])->first();--}}
{{--                                }--}}
{{--                                elseif ($type=='month'){--}}
{{--                                    $sale=collect($data)->where('month',$l['format'])->first();--}}
{{--                                }--}}
{{--                                @endphp--}}
{{--                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($sale['total_after_disc']) ? number_format($sale['total_after_disc'],2) : '-'}}--}}
{{--                                </td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}

{{--                        <!-- Even row -->--}}
{{--                        <tr class="bg-gray-50">--}}
{{--                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">--}}
{{--                                Sales Return (PKR)--}}
{{--                            </td>--}}
{{--                            @foreach($label as $l)--}}
{{--                                @php--}}
{{--                                    if ($type=='date'){--}}
{{--                                        $sale=collect($data)->where('date',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='week'){--}}
{{--                                        $sale=collect($data)->where('week',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='month'){--}}
{{--                                        $sale=collect($data)->where('month',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                @endphp--}}
{{--                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($sale['return_total']) ? number_format($sale['return_total'],2) : '-'}}--}}
{{--                                </td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        <!-- Odd row -->--}}
{{--                        <tr class="bg-white">--}}
{{--                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">--}}
{{--                                Net Sale (PKR)--}}
{{--                            </td>--}}
{{--                            @foreach($label as $l)--}}
{{--                                @php--}}
{{--                                    if ($type=='date'){--}}
{{--                                        $sale=collect($data)->where('date',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='week'){--}}
{{--                                        $sale=collect($data)->where('week',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='month'){--}}
{{--                                        $sale=collect($data)->where('month',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    $net_sale=$sale['total_after_disc']-$sale['return_total'];--}}
{{--                                @endphp--}}
{{--                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($net_sale) ? number_format($net_sale,2) : '-'}}--}}
{{--                                </td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        <!-- Even row -->--}}
{{--                        <tr class="bg-gray-50">--}}
{{--                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">--}}
{{--                                COS (PKR)--}}
{{--                            </td>--}}
{{--                            @foreach($label as $l)--}}
{{--                                @php--}}
{{--                                    if ($type=='date'){--}}
{{--                                        $sale=collect($data)->where('date',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='week'){--}}
{{--                                        $sale=collect($data)->where('week',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='month'){--}}
{{--                                        $sale=collect($data)->where('month',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                @endphp--}}
{{--                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($sale['cos']) ? number_format($sale['cos']-$sale['return_cos'],2) : '-'}}--}}
{{--                                </td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        <!-- Odd row -->--}}
{{--                        <tr class="bg-white">--}}
{{--                            <td class="border-r px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">--}}
{{--                                Gross Profit (PKR)--}}
{{--                            </td>--}}
{{--                            @foreach($label as $l)--}}
{{--                                @php--}}
{{--                                    if ($type=='date'){--}}
{{--                                        $sale=collect($data)->where('date',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='week'){--}}
{{--                                        $sale=collect($data)->where('week',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    elseif ($type=='month'){--}}
{{--                                        $sale=collect($data)->where('month',$l['format'])->first();--}}
{{--                                    }--}}
{{--                                    $profit=($sale['total_after_disc']-$sale['return_total'])-($sale['cos']-$sale['return_cos'])--}}
{{--                                @endphp--}}
{{--                                <td class="@if(!$loop->last) border-r @endif px-6 py-4 font-medium text-center whitespace-nowrap text-sm text-gray-800">--}}
{{--                                    {{!empty($profit) ? number_format($profit,2) : '-'}}--}}
{{--                                </td>--}}
{{--                            @endforeach--}}
{{--                        </tr>--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
