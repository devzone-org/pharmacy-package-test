<div>
    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/master-data') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Master Data</span>
        </h3>
    </div>

    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                            <div class="grid grid-cols-8 gap-6">
                                <div class="col-span-8 sm:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" wire:model.defer="name" name="name" id="name" autocomplete="off"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <label for="salt" class="block text-sm font-medium text-gray-700">Generic</label>
                                    <input type="text" wire:model.defer="salt" id="salt" autocomplete="off"
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
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Product List</h3>
                                <div class="flex justify-between items-center space-x-2">
                                    <a href="{{ url('pharmacy/master-data/products/add') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Add new Product
                                    </a>
                                    <a href="{{ url('pharmacy/master-data/products/supplier') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Supplier Products
                                    </a>
                                </div>

                            </div>


                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    #
                                </th>
                                <th wire:click="sortBy('p.name')" scope="col"
                                    class="flex items-center cursor-pointer px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    @if($sortDirection=='asc')
                                        <svg class="w-4 h-4 {{ $sortBy=='p.name' ? 'text-gray-600':'text-gray-400' }}"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 {{ $sortBy=='p.name' ? 'text-gray-600':'text-gray-400' }}"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                        </svg>
                                    @endif
                                    Name
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Generic
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Pieces in Packing
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Barcode
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Supplier Cost
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Retail Price
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Manufacture
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Category
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Rack
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
                            @foreach($products as $m)
                                <tr class="{{ !empty($m->type) ? 'bg-red-50':'' }}">
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->name }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->salt }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->packing }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->barcode }}
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($m->cost_of_price,2) }}
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($m->retail_price,2) }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->m_name }}
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->c_name }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->r_name }} - {{ $m->tier }}
                                    </td>


                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if($m->status == 't')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
  Active
</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
  Inactive
</span>
                                        @endif
                                    </td>


                                    <td class="px-3 py-3   text-right text-sm font-medium">
                                        <a href="{{ url('pharmacy/master-data/products/edit/') }}/{{$m->id}}"
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                        @if($products->hasPages())
                            <div class="bg-white p-3 border-t">
                                {{ $products->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>

