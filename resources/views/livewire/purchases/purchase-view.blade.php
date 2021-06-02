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


    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-4">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    PO # {{ $purchase_id }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Purchase order details
                </p>
            </div>
            <div class="relative inline-block text-left" x-data="{open:false}">
                <div>
                    <button type="button" x-on:click="open=true;" @click.away="open=false;"
                            class="  rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                            id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <span class="sr-only">Open options</span>
                        <!-- Heroicon name: solid/dots-vertical -->
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                             fill="currentColor" aria-hidden="true">
                            <path
                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </button>
                </div>


                <div x-show="open"
                     class="origin-top-right absolute right-0 mt-2 w-56 z-10 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                     role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                    <div class="py-1" role="none">

                        <button  wire:click="openBasicInfo"
                           class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                           role="menuitem" tabindex="-1">Edit Basic Info</button>
                        @if(empty($purchase->approved_by))
                            <a href="#" wire:click="markAsApproved('{{ $purchase_id }}')"
                               class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                               role="menuitem" tabindex="-1">Mark as Approve
                            </a>
                            <a href="{{ url('pharmacy/purchases/edit') }}/{{$purchase_id}}"
                               class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                               role="menuitem" tabindex="-1">Edit Order</a>

                            <a href="#" wire:click="removePurchase('{{ $purchase_id }}')"
                               class="text-red-700 block w-full text-left px-4 py-2 text-sm hover:bg-red-200"
                               role="menuitem" tabindex="-1">Remove Order</a>
                        @else
                            @if($purchase->status == 'awaiting-delivery')
                                <a href="{{ url('pharmacy/purchases/receive/') }}/{{$purchase_id}}"
                                   class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                   role="menuitem" tabindex="-1">Receive Order</a>
                            @else
                                <a href="{{ url('pharmacy/purchases/compare/') }}/{{$purchase_id}}"
                                   class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                   role="menuitem" tabindex="-1">Order Comparison Report</a>
                            @endif
                        @endif


                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Supplier Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $purchase->supplier_name }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Supplier Invoice
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $purchase->supplier_invoice ?? '-' }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        GRN #
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $purchase->grn_no ?? '-' }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Delivery Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(!empty($purchase->delivery_date))
                            {{ date('d M Y',strtotime($purchase->delivery_date)) }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Status
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($purchase->status == 'approval-awaiting')
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                              Approval Awaiting
                            </span>
                        @elseif($purchase->status == 'awaiting-delivery')
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                               Awaiting Delivery
                            </span>
                        @elseif($purchase->status == 'receiving')
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                              Order Received
                            </span>
                            <br>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                               Approval Pending
                            </span>
                        @elseif($purchase->status == 'received')
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                             Order  Received
                            </span>
                            <br>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                               Invoice Unpaid
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        PO Created By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $purchase->created_by }} <br>
                        {{ date('d M Y h:i A',strtotime($purchase->created_at)) }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        PO Approved By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(!empty($purchase->approved_by))
                            {{ $purchase->approved_by }} <br>
                            {{ date('d M Y h:i A',strtotime($purchase->approved_at)) }}
                        @else
                            -
                        @endif
                    </dd>
                </div>

            </dl>
        </div>
    </div>


    <div class="shadow sm:rounded-md sm:overflow-hidden">

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
                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500   ">
                    Qty
                </th>
                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500   ">
                    Supplier Cost
                </th>
                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                    Retail Price
                </th>

                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                    Total Cost
                </th>

            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($details as $key => $m)
                <tr>
                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $m->name }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $m->salt }}
                    </td>
                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                        {{ $m->qty }}
                    </td>
                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                        {{ number_format($m->cost_of_price,2) }}

                    </td>
                    <td class="px-3 py-3  text-center text-sm text-gray-500">
                        {{ number_format($m->retail_price,2) }}
                    </td>


                    <td class="px-3 py-3 text-center  text-sm text-gray-500">
                        {{ number_format($m->total_cost,2) }}
                    </td>


                </tr>
            @endforeach
            <tr class="bg-gray-50 border-b">
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                </th>
                <th scope="col" class="px-3 py-3  text-md font-medium text-gray-500   ">
                    Total
                </th>
                <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500   ">
                    {{ number_format($details->sum('qty'),2) }}
                </th>
                <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500   ">
                    {{ number_format($details->sum('cost_of_price'),2) }}
                </th>
                <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500    ">
                    {{ number_format($details->sum('retail_price'),2) }}
                </th>

                <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500    ">
                    {{ number_format($details->sum('total_cost'),2) }}
                </th>

            </tr>
            </tbody>
        </table>

    </div>

    @include('pharmacy::include.po-basic-info')
</div>

