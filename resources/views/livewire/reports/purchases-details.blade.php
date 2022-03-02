<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-10 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label for="supplier_name" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                    <input wire:model.defer="supplier_name"
                           wire:click="searchableOpenModal('supplier_id', 'supplier_name', 'supplier')" readonly
                           type="text" autocomplete="off"
                           class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           id="supplier_name">
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
                        <label for="from" class="block text-sm font-medium text-gray-700">Purchase From</label>
                        <input type="date" wire:model.defer="from" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="col-span-8 sm:col-span-2">
                        <label for="to" class="block text-sm font-medium text-gray-700">Purchase To</label>
                        <input type="date" wire:model.defer="to" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                @endif
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
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-4 rounded-md bg-red-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        There {{ $errors->count() > 1? 'were' : 'was' }} {{ $errors->count() }} {{
                                $errors->count() > 1? 'errors' : 'error' }}
                        with your submission
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="pl-5 space-y-1 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Pharmacy Purchases Details Report</p>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    @foreach(collect($this->report)->groupBy('po_no') as $po)
        @php
            $first=collect($po)->first();
            $total_discount_value=0;
        @endphp
        <dl class="grid grid-cols-1  rounded-t-lg bg-white overflow-hidden shadow divide-y divide-gray-200 md:grid-cols-6 md:divide-y-0 md:divide-x">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-base font-semibold text-gray-900">
                    Supplier Name
                </dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-gray-900">
                        {{$first['supplier_name']}}
                    </div>
                </dd>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-base font-semibold text-gray-900">
                    PO #
                </dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-gray-900">
                        {{$first['po_no']}}
                    </div>
                </dd>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <dt class="text-base font-semibold text-gray-900">
                    PO Date
                </dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-gray-900">
                        {{!empty($first['placement_date']) ? date('d M Y',strtotime($first['placement_date'])) : '-'}}
                    </div>
                </dd>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-base font-semibold text-gray-900">
                    GR#
                </dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-gray-900">
                        {{$first['grn_no']}}
                    </div>
                </dd>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-base font-semibold text-gray-900">
                    Supplier Invoice
                </dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-gray-900">
                        {{$first['supplier_invoice']}}
                    </div>
                </dd>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <dt class="text-base font-semibold text-gray-900">
                    Delivery Date
                </dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-gray-900">
                        {{!empty($first['receiving_date']) ? date('d M Y',strtotime($first['receiving_date'])) : '-'}}
                    </div>
                </dd>
            </div>
        </dl>
        <div class="bg-white mb-4 pb-2 rounded-b-lg">
            <table class="min-w-full  divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-3 py-3 text-left text-sm font-medium text-gray-900   ">
                        Sr #
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                        Item
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                        Purchase
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900   ">
                        Qty
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Bonus Qty
                    </th>

                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Total Value
                    </th>

                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Disc
                    </th>

                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        After Disc
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Tax
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Grand Total
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Retail Price
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Expiry Date
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900    ">
                        Batch No
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $total_tax = 0;
                @endphp
                @foreach($po as $o)
                    <tr>
                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                            {{ $loop->iteration  }}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['product_name']}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['cost_of_price']}}
                        </td>
                        <td class="px-3 py-3  text-center text-sm text-gray-500">
                            {{$o['qty']}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['bonus']}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{ number_format($o['qty'] * $o['cost_of_price']) }}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            @php
                                $discount_value=$o['qty']*$o['cost_of_price'] - $o['qty']*$o['after_disc_cost'];
                                $total_discount_value=$total_discount_value+$discount_value;
                            @endphp
                            {{number_format($discount_value,2)}} ({{$o['discount']}}%)
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['total_cost']}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            @php
                                $tax = 0;
                                    if(!empty($o['advance_tax'])){
                                        $tax = $o['total_cost'] /100 *$o['advance_tax'];
                                    }
                                    $total_tax = $total_tax + $tax;
                            @endphp
                            {{number_format($tax,2)}}
                        </td>

                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['total_cost']+$tax}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['retail_price']}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{!empty($o['expiry'])? date('d M Y',strtotime($o['expiry'])) : '- '}}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{$o['batch_no']}}
                        </td>
                    </tr>
                @endforeach
                <tr class="bg-gray-50">
                    <th scope="col" colspan="3"
                        class="px-3 py-3 text-left text-sm font-medium text-gray-900">
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                        {{collect($po)->sum('qty')}}
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                        {{collect($po)->sum('bonus')}}
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">

                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                        {{number_format($total_discount_value,2)}}
                    </th>

                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                        {{number_format(collect($po)->sum('total_cost'),2)}}
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                    {{ number_format($total_tax,2) }}
                    </th>
                    <th scope="col"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                        {{number_format($total_tax + collect($po)->sum('total_cost'),2)}}
                    </th>
                    <th scope="col" colspan="3"
                        class="px-3 py-3 text-center text-sm font-medium text-gray-900">

                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    @endforeach
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
