<div class="bg-white pt-6 mt-2 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-5 pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
            Top Suppliers Payable
        </h2>

    </div>

    <div class="p-10 " id="charting"></div>

    <script>
        var labels = "{{$slabel}}";
        labels = JSON.parse(labels.replace(/&quot;/g, '"'));
        var results = "{{ $result }}";
        results = JSON.parse(results.replace(/&quot;/g, '"'));



        var options = {
            series: [{

                data: results
            }],
            chart: {
                type: 'bar',
                height: 363
            },
            plotOptions: {
                bar: {
                    barHeight: '100%',
                    distributed: true,
                    horizontal: true,
                    dataLabels: {
                        position: 'bottom'
                    },
                }
            },

            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#000']
                },
                formatter: function (val, opt) {
                    return   "PKR " + (new Intl.NumberFormat().format(val))
                },
                offsetX: 0,
                dropShadow: {
                    enabled: false
                }
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: labels,
                labels: {
                    formatter: function (value) {
                        return "PKR " +(new Intl.NumberFormat().format(value))

                    }
                },
            },
            yaxis: {
                show:true,

            },



            tooltip: {

                x: {
                    show: true
                },
                y: {
                    formatter: function (value) {
                        return "PKR " +(new Intl.NumberFormat().format(value))

                    },
                    title: {
                        formatter: function () {
                            return ''
                        }
                    }
                }
            }
        };

        var chartsin = new ApexCharts(document.querySelector("#charting"), options);
        chartsin.render();

    </script>

</div>
