<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" wire:model.defer="date" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>
    <section aria-labelledby="announcements-title">
        <div class="rounded-lg bg-white overflow-hidden shadow">
            <div x-data="app()" x-cloak class="">
                <div class="max-w-full mx-auto">
                    <div class="shadow p-6 rounded-lg bg-white">
                        <div class="md:flex md:justify-between md:items-center">
                            <div>
                                <h2 class="text-xl text-gray-800 font-bold leading-tight">Hourly Sales Value Graph</h2>
                                <h3 class="mb-2 text-gray-900 text-base font-bold">{{date('d F Y',strtotime($date))}}</h3>
                            </div>
                            <!-- Legends -->
                            <div class="mb-4">
                            </div>
                        </div>
                        <div class="line my-8 relative">
                            <!-- Tooltip -->
                            <template x-if="tooltipOpen == true">
                                <div x-ref="tooltipContainer"
                                     class="p-0 m-0 z-10 shadow-lg rounded-lg absolute h-auto block"
                                     :style="`bottom: ${tooltipY}px; left: ${tooltipX}px`">
                                    <div class="shadow-xs rounded-lg bg-white p-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <div>Sales Value (PKR):</div>
                                            <div class="font-bold ml-2">
                                                <span x-html="tooltipContent"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <!-- Bar Chart -->
                            <div class="flex -mx-2 items-end mb-2">
                                @php
                                    $max=max($char_data_value);
                                    if ($max==0){
                                        $max=1;
                                    }
                                @endphp
                                @foreach($char_data_value as $cd)
                                    <div class="px-4" style="width: 4.12%">
                                        <div style="height: {{(int)(($cd/$max)*200)}}px;width:10px"
                                             class="transition ease-in duration-200 bg-blue-600 hover:bg-blue-400 relative"
                                             @mouseenter="showTooltip($event); tooltipOpen = true"
                                             @mouseleave="hideTooltip($event)">
                                            <div class="text-center absolute top-0 -left-1 right-0 -mt-6 text-gray-800 text-sm">
                                                {{number_format($cd)}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{--                            <!-- Labels -->--}}
                            <div class="border-t border-gray-400 mx-auto"
                                 :style="`height: 1px; width: ${ 100 - 1/chartData.length*100 + 1}%`"></div>
                            <div class="flex -mx-2 items-end">
                                <template x-for="data in labels">
                                    <div class="px-4" style="width: 4.12%">
                                        <div class="bg-red-600 relative">
                                            <div class="text-center absolute top-0 left-0 right-0 h-2 -mt-px bg-gray-400 mx-auto"
                                                 style="width: 1px"></div>
                                            <div x-text="data" style="width:10px"
                                                 class="text-center absolute top-0 -left-1 right-0 mt-3 text-gray-700 text-sm"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-3" aria-labelledby="announcements-title">
        <div class="rounded-lg bg-white overflow-hidden shadow">
            <div x-data="app()" x-cloak class="">
                <div class="max-w-full mx-auto">
                    <div class="shadow p-6 rounded-lg bg-white">
                        <div class="md:flex md:justify-between md:items-center">
                            <div>
                                <h2 class="text-xl text-gray-800 font-bold leading-tight">Hourly Sales Invoices
                                    Graph</h2>
                                <h3 class="mb-2 text-gray-900 text-base font-bold">{{date('d F Y',strtotime($date))}}</h3>
                            </div>
                            <!-- Legends -->
                            <div class="mb-4">
                            </div>
                        </div>
                        <div class="line my-8 relative">
                            <!-- Tooltip -->
                            <template x-if="tooltipOpen == true">
                                <div x-ref="tooltipContainer"
                                     class="p-0 m-0 z-10 shadow-lg rounded-lg absolute h-auto block"
                                     :style="`bottom: ${tooltipY}px; left: ${tooltipX}px`">
                                    <div class="shadow-xs rounded-lg bg-white p-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <div>No of Invoices:</div>
                                            <div class="font-bold ml-2">
                                                <span x-html="tooltipContent"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <!-- Bar Chart -->
                            <div class="flex -mx-2 items-end mb-2">
                                @php
                                    $max2=max($char_data);
                                if ($max2==0){
                                    $max2=1;
                                }
                                @endphp
                                @foreach($char_data as $cd)
                                    <div class="px-4" style="width: 4.12%">
                                        <div style="height: {{(int)(($cd/$max2)*5)}}px;width:10px"
                                             class="transition ease-in duration-200 bg-blue-600 hover:bg-blue-400 relative"
                                             @mouseenter="showTooltip($event); tooltipOpen = true"
                                             @mouseleave="hideTooltip($event)">
                                            <div class="text-center absolute top-0 -left-1 right-0 -mt-6 text-gray-800 text-sm">
                                                {{$cd}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{--                            <!-- Labels -->--}}
                            <div class="border-t border-gray-400 mx-auto"
                                 :style="`height: 1px; width: ${ 100 - 1/chartData.length*100 + 1}%`"></div>
                            <div class="flex -mx-2 items-end">
                                <template x-for="data in labels">
                                    <div class="px-4" style="width: 4.12%">
                                        <div class="bg-red-600 relative">
                                            <div class="text-center absolute top-0 -left-0.5 right-0 h-2 -mt-px bg-gray-400 mx-auto"
                                                 style="width: 1px"></div>
                                            <div x-text="data" style="width:10px"
                                                 class="text-center absolute top-0 -left-1 right-0 mt-3 text-gray-700 text-sm"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<script>
    let labels = ("{{ json_encode($labels) }}");
    labels = JSON.parse(labels.replace(/&quot;/g, '"'));
    let chartData = (JSON.parse("{{ json_encode($char_data) }}"));
    let chartDataValue = (JSON.parse("{{ json_encode($char_data_value) }}"));

    function app() {
        return {
            chartData: chartData,
            chartDataValue: chartDataValue,
            labels: labels,
            tooltipContent: '',
            tooltipOpen: false,
            tooltipX: 0,
            tooltipY: 0,
            showTooltip(e) {
                console.log(e);
                this.tooltipContent = e.target.textContent
                this.tooltipX = e.target.offsetLeft - e.target.clientWidth;
                this.tooltipY = e.target.clientHeight + e.target.clientWidth;
            },
            hideTooltip(e) {
                this.tooltipContent = '';
                this.tooltipOpen = false;
                this.tooltipX = 0;
                this.tooltipY = 0;
            }
        }
    }

</script>
