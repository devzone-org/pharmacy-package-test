<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-10 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Product</label>
                    <input wire:model="product_name" readonly
                           wire:click="searchableOpenModal('product_id', 'product_name', 'product')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <input wire:model="supplier_name" readonly
                           wire:click="searchableOpenModal('supplier_id','supplier_name','supplier')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Manufacturer</label>
                    <input wire:model="manufacture_name" readonly
                           wire:click="searchableOpenModal('manufacture_id','manufacture_name','manufacture')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Rack</label>
                    <input wire:model="rack_name" readonly
                           wire:click="searchableOpenModal('rack_id','rack_name','rack')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <input wire:model="category_name" readonly
                           wire:click="searchableOpenModal('category_id','category_name','category')"
                           type="text"
                           autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label  class="block text-sm font-medium text-gray-700">Type</label>
                    <select wire:model="type"
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        <option value="s">Sound alike</option>
                        <option value="l">Look alike</option>
                    </select>
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="from" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="date" wire:model.defer="expiry_date" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search" wire:loading.attr="disabled"
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

    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Stock Near Expiry Report</p>
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
                                    PO #
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Manufacturer
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Category
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Rack
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                                    Supplier
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Type
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Stock in Quantity
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Expiry Date
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Expiring In
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                                    Last Sold
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report as $r)
                                <tr>
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{$loop->iteration}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['item']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['po_id']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['manufacturer'])  ? $r['manufacturer'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{!empty($r['category']) ? $r['category'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3  text-center text-sm text-gray-500">
                                        {{!empty($r['rack']) ? $r['rack'] : '-'}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['supplier']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        @if($r['type']=='s') Sound alike @elseif($r['type']=='l') Look alike @else - @endif
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{$r['stock_in_hand']}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['expiry']) ? date('d M Y',strtotime($r['expiry'])) : 'Not Defined'}}
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        @if($r['expired'])
                                            <span class="text-red-600"> Already Expired</span>
                                        @else
                                            {{$r['expiring_in']}}
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                        {{!empty($r['last_sold']) ? date('d M Y',strtotime($r['last_sold'])) : '-'}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <th scope="col" colspan="8"
                                    class="px-3 py-3 text-left text-sm font-medium text-gray-900">
                                </th>
                                <th scope="col"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    {{number_format(collect($report)->sum('stock_in_hand'))}}
                                </th>
                                <th scope="col" colspan="3"
                                    class="px-3 py-3 text-center text-sm font-medium text-gray-900">
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
