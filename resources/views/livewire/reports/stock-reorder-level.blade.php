<div>
    <form wire:submit.prevent="search()">
        <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                <div class="grid grid-cols-10 gap-6">
                    <div class="col-span-6 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Product</label>
                        <input wire:model.defer="product_name" readonly
                               wire:click="searchableOpenModal('product_id', 'product_name', 'product')"
                               type="text"
                               autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="col-span-6 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Manufacturer</label>
                        <input wire:model.defer="manufacture_name" readonly
                               wire:click="searchableOpenModal('manufacture_id','manufacture_name','manufacture')"
                               type="text"
                               autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="col-span-6 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Show By</label>
                        <select wire:model.defer="type"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="all">All</option>
                            <option value="reorder_level">Reorder Level greater than zero.</option>
                        </select>
                    </div>


                    <div class="col-span-8 sm:col-span-2">
                        <button type="submit"
                                class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <div wire:loading wire:target="search">
                                Searching ...
                            </div>
                            <div wire:loading.remove wire:target="search">
                                Search
                            </div>
                        </button>
                        <button type="button" wire:click="resetSearch" wire:loading.attr="disabled"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Stock Reorder Level
                                Report</p>
                        </div>
                        <table class="min-w-full  divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-900   ">
                                    #
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Item
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Manufacturer
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Type
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Stock in Hand
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Reorder Level
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Reorder Qty
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report as $r)
                                <tr class="@if($r['stock_in_hand'] < $r['reorder_level']) bg-red-50 hover:bg-red-100 @else hover:bg-gray-50 @endif ">
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{$loop->iteration}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['item']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['manufacturer']) ? $r['manufacturer'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3  text-center text-sm text-gray-500">
                                        @if($r['type']=='s') Sound alike @elseif($r['type']=='l') Look alike @else
                                            - @endif
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['stock_in_hand']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['reorder_level']) ? $r['reorder_level'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['reorder_qty']) ?$r['reorder_qty'] :  '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <th scope="col" colspan="4"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-900">
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{number_format(collect($report)->sum('stock_in_hand'))}}
                                </th>
                                <th scope="col" colspan="2"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-900">
                                </th>
                            </tr>
                            </tbody>
                        </table>


                    </div>
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
        })
    });
</script>
