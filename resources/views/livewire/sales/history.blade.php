<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Receipt #</label>
                    <input type="text" wire:model.defer="receipt" id="receipt" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                    <input type="date" wire:model.defer="from" id="from" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                    <input type="date" wire:model.defer="to" id="to" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>

                    <button type="button" wire:click="resetSearch"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Sale History</h3>
                                <a href="{{ url("pharmacy/sales/add") }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Sale
                                </a>
                            </div>


                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    #
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Receipt #
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Sub Total
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Discount
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Gross Total
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Sale at
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Referred by
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Status
                                </th>
                                <th scope="col" class="relative px-3 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($history as $h)
                                <tr>
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration + ( $history->firstItem() - 1)   }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $h->id }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->sub_total,2) }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->sub_total - $h->gross_total,2) }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->gross_total,2) }}
                                    </td>


                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ date('d M, y h:i A') }} <br>
                                        {{ $h->sale_by }}
                                    </td>


                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $h->referred_by }} <br> {{ $h->patient_name }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if($h->is_refund == 'f')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                          Sale
                                        </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                          Refund
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3   text-right text-sm font-medium">
                                        <div class="flex justify-between">
                                            <a target="_blank" href="{{ url('pharmacy/sales/view/') }}/{{$h->id}}">
                                            <svg title="View Sale" class="text-indigo-600 cursor-pointer w-4 h-4"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            </a>
                                            <a target="_blank" href="{{ url('pharmacy/sales/refund/') }}/{{$h->id}}">
                                            <svg title="Refund Sale" class="text-red-600 cursor-pointer w-4 h-4"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                                            </svg>
                                            </a>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        @if($history->hasPages())
                            <div class="bg-white border-t px-3 py-2">
                                {{ $history->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
