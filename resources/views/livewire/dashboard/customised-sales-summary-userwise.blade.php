<div class="bg-white pt-6 mt-2 shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-5 pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
            Sales Summary Salesman Wise
        </h2>
        <div class="mt-3 flex sm:mt-0 sm:ml-4">
            <table class="min-w-full divide-y divide-gray-200">
                <tr>
                    <td class="w-20">
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                  <button type="button" wire:click="changeType('date')"
                          class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium  hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 @if($type=='date') text-green-600 z-10 outline-none ring-1 ring-green-500 border-green-500 @else text-gray-900 @endif">
                    Day
                  </button>
                  <button type="button" wire:click="changeType('week')"
                          class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium  hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500  @if($type=='week') text-green-600 z-10 outline-none ring-1 ring-green-500 border-green-500 @else text-gray-900  @endif">
                    Week
                  </button>
                  <button type="button" wire:click="changeType('month')"
                          class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium  hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 @if($type=='month') text-green-600 z-10 outline-none ring-1 ring-green-500 border-green-500 @else text-gray-900 @endif">
                    Month
                  </button>
                </span>
                    </td>
                    <td>
                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                          <button type="button" wire:click="changeDate('prev')"
                                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="sr-only">Previous</span>
                              <!-- Heroicon name: solid/chevron-left -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                 fill="currentColor"
                                 aria-hidden="true">
                              <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd"/>
                            </svg>
                          </button>
                          <input type="text" wire:model="display_date" disabled
                                 class="relative inline-flex items-center text-center px-2 py-2 border border-l-0 border-gray-300 bg-white text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 focus:border-0">
                          <button type="button" wire:click="changeDate('next')"
                                  class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="sr-only">Next</span>
                              <!-- Heroicon name: solid/chevron-right -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                 fill="currentColor"
                                 aria-hidden="true">
                              <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"/>
                            </svg>
                          </button>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>


        <div class="p-10 " id="chartBarUserwise"></div>

    <!-- Chart bar -->
    <script>
        var labels = "{{$label_plucked}}";
        labels = JSON.parse(labels.replace(/&quot;/g, '"'));
        var results = "{{ $result }}";
        results = JSON.parse(results.replace(/&quot;/g, '"'));

        var options = {
            series: results,
            chart: {
                height: 350,
                type: 'line',

                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: false
                }
            },

            dataLabels: {
                enabled: true,


                formatter: function (val, opts) {
                    return  (new Intl.NumberFormat().format(val))
                },

            },
            stroke: {
                curve: 'smooth'
            },

            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns

                },
            },
            markers: {
                size: 1
            },
            xaxis: {
                categories: labels,

            },

            legend: {
                position: 'top',
                horizontalAlign: 'left',
                floating: true,
                offsetY: 0,
                offsetX: 60
            },
            yaxis: {
                yaxis: {
                    title: {
                        text: 'Temperature'
                    },
                    min: 5000,
                    max: 150000
                },
                labels: {
                    formatter: function (value) {
                        return "PKR " +(new Intl.NumberFormat().format(value))

                    }
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chartBarUserwise"), options);
        chart.render();
        window.addEventListener('userwise-sale', event => {
            var labels = event.detail.label;
            labels = JSON.parse(labels.replace(/&quot;/g, '"'));

            var results = event.detail.result;
            results = JSON.parse(results.replace(/&quot;/g, '"'));
console.log(results)
            chart.updateOptions({
                series: results,
                xaxis: {
                    type: 'category',
                    categories: labels,
                }
            })
        })
    </script>
</div>
