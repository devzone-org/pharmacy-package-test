<div>

    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-2">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" wire:model="product_name"
                           wire:click="searchableOpenModal('product_id', 'product_name', 'product')" readonly
                           name="first-name" id="first-name" autocomplete="given-name"
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-2">
                    <label for="last-name" class="block text-sm font-medium text-gray-700">Indicator</label>
                    <select name="" wire:model.defer="indicator"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value=""></option>
                        <option value="i">Increase</option>
                        <option value="d">Decrease</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">From</label>
                    <input type="text" wire:model.lazy="from" id="from" name="first-name" autocomplete="given-name" readonly
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-2">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">To</label>
                    <input type="text" wire:model.defer="to" id="to" autocomplete="given-name" readonly
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="col-span-3">
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
                <h3 class="text-lg leading-6 font-medium text-gray-900">Stock Adjustment</h3>

            </div>
            <div class="  ">
                <a href="{{ url('pharmacy/purchases/stock-adjustment/add') }}"
                   class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Adjust Stock
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
                    Name
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Remarks
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Added By
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Added At
                </th>

            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($stock as $key => $a)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $loop->iteration + ( $stock->firstItem() - 1)   }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $a->name }}
                    </td>
                    <td class="px-6 flex py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($a->indicator == 'i')
                            <svg class="w-6 text-green-600 h-6" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        @else
                            <svg class="w-6 text-red-600 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        @endif &nbsp;
                        <span>{{ $a->qty }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $a->remarks }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $a->added_by }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ date('d M, Y h:i A',strtotime($a->created_at)) }}
                    </td>


                </tr>
            @endforeach
            </tbody>
        </table>

        @if($stock->hasPages())
            <div class="bg-white border-t px-3 py-2">
                {{ $stock->links() }}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<script>
    let from_date = new Pikaday({
        field: document.getElementById('from'),
        format: "DD MMM YYYY"
    });

    let to_date = new Pikaday({
        field: document.getElementById('to'),
        format: "DD MMM YYYY"
    });

    from_date.setDate(new Date('{{ $from }}'));
    to_date.setDate(new Date('{{ $to }}'));
</script>
