<div>
    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/purchases') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Purchase Orders</span>
        </h3>
    </div>
    <form wire:submit.prevent="create">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Purchase Order</h3>

                </div>

                @include('pharmacy::include.errors')
                @if(!empty($success))
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <!-- Heroicon name: check-circle -->
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ $success }}
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" wire:click="$set('success', '')"
                                            class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                        <span class="sr-only">Dismiss</span>
                                        <!-- Heroicon name: x -->
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20"
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

                <div class="grid grid-cols-6 gap-6">


                        <div class="col-span-6 sm:col-span-2">
                            <label for="podate" class="block text-sm font-medium text-gray-700">PO Edited Date</label>
                            <input value="{{ date('Y-m-d') }}" readonly type="date" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   id="podate">
                        </div>


                        <div class="col-span-6 sm:col-span-2">
                            <label for="created_by" class="block text-sm font-medium text-gray-700">Edited By</label>
                            <input value="{{ Auth::user()->name }}" readonly type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">PO Status</label>
                            <input value="Draft" readonly type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                        </div>



                        <div class="col-span-6 sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input wire:model="supplier_name" readonly
                               wire:click="searchableOpenModal('supplier_id','supplier_name','supplier')" type="text"
                               autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="name">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="date" class="block text-sm font-medium text-gray-700">Expected Delivery Date</label>
                        <input wire:model="expected_date" type="date" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="date">
                    </div>

                    @if(!empty($supplier_id))
                        <div class="col-span-6 sm:col-span-2">
                            <div class="flex justify-between">
                                <label for="sale_days" class="block text-sm font-medium text-gray-700">Sales Of
                                    Days</label>
                                <a href="javascript:void(0);" class="block text-sm font-medium text-indigo-700"
                                   wire:click="inDemand" wire:loading.attr="disabled">+ Supplier's
                                    Products</a>
                            </div>
                            <input type="number" autocomplete="off" wire:model="sale_days" id="sale_days"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            {{--                            <select type="text" wire:model="sale_days" id="sale_days"--}}
                            {{--                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">--}}
                            {{--                                <option value="10">Last 10</option>--}}
                            {{--                                <option value="20">Last 20</option>--}}
                            {{--                                <option value="30">Last 30</option>--}}
                            {{--                            </select>--}}
                        </div>
                    @endif
                    <div class="col-span-6 sm:col-span-2">
                        <label for="date" class="block text-sm font-medium text-gray-700"> Loose Purchase
                        </label>
                        <select wire:model.lazy="loose_purchase" type="text" autocomplete="off"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="f">No </option>
                            <option value="t">Yes </option>
                        </select>
                    </div>





                    <div class="col-span-6">
                        <p class="text-indigo-600 font-bold cursor-pointer inline-block"
                           wire:click="openProductModal">+ Add Products</p>
                    </div>
                </div>


            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        #
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Name
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Generic
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Qty
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Pieces in Packing
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Total Qty
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Closing Inventory
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Supplier Cost
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        Retail Price
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        Total Cost
                    </th>
                    <th scope="col" class="relative px-3 py-3">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order_list as $key => $m)
                    @php
                   $closing= \Devzone\Pharmacy\Models\ProductInventory::where('product_id',$m['id'])->where('qty','>','0')->sum('qty');
                    @endphp

                    <tr>
                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['name'] }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['salt'] }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            <input type="number"  onclick="this.select()"  class="block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="order_list.{{$key}}.qty">
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['packing'] }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            @if($loose_purchase == 't')
                                {{number_format($m['qty'],2)}}
                            @else
                                {{ number_format($m['packing']*$m['qty'],2) }}
                            @endif
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ number_format($closing,2) }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            <input type="number" step="0.01" onclick="this.select()"  class="block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="order_list.{{$key}}.cost_of_price">

                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            <input type="number" step="0.01" onclick="this.select()"  class="block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="order_list.{{$key}}.retail_price">

                        </td>


                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ number_format($m['total_cost'],2) }}
                        </td>


                        <td class="px-3 py-3 w-7   text-right text-sm font-medium">
                            <svg wire:click="removeProduct('{{ $key }}')" wire:loading.attr="disabled" class="w-6 h-6 text-red-600 cursor-pointer hover:text-red-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </td>
                    </tr>
                @endforeach
                <tr class="bg-gray-50 border-b">
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Total
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        {{ number_format(collect($order_list)->sum('qty'),2) }}
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>  <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        {{ number_format(collect($order_list)->sum('cost_of_price'),2) }}
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        {{ number_format(collect($order_list)->sum('retail_price'),2) }}
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        {{ number_format(collect($order_list)->sum('total_cost'),2) }}
                    </th>
                    <th scope="col" class="relative px-3 py-3">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t">
                <button type="submit"
                        class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    <div wire:loading wire:target="create">
                        Updating ...
                    </div>
                    <div wire:loading.remove wire:target="create">
                        Update
                    </div>
                </button>
            </div>
        </div>
    </form>


    <div x-data="{ open: @entangle('products_modal') }" x-cloak x-show="open"
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
                 class="h-1/3 inline-block align-bottom bg-white rounded-lg  text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full  "
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class="  px-2 pt-2 pb-2">


                    <div class="">
                        <label for="status"
                               class="block text-sm font-medium text-gray-600">Browse Products</label>
                        <input type="text"
                               wire:model.debounce.500ms="search_products"
                               wire:keydown.arrow-up="decrementHighlight"
                               wire:keydown.arrow-down="incrementHighlight"
                               wire:keydown.enter="selectProduct"
                               wire:keydown.escape="$set('products_modal',false)"
                               id="search_products"
                               class="shadow-sm mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               autocomplete="off">


                    </div>


                </div>

                @if(!empty($product_data))
                    <table class="mt-3 min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>

                            <th scope="col"
                                class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                #
                            </th>

                            <th scope="col"
                                class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                Name
                            </th>

                            <th scope="col"
                                class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                Generic
                            </th>

                            <th scope="col"
                                class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                Pieces in Packing
                            </th>


                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product_data as $key=> $a)
                            <tr class="hover:bg-indigo-600 hover:text-white cursor-pointer  {{ $highlight_index === $key ? 'bg-indigo-600 text-white' : ' text-gray-500' }}"
                                wire:click="selectProduct('{{ $key }}')">

                                <td class="px-2 py-2 whitespace-nowrap text-sm ">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-2 py-2 whitespace-nowrap text-sm ">
                                    {{ $a['name'] }}
                                </td>

                                <td class="px-2 py-2 whitespace-nowrap text-sm ">
                                    {{ $a['salt'] }}
                                </td>

                                <td class="px-2 py-2 whitespace-nowrap text-sm ">
                                    {{ $a['packing'] }}
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    @if(strlen($search_products) < 2)
                        <p class="text-sm opacity-25 pt-0 p-3 ">Please enter {{ 2 - strlen($search_products) }}
                            or more
                            {{ (2 - strlen($search_products)) > 1 ? 'characters' : 'character' }}</p>
                    @else
                        <p class="text-sm opacity-25 pt-0 p-3">{{ empty($product_data) ? 'No Record Found': '' }}</p>
                    @endif
                @endif
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
