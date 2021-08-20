<div class="bg-white pt-6 mt-6 shadow sm:rounded-md sm:overflow-hidden">
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

    <div class="overflow-hidden">
        <canvas class="p-10 " id="chartBarUserwise"></canvas>
    </div>
    <!-- Chart bar -->
    <script>
        var labels = "{{$label_plucked}}";
        labels = JSON.parse(labels.replace(/&quot;/g, '"'));
        var userWise = "{{ json_encode( $prepare_data)}}";
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
</div>
