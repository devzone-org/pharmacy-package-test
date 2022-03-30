<div>
    <div class="mb-5 shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                            <div class="grid grid-cols-8 gap-6">
                                <div class="col-span-8 sm:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Supplier
                                        Name</label>
                                    <input type="text" wire:model.defer="supplier_name" readonly
                                           wire:click="searchableOpenModal('supplier_id','supplier_name','supplier')"
                                           name="name" id="name" autocomplete="off"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="col-span-8 sm:col-span-2">
                                    <label for="salt" class="block text-sm font-medium text-gray-700">Supplier
                                        Invoice</label>
                                    <input type="text" wire:model.defer="supplier_invoice" id="salt" autocomplete="off"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="col-span-8 sm:col-span-2">
                                    <label for="from" class="block text-sm font-medium text-gray-700">From</label>
                                    <input type="text" wire:model.lazy="from" id="from" autocomplete="off"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="col-span-8 sm:col-span-2">
                                    <label for="to" class="block text-sm font-medium text-gray-700">To</label>
                                    <input type="text" wire:model.lazy="to" id="to" autocomplete="off"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="col-span-8 sm:col-span-2">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model.defer="status"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            id="status">
                                        <option value=""></option>
                                        <option value="approval-awaiting">PO - Unapproved</option>
                                        <option value="awaiting-delivery">PO - Approved</option>
                                        <option value="receiving">Stock Receiving In-process</option>
                                        <option value="received-f">Stock Received - Invoice Unpaid</option>
                                        <option value="received-t">Order Complete - Invoice Paid</option>
                                    </select>
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
                </div>
            </div>
        </div>


    </div>
    <div class="bg-white mb-5 rounded-md shadow overflow-hidden py-4 px-6">
        <div class="grid grid-cols-5 gap-6">
            <div class="col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    PO - Unapproved
                </dt>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    # {{ number_format($po_unapproved->sum('total')) }}
                </dd>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    PKR {{ number_format($po_unapproved->sum('total_cost_order')) }}
                </dd>
            </div>
            <div class="col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    PO - Approved
                </dt>
                <dd class="mt-1  text-xl text-md text-gray-900">
                    {{ number_format($po_approved->sum('total')) }}
                </dd>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    PKR {{ number_format($po_approved->sum('total_cost_order')) }}
                </dd>
            </div>

            <div class="col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Stock Receiving In-process
                </dt>
                <dd class="mt-1   text-xl text-md text-gray-900">
                    # {{ number_format($stock_receiving_in_process->sum('total')) }}
                </dd>

                <dd class="mt-1   text-xl text-md text-gray-900">
                    PKR {{ number_format($stock_receiving_in_process->sum('total_cost_order')) }}
                </dd>
            </div>

            <div class="col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Unpaid Invoices
                </dt>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    # {{ number_format($unpaid_invoices->sum('total')) }}
                </dd>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    PKR {{ number_format($unpaid_invoices->sum('total_cost_order')) }}
                </dd>
            </div>

            <div class="col-span-1">
                <dt class="text-sm font-medium text-gray-500">
                    Order Completed
                </dt>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    # {{ number_format($order_completed->sum('total')) }}
                </dd>
                <dd class="mt-1 text-xl text-md text-gray-900">
                    PKR {{ number_format($order_completed->sum('total_cost_order')) }}
                </dd>
            </div>
        </div>
    </div>
    <div class="shadow  rounded-b-md ">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6  rounded-t-md">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Purchase Orders</h3>

                <a href="{{ url('pharmacy/purchases/add') }}"
                   class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    Make Purchase Order
                </a>
            </div>


            @include("pharmacy::include.errors")
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

        </div>
        <table class="min-w-full divide-y divide-gray-200 rounded-md ">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    #
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    PO #
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Supplier
                    (Inv #)
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Delivery Date
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Inv Amount
                    <br>(A)
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Adv tax<br>(B)
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Gross Payable Amount<br>(A+B)
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    Status
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    PO Created By
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    PO Approved By
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    Payment Created By
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    Payment Approved By
                </th>
                <th scope="col" class="relative px-3 py-3">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
            </thead>
            <tbody class="   bg-white divide-y divide-gray-200 ">
            @foreach($purchase as $key => $m)
                <tr class="">
                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $m->id }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $m->supplier_name }}
                        <br>{{ !empty($m->supplier_invoice) ? '('.$m->supplier_invoice.')' : '' }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        @if(!empty($m->delivery_date))
                            {{ date('d M Y',strtotime($m->delivery_date)) }}
                        @endif
                        @if(!empty($m->expected_date))
                                <br>
                            (Exp. {{ date('d M Y',strtotime($m->expected_date)) }})
                        @endif

                    </td>
                    @php
                        if($m->status == 'received'){
                             $received=$purchase_receives->where('id',$m->id)->first()->cost_after_receiving;
                             $tax=($m->advance_tax/100)*$received;
                             $received_after_tax=$tax+$received;
                        }else{
                            $received=$m->cost_before_receiving;
                            $tax=($m->advance_tax/100)*$received;
                            $received_after_tax=$tax+$received;
                        }
                    @endphp
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ number_format($received,2) }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ number_format($tax,2) }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ number_format($received_after_tax,2) }}
                    </td>

                    <td class="px-3 py-3   text-sm text-gray-500">
                        @if($m->status == 'approval-awaiting')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                              PO - Unapproved
                            </span>
                        @elseif($m->status == 'awaiting-delivery')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                               PO - Approved
                            </span>
                        @elseif($m->status == 'receiving')
                            <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                               Stock Receiving <br>In-process
                            </span>
                        @elseif($m->status == 'Void')
                            <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                           Void
                            </span>
                        @elseif($m->status == 'received')
                            @if($m->is_paid=='f')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                 Stock Received
                                </span>
                                <br>

                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                   Invoice Unpaid
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Order Complete
                                </span>
                                <br>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Invoice Paid
                                </span>
                            @endif

                        @endif
                    </td>

                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $m->created_by }} <br>
                        {{ date('d M Y h:i A',strtotime($m->created_at)) }}
                    </td>

                    <td class="px-3 py-3   text-sm text-gray-500">
                        @if(!empty($m->approved_by))
                            {{ $m->approved_by }} <br>
                            {{ date('d M Y h:i A',strtotime($m->approved_at)) }}
                        @endif
                    </td>
                    @php
                        $p=$payments->where('order_id',$m->id)->first();
                    @endphp
                    <td class="px-3 py-3   text-sm text-gray-500">
                        @if(!empty($p))
                            {{$p->added_by}} <br>
                            {{ !empty($p->created_at) ? date('d M Y h:i A',strtotime($p->created_at)) : '' }}
                        @endif
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        @if(!empty($p))
                            {{$p->approved_by}} <br>
                            {{ !empty($p->approved_at) ? date('d M Y h:i A',strtotime($p->approved_at)) : '' }}
                        @endif
                    </td>

                    <td class="px-3 py-3 w-7   text-right text-sm font-medium">
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
                                    <a href="{{ url('pharmacy/purchases/view') }}/{{$m->id}}"
                                       class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                       role="menuitem" tabindex="-1">View Order</a>

                                    @if(empty($m->approved_by))
                                        <a href="{{ url('pharmacy/purchases/view') }}/{{$m->id}}"

                                           class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                           role="menuitem" tabindex="-1">Mark as Approve
                                        </a>
                                        <a href="{{ url('pharmacy/purchases/edit') }}/{{$m->id}}"
                                           class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                           role="menuitem" tabindex="-1">Edit Order</a>

                                        <a href="#" wire:click="removePurchase('{{ $m->id }}')"
                                           class="text-red-700 block w-full text-left px-4 py-2 text-sm hover:bg-red-200"
                                           role="menuitem" tabindex="-1">Remove Order</a>


                                    @else
                                        @if($m->status == 'awaiting-delivery' )
                                            <a href="{{ url('pharmacy/purchases/receive/') }}/{{$m->id}}"
                                               class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                               role="menuitem" tabindex="-1">Receive Order</a>

                                            <a href="{{ url('pharmacy/purchases/view') }}/{{$m->id}}"
                                               class="text-red-700 block w-full text-left px-4 py-2 text-sm hover:bg-red-200"
                                               role="menuitem" tabindex="-1">Void</a>
                                        @elseif($m->status != 'Void')
                                            <a href="{{ url('pharmacy/purchases/compare/') }}/{{$m->id}}"
                                               class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                               role="menuitem" tabindex="-1">Order Comparison Report</a>
                                        @endif

                                    @endif


                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>


        <div class="bg-white p-3 border-t rounded-b-md  ">
            {{ $purchase->links() }}
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
