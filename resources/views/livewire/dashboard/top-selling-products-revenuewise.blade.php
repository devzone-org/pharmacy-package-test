<div class="bg-white    shadow sm:rounded-md sm:overflow-hidden">


    <div class="p-6 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
            Top 5 Selling Products - {{ucfirst($report_type)}}
            wise
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


    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Product
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Supplier
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                # Sales
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Sale
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                COS
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                G.Profit
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $d)
            <tr class="@if($loop->even)  bg-gray-50 @endif hover:bg-gray-200">

                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{$d->name}}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{!empty($d->supplier) ? $d->supplier : '-'}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{number_format($d->count_product)}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{number_format($d->total_after_refund,2)}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{number_format($d->cos,2)}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{number_format($d->total_after_refund-$d->cos,2)}}
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
