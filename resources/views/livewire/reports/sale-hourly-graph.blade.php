<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-6 gap-6">
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
                @if($date_range)
                    <div class="col-span-8 sm:col-span-2">
                        <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                        <input type="date" wire:model.defer="from" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="col-span-8 sm:col-span-2">
                        <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                        <input type="date" wire:model.defer="to" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                @endif
                <div class="col-span-6 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3 shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 sm:p-6 ">
            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Hourly Sale Graph</p>
            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
        </div>
        <div class="p-4 bg-white" id="chart"></div>
    </div>




</div>
<script>
    let labels = ("{{ json_encode($labels) }}");
    labels = JSON.parse(labels.replace(/&quot;/g, '"'));
    let chartData = (JSON.parse("{{ json_encode($char_data) }}"));
    let chartDataValue = (JSON.parse("{{ json_encode($char_data_value) }}"));

    var options = {
        series: [{
            name: 'Sale Value',
            type: 'column',
            data: chartDataValue
        }, {
            name: 'No. of Sale',
            type: 'line',
            data: chartData
        }],
        chart: {
            height: 450,
            type: 'line',
        },
        stroke: {
            width: [0, 6]
        },

        dataLabels: {
            enabled: true,
            enabledOnSeries: [1]
        },
        labels: labels,
        xaxis: {

        },
        yaxis: [{
            title: {
                text: 'Sale Value',
            },
            labels: {
                formatter: function (value) {
                    return "PKR " +(new Intl.NumberFormat().format(value))

                }
            },

        }, {
            opposite: true,
            title: {
                text: 'No. of Sale'
            }
        }]
    };

    var charthr = new ApexCharts(document.querySelector("#chart"), options);
    charthr.render();


    window.addEventListener('hour-summary', event => {
        var vol = event.detail.vol;


        var val = event.detail.val;


        charthr.updateOptions({
            series: [{
                name: 'Sale Value',
                type: 'column',
                data: vol
            }, {
                name: 'No. of Sale',
                type: 'line',
                data: val
            }]

        })
    })

</script>
