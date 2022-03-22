<div>
    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-8 gap-6">
                {{--                    <div class="col-span-2">--}}
                {{--                        <label for="first-name" class="block text-sm font-medium text-gray-700">Product Name</label>--}}
                {{--                        <input type="text" wire:model="product_name"--}}
                {{--                               wire:click="searchableOpenModal('product_id', 'product_name', 'product')" readonly--}}
                {{--                               name="first-name" id="first-name" autocomplete="given-name"--}}
                {{--                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">--}}
                {{--                    </div>--}}
                <div class="col-span-2">
                    <label for="voucher" class="block text-sm font-medium text-gray-700">Voucher #</label>
                    <input type="text" wire:model.defer="voucher" name="voucher" autocomplete="off"
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-2">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">From</label>
                    <input type="date" wire:model.defer="from" name="first-name" autocomplete="given-name"
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-2">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">To</label>
                    <input type="date" wire:model.defer="to" autocomplete="given-name"
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="col-span-2 flex justify-center items-end">


                    <button type="button" wire:click="search" wire:loading.attr="disabled"
                            class=" bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <div wire:loading wire:target="search">
                            Searching ...
                        </div>
                        <div wire:loading.remove wire:target="search">
                            Search
                        </div>
                    </button>

                    <button type="button" wire:click="resetSearch" wire:loading.attr="disabled"
                            class="ml-2 bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Reset
                    </button>

                </div>
            </div>
        </div>
    </div>


    <div class="shadow sm:rounded-md sm:overflow-hidden">
        <div class="bg-white py-6 flex   justify-between items-center  px-4  sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Open Returns</h3>

            </div>
            <div class="  ">
                <a href="{{url('pharmacy/sales/open-returns/add')}}"
                   class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Returns
                </a>
            </div>

        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    #
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Voucher #
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Remarks
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total <br> (After Deduction)
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Added By
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                </th>

            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($returns as $key => $r)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $loop->iteration + ( $returns->firstItem() - 1)   }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                        {{ $r->voucher }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $r->remarks }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($r->total, 2) }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($r->total_after_deduction, 2) }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $r->added_by }} @<br>
                        {{ date('d M, Y h:i A',strtotime($r->created_at)) }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a class="text-green-600 cursor-pointer" target="_blank"
                           href="{{ url('pharmacy/sales/open-returns/add') }}/{{$r->id}}">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                      d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </td>


                </tr>
            @endforeach
            </tbody>
        </table>

        @if($returns->hasPages())
            <div class="bg-white border-t px-3 py-2">
                {{ $returns->links() }}
            </div>
        @endif
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
