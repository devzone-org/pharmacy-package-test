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

        labels = JSON.parse(labels.replace(/&quot;/g, '"'));
        {{--var sale ="{{collect($data)->pluck('total_after_disc')}}";--}}
        {{--sale = JSON.parse(sale.replace(/&quot;/g,'"'));--}}
        {{--var sale_refund ="{{collect($data)->pluck('return_total')}}";--}}
        {{--sale_refund = JSON.parse(sale_refund.replace(/&quot;/g,'"'));--}}
        var net_sale = "{{collect($data)->pluck('net_sale')}}";
        net_sale = JSON.parse(net_sale.replace(/&quot;/g, '"'));
        var cos = "{{collect($data)->pluck('net_cos')}}";
        cos = JSON.parse(cos.replace(/&quot;/g, '"'));
        var gross_profit = "{{collect($data)->pluck('net_cos')}}";
        gross_profit = JSON.parse(gross_profit.replace(/&quot;/g, '"'));
        const data = {
            labels: labels,
            datasets: [
                {
                    label: 'Sale',
                    data: net_sale,
                    borderColor: '#5bd6aa',
                    backgroundColor: '#5bd6aa',
                },
                {
                    label: 'COS',
                    data: cos,
                    borderColor: '#fcb37b',
                    backgroundColor: '#fcb37b',
                },
                {
                    label: 'Gross Profit',
                    data: gross_profit,
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
            document.getElementById('chartBar'),
            config
        );
    </script>
</div>
