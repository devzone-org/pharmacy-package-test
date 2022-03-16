<div class="max-w-7xl mx-auto grid grid-cols-4 gap-6 sm:px-6  ">

    <div class=" col-span-4 border-gray-200">
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

    <div class="space-y-6   col-span-3">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-4">
            @if($description_check != true)

                @include('pharmacy::include.errors')
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
            @endif

            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Purchase Order Details
                    </h3>

                </div>
                <div class="flex items-center">
                    @if(empty($purchase->approved_by))
                        @can('12.purchase-order-approve')
                            <button type="button" wire:click="markAsApproved('{{ $purchase_id }}')"
                                    wire:loading.attr="disabled"
                                    class="mr-4 bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                                Mark as Approve
                            </button>
                        @endcan
                    @elseif($purchase->status == 'awaiting-delivery' && $purchase->status != 'Void')
                        <button type="button" wire:click="openDescription"
                                class="mr-4 bg-gray-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-600">
                            Void
                        </button>
                    @endif

                    @if($purchase->status == 'awaiting-delivery' || $purchase->status == 'received' || $purchase->status == 'receiving'  )
                        <a href="{{url('pharmacy/purchases/purchase-order/view/pdf')}}?purchase_id={{$purchase_id}}"
                           target="_blank"
                           class="mr-4 bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                            Download Purchase Order
                        </a>

                    @endif

                    @if($purchase->status != 'Void')

                        <div class="relative inline-block text-left" x-data="{open:false}">
                            <div>
                                <button type="button" x-on:click="open=true" @click.away="open=false;"
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

                                    <button wire:click="openBasicInfo"
                                            class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                            role="menuitem" tabindex="-1">Edit Basic Info
                                    </button>
                                    @if(empty($purchase->approved_by))

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
                    @endif

                </div>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            PO #
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            {{ $purchase_id }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Supplier Name
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            {{ $purchase->supplier_name }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Supplier Invoice
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            {{ $purchase->supplier_invoice ?? '-' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            GRN #
                        </dt>
                        <dd class="mt-1 text-sm  font-medium text-gray-900">
                            {{ $purchase->grn_no ?? '-' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Delivery Date
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
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
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            @if($purchase->status == 'approval-awaiting')
                                <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                              PO - Unapproved
                            </span>
                            @elseif($purchase->status == 'awaiting-delivery')
                                <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                               PO - Approved
                            </span>
                            @elseif($purchase->status == 'receiving')

                                <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                               Stock Receiving <br>In-process
                            </span>
                            @elseif($purchase->status == 'Void')
                                <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text--black-800">
                             Void
                            </span>


                            @elseif($purchase->status == 'received')
                                @if($purchase->is_paid=='f')
                                    <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                     Stock Received
                                    </span>
                                    <br>

                                    <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                       Invoice Unpaid
                                    </span>
                                @else
                                    <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                     Order Complete
                                    </span>
                                    <br>
                                    <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                     Invoice Paid
                                    </span>
                                @endif
                            @endif
                        </dd>
                    </div>
                    @php
                        if ($purchase->status == 'received'){
                            $received=$purchase_receive->total_receive;
                            $tax=($purchase->advance_tax/100)*$received;
                            $received_after_tax=$received+$tax;
                        }else{
                            $received=$details->sum('total_cost');
                            $tax=($purchase->advance_tax/100)*$received;
                            $received_after_tax=$received+$tax;
                        }
                    @endphp
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            PO Inventory Amount (A)
                        </dt>
                        <dd class="mt-1 text-sm  font-medium text-gray-900">
                            {{number_format($received,2)}}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            PO Advance Tax (B)
                        </dt>
                        <dd class="mt-1 text-sm  font-medium text-gray-900">
                            {{number_format($tax,2)}}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            PO Gross Payable Amount (A+B)
                        </dt>
                        <dd class="mt-1 text-sm  font-medium text-gray-900">
                            {{number_format($received_after_tax,2)}}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            PO Created By
                        </dt>
                        <dd class="mt-1 text-sm  font-medium text-gray-900">
                            {{ $purchase->created_by }} <br>
                            {{ date('d M Y h:i A',strtotime($purchase->created_at)) }}
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm  font-medium font-medium text-gray-500">
                            PO Approved By
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            @if(!empty($purchase->approved_by))
                                {{ $purchase->approved_by }} <br>
                                {{ date('d M Y h:i A',strtotime($purchase->approved_at)) }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm  font-medium  text-gray-500">
                            Payment Created By
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            @if(!empty($supplier_payment_details))
                                {{ $supplier_payment_details->added_by }} <br>
                                {{ !empty($supplier_payment_details->created_at) ? date('d M Y h:i A',strtotime($supplier_payment_details->created_at)) : '-' }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm  font-medium font-medium text-gray-500">
                            Payment Approved By
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            @if(!empty($supplier_payment_details))
                                {{ $supplier_payment_details->approved_by }} <br>
                                {{ !empty($supplier_payment_details->approved_at) ? date('d M Y h:i A',strtotime($supplier_payment_details->approved_at)) : '-' }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm  font-medium font-medium text-gray-500">
                            Description
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            @if(!empty($purchase->description))
                                {{ $purchase->description }}
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
                        Disc (%)
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
                        <td class="px-3 py-3  text-center text-sm text-gray-500">
                            {{ $m->discount ?? 0.00 }}
                        </td>
                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                            {{ number_format($m->total_cost,2) }}
                        </td>
                    </tr>
                @endforeach
                <tr class="bg-gray-50 border-b">
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                    </th>
                    <th scope="col" class="px-3 py-3  text-md font-medium text-gray-500">
                        Total
                    </th>
                    <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500">
                        {{ number_format($details->sum('qty'),2) }}
                    </th>
                    <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500">
                        {{ number_format($details->sum('cost_of_price'),2) }}
                    </th>
                    <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500">
                        {{ number_format($details->sum('retail_price'),2) }}
                    </th>
                    <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500">
                    </th>
                    <th scope="col" class="px-3 py-3 text-center text-md font-medium text-gray-500">
                        {{ number_format($details->sum('total_cost'),2) }}
                    </th>

                </tr>
                </tbody>
            </table>

        </div>
    </div>
    <div class=" col-span-1">
        <div class="bg-white shadow rounded-md  p-5">
            <!-- This example requires Tailwind CSS v2.0+ -->
            <nav aria-label="Progress">
                <ol class="overflow-hidden">
                    <li class="relative pb-10">
                        <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-indigo-600"
                             aria-hidden="true"></div>
                        <a href="#" class="relative flex items-center group">
                            <span class="h-9 flex items-center">
          <span
                  class="relative z-10 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 aria-hidden="true">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"/>
            </svg>
          </span>
        </span>
                            <span class="ml-4 min-w-0 flex flex-col">
                                <span class="text-xs font-semibold tracking-wide uppercase">Create PO</span>
                            </span>
                        </a>
                    </li>

                    <li class="relative pb-10">

                        @if(in_array($purchase->status,['awaiting-delivery','receiving','received']))
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-indigo-600"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group">
                            <span class="h-9 flex items-center">
          <span
                  class="relative z-10 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 aria-hidden="true">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"/>
            </svg>
          </span>
        </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                <span class="text-xs font-semibold tracking-wide uppercase">Approved PO</span>
                            </span>
                            </a>
                        @elseif(in_array($purchase->status,['approval-awaiting']))
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group" aria-current="step">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-indigo-600 rounded-full">
                                    <span class="h-2.5 w-2.5 bg-indigo-600 rounded-full"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-indigo-600">Approved PO</span>
                                </span>
                            </a>
                        @else
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-full group-hover:border-gray-400">
                                    <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-gray-500">Business information</span>
                                </span>
                            </a>
                        @endif

                    </li>

                    <li class="relative pb-10">

                        @if(in_array($purchase->status,['received']))
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-indigo-600"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group">
                            <span class="h-9 flex items-center">
          <span
                  class="relative z-10 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 aria-hidden="true">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"/>
            </svg>
          </span>
        </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                <span class="text-xs font-semibold tracking-wide uppercase">Stock Received</span>
                            </span>
                            </a>
                        @elseif(in_array($purchase->status,['receiving','awaiting-delivery']))
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group" aria-current="step">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-indigo-600 rounded-full">
                                    <span class="h-2.5 w-2.5 bg-indigo-600 rounded-full"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-indigo-600">Stock Received</span>
                                </span>
                            </a>
                        @else
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-full group-hover:border-gray-400">
                                    <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-gray-500">Stock Received</span>
                                </span>
                            </a>
                        @endif

                    </li>

                    <li class="relative pb-10">

                        @if(in_array($purchase->status,['received']) && $purchase->is_paid == 't')
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-indigo-600"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group">
                            <span class="h-9 flex items-center">
          <span
                  class="relative z-10 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 aria-hidden="true">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"/>
            </svg>
          </span>
        </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                <span class="text-xs font-semibold tracking-wide uppercase">Invoice Paid</span>
                            </span>
                            </a>
                        @elseif($purchase->status == 'received' && $purchase->is_paid == 'f')
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group" aria-current="step">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-indigo-600 rounded-full">
                                    <span class="h-2.5 w-2.5 bg-indigo-600 rounded-full"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-indigo-600">Invoice Paid</span>
                                </span>
                            </a>
                        @else
                            <div class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300"
                                 aria-hidden="true"></div>
                            <a href="#" class="relative flex items-center group">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-full group-hover:border-gray-400">
                                    <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-gray-500">Invoice Paid</span>
                                </span>
                            </a>
                        @endif

                    </li>

                    <li class="relative">
                        @if(in_array($purchase->status,['received']) && $purchase->is_paid == 't')
                            <a href="#" class="relative flex items-center group">
                            <span class="h-9 flex items-center">
          <span
                  class="relative z-10 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 aria-hidden="true">
              <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"/>
            </svg>
          </span>
        </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                <span class="text-xs font-semibold tracking-wide uppercase">Order Complete</span>
                            </span>
                            </a>

                        @else
                            <a href="#" class="relative flex items-center group">
                                <span class="h-9 flex items-center" aria-hidden="true">
                                  <span
                                          class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-full group-hover:border-gray-400">
                                    <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300"></span>
                                  </span>
                                </span>
                                <span class="ml-4 min-w-0 flex flex-col">
                                  <span class="text-xs font-semibold tracking-wide uppercase text-gray-500">Order Complete</span>
                                </span>
                            </a>
                        @endif
                    </li>
                </ol>
            </nav>

        </div>
    </div>
    @include('pharmacy::include.po-basic-info')
    @include('pharmacy::include.po-description')

</div>

