<div>
    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/purchases/stock-adjustment') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Adjustment History</span>
        </h3>
    </div>


        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 flex   justify-between items-center  px-4  sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Stock Adjustment</h3>
                    <p class="mt-1 text-sm text-gray-500">Here you can increase or decrease the stock.</p>
                </div>
                <div class="  ">
                    <button type="button"
                            wire:click="searchableOpenModal('product_id', 'product_name', 'adjustment_items')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search Items
                    </button>
                </div>

            </div>
            @if(!empty($adjustments))
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Packing
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Available Qty
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expiry
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Indicator
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Qty
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($adjustments as $key => $a)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['item'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['packing'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['qty'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['expiry'] }}
                            </td>

                            <td class=" px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <select name="" wire:model="adjustments.{{ $key }}.indicator"
                                        class=" w-48  px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <option value="i">Increase</option>
                                    <option value="d">Decrease</option>
                                </select>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                                <input type="number" wire:model.debounce="adjustments.{{ $key }}.a_qty"
                                       class="  w-48  px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                <svg wire:click="removeItem('{{ $key }}')" class="w-5 h-5 text-red-700 cursor-pointer"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="button" wire:click="proceed" wire:loading.attr="disabled"
                            class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Proceed
                    </button>
                </div>

            @endif
        </div>

    <div x-data="{ open: @entangle('show_model') }" x-cloak x-show="open"
         class="fixed z-40 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-description="Background overlay, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
            <div @click.away="open = false;" x-show="open" x-description="Modal panel, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg  text-left overflow-hidden shadow-xl transform transition-all
              sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full  "
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class="   px-4 py-3  ">

                    @if(!empty($error))
                        <div class="rounded-md mb-5 bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">

                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ $error }}
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" wire:click="$set('error','')"
                                                class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                            <span class="sr-only">Dismiss</span>
                                            <!-- Heroicon name: solid/x -->
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                 fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="text-red-600 text-lg">Please confirm that the inventory below is correct.</p>

                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col"
                            class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>

                        <th scope="col"
                            class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Current Qty
                        </th>

                        <th scope="col"
                            class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Indicator
                        </th>

                        <th scope="col"
                            class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            After
                        </th>


                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($adjustments as $key => $a)
                        <tr class="{{ $loop->even? 'bg-gray-50':'' }}">
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['item'] }}
                            </td>

                            <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['qty'] }}
                            </td>


                            <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-500">
                                {{ $a['indicator'] == 'i'? 'Increase':'Decrease' }} By {{ $a['a_qty'] }}
                            </td>

                            <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-500">
                                {{$a['indicator'] == 'i' ? ($a['a_qty'] + $a['qty']) :($a['qty'] - $a['a_qty'])  }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="5">
                            <label for="" class="p-2 underline">Remarks*</label>
                            <textarea name="" cols="30" rows="5"  wire:model.defer="remarks" class="p-2 focus:ring-0 block w-full border-0 text-md resize-none h-40  "></textarea>
                        </th>
                    </tr>
                    </tbody>
                </table>

                <div class="px-4 py-3    border-t">
                    <button type="button" wire:click="confirm" wire:loading.attr="disabled"
                            class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Confirm and Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>


    @include('pharmacy::include.searchable')

</div>

<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('focusInput', postId => {
            setTimeout(() => {
                document.getElementById('searchable_query').focus();
            }, 50);
        });

        Livewire.on('focusProductInput', postId => {

            setTimeout(() => {
                document.getElementById('search_products').focus();
            }, 200);
        })
    });
</script>
