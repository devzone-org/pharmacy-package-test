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
            <span class="ml-4">Product List</span>
        </h3>
    </div>


    <form wire:submit.prevent="create">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add Product</h3>

                </div>

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <!-- Heroicon name: x-circle -->
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    @php
                                        $count = count($errors->all());
                                    @endphp
                                    There {{ $count > 1 ? "were {$count} errors": "was {$count} error" }}
                                    with
                                    your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">

                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input wire:model="name" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="name">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="salt" class="block text-sm font-medium text-gray-700">Generic / Salt</label>
                        <input wire:model="salt" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="salt">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                        <input wire:model="barcode" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="barcode">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="packing" class="block text-sm font-medium text-gray-700">Packing in Piece</label>
                        <input wire:model="packing" type="number" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="packing">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost of Price</label>
                        <input wire:model="cost_of_price" type="number" step="0.01" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="cost_price">
                    </div>

                    <div class="col-span-6 sm:col-span-2">
                        <label for="retail_price" class="block text-sm font-medium text-gray-700">Retail Price</label>
                        <input wire:model="retail_price" type="number" step="0.01" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="retail_price">
                    </div>

                    <div class="col-span-6 sm:col-span-2">
                        <label for="discount_check" class="block text-sm font-medium text-gray-700">Discount
                            Available</label>
                        <select wire:model="discount_check" id="discount_check"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="f">No</option>
                            <option value="t">Yes</option>
                        </select>
                    </div>

                    @if($discount_check == 't')
                        <div class="col-span-6 sm:col-span-2">
                            <label for="max_disc" class="block text-sm font-medium text-gray-700">Max Discount</label>
                            <input wire:model="max_disc" type="number" step="0.01" min="0" max="100" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   id="max_disc">
                        </div>
                    @endif

                    <div class="col-span-6 sm:col-span-2">
                        <label for="manufacture_id" class="block text-sm font-medium text-gray-700">Manufacturer</label>
                        <select wire:model="manufacture_id" id="manufacture_id"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value=""></option>
                            @if($all_manufacturers->isNotEmpty())
                                @foreach($all_manufacturers as $key=>$man)
                                    <option value="{{$man->id}}">{{$man->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>



                    <div class="col-span-6 sm:col-span-2">
                        <label for="rack_id" class="block text-sm font-medium text-gray-700">Rack #</label>
                        <select wire:model="rack_id" id="rack_id"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value=""></option>
                            @if($all_racks->isNotEmpty())
                                @foreach($all_racks as $key=>$ra)
                                    <option value="{{$ra->id}}">{{$ra->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select wire:model="category_id" id="category_id"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value=""></option>
                            @if($all_categories->isNotEmpty())
                                @foreach($all_categories as $key=>$cat)
                                    <option value="{{$cat->id}}">{{$cat->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="reorder_level" class="block text-sm font-medium text-gray-700">Reorder Level</label>
                        <input wire:model="reorder_level" type="number" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="reorder_level">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="reorder_qty" class="block text-sm font-medium text-gray-700">Reorder Qty</label>
                        <input wire:model="reorder_qty" type="number" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="reorder_qty">
                    </div>

                    <div class="col-span-6 sm:col-span-2">
                        <label for="control_medicine" class="block text-sm font-medium text-gray-700">Control
                            Medicine</label>
                        <select wire:model="control_medicine" id="control_medicine"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value=""></option>
                            <option value="t">Yes</option>
                            <option value="f">No</option>
                        </select>
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select wire:model="type" id="type"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value=""></option>
                            <option value="s">Sound alike</option>
                            <option value="l">Look alike</option>
                        </select>
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Temperature</label>
                        <input wire:model="temperature" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>

                    <div class="col-span-6 sm:col-span-2">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model="status" id="status"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="t">Active</option>
                            <option value="f">Inactive</option>
                        </select>
                    </div>


                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    <div wire:loading wire:target="create">
                        Adding...
                    </div>
                    <div wire:loading.remove wire:target="create">
                        Add
                    </div>
                </button>
            </div>
        </div>
    </form>


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
