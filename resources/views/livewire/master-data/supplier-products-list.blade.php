<div>
    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/master-data/products') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Products List</span>
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
                                    <label for="salt"
                                           class="block text-sm font-medium text-gray-700">Generic/Salt</label>
                                    <input type="text" wire:model.defer="salt" id="salt" autocomplete="off"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <label for="manufacturer" class="block text-sm font-medium text-gray-700">Manufacturer</label>
                                    <select wire:model.defer="manufacturer_id"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            >
                                        <option value=""></option>
                                        @foreach($manufacturers as $i => $man)
                                            <option value="{{$man->id}}">{{$man->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                                    <select wire:model.defer="supplier_id"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            >
                                        <option value=""></option>
                                        @foreach($suppliers as $i => $sup)
                                            <option value="{{$sup->id}}">{{$sup->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select wire:model.defer="category_id"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                        <option value=""></option>
                                        @foreach($categories as $i => $cat)
                                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <label for="show_data" class="block text-sm font-medium text-gray-700">Show Data</label>
                                    <select wire:model.defer="show_data"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            id="show_data">
                                        <option value="">Both</option>
                                        <option value="f">Not Verified</option>
                                        <option value="t">Verified</option>
                                    </select>
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <button type="button" wire:click="search" wire:loading.attr="disabled"
                                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <div wire:loading wire:target="search">
                                            Searching...
                                        </div>
                                        <div wire:loading.remove wire:target="search">
                                            Search
                                        </div>
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

    <div>
        @if(!empty($all_products))
            <div class=" shadow sm:rounded-md sm:overflow-hidden">
                <!-- This example requires Tailwind CSS v2.0+ -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

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
                                            Generic/Salt
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                            Pieces in Packing
                                        </th>

                                        <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                            Supplier Cost
                                        </th>

                                        <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                            Retail Price
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                            Manufacturer
                                        </th>

                                        <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                            Supplier
                                        </th>

                                        <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                            Category
                                        </th>

                                        <th scope="col" class="relative px-3 py-3">
                                            <span class="sr-only">Verify</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($all_products as $key=> $m)
                                        <tr class="{{ !empty($m->type) ? 'bg-red-50':'' }}" wire:key="{{$loop->index}}">
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
                                                {{ $m['packing'] }}
                                            </td>

                                            <td class="px-3 py-3   text-sm text-gray-500">
                                                {{ number_format($m['cost_of_price'],2) }}
                                            </td>

                                            <td class="px-3 py-3   text-sm text-gray-500">
                                                {{ number_format($m['retail_price'],2) }}
                                            </td>
                                            <td class="px-3 py-3   text-sm text-gray-500">
                                                <label for="manufacturer" class="sr-only block text-sm font-medium text-gray-700">Manufacturer</label>
                                                <select wire:model="all_products.{{$key}}.manufacture_id"
                                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                        id="manufacturer">
                                                    <option value=""></option>
                                                    @foreach($manufacturers as $i => $man)
                                                        <option value="{{$man->id}}" wire:key="{{$loop->index}}">{{$man->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td class="px-3 py-3   text-sm text-gray-500">
                                                <label for="supplier" class="sr-only block text-sm font-medium text-gray-700">Supplier</label>
                                                <select wire:model="all_products.{{$key}}.supplier_id"
                                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                        id="supplier">
                                                    <option value=""></option>
                                                    @foreach($suppliers as $i => $sup)
                                                        <option value="{{$sup->id}}" wire:key="{{$loop->index}}">{{$sup->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td class="px-3 py-3   text-sm text-gray-500">
                                                <label for="category" class="sr-only block text-sm font-medium text-gray-700">Category</label>
                                                <select wire:model="all_products.{{$key}}.category_id"
                                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                        id="supplier">
                                                    <option value=""></option>
                                                    @foreach($categories as $i => $cat)
                                                        <option value="{{$cat->id}}" wire:key="{{$loop->index}}">{{$cat->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>


                                            <td class="px-3 py-3   text-right text-sm font-medium">
                                                <a href="javascript:void(0)" @if($m['narcotics'] == 'f') wire:click="verifyProduct('{{$key}}')" @endif>
                                                    @if($m['narcotics'] == 't')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Verified
                                            </span>
                                                    @else
                                                        <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Verify
                                            </span>
                                                    @endif
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        @endif
    </div>


</div>

