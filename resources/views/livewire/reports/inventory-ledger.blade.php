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
                <div class="col-span-8 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Date Range</label>
                    <select wire:model="range"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="seven_days">Last 7 Days</option>
                        <option value="thirty_days">Last 30 Days</option>
                        <option value="custom_range">Custom Range</option>
                    </select>
                </div>
                @if($date_range)
                    <div class="col-span-8 sm:col-span-2">
                        <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                        <input type="date" wire:model.defer="from" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="col-span-8 sm:col-span-2">
                        <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                        <input type="date" wire:model.defer="to" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                @endif
                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Inventory Ledger
                                Report</p>
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{!empty($report) ? $report['0']['item'] : '-'}}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-900">
                                    Sr #
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    Description
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    Decrease
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    Increase
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                    Closing Inventory
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @if(!empty($report))
                                <tr class="bg-gray-50">
                                    <th scope="col" colspan="5"
                                        class="px-3 py-3 text-right text-sm font-medium text-gray-900">
                                        Opening Inventory
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        {{$opening_inv}}
                                    </th>
                                    @php
                                        $closing = $opening_inv;
                                    @endphp
                                </tr>
                                @foreach($report as $r)
                                    <tr>
                                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                            {{ $loop->iteration  }}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{date('d M Y h:i A',strtotime($r['created_at']))}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['description']}}
                                        </td>
                                        <td class="px-3 py-3  text-center text-sm text-gray-500">
                                            {{$r['decrease']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['increase']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            @php
                                                if($r['increase']>0){
                                                    $closing = $closing+$r['increase'];
                                                }else{
                                                    $closing = $closing-$r['decrease'];
                                                }
                                            @endphp
                                            {{$closing}}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50">
                                    <th scope="col" colspan="5"
                                        class="px-3 py-3 text-right text-sm font-medium text-gray-900">
                                        Closing Inventory
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                        @php
                                            $opening= $opening_inv;
                                        @endphp
                                        {{abs($closing)}}
                                    </th>
                                </tr>
                            </tbody>
                            @endif
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
            }, 100);
        });
    });
</script>
