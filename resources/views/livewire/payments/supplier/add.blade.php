<div>

    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/purchases/payments') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Supplier Payments</span>
        </h3>
    </div>


    <form wire:submit.prevent="create">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add Supplier Payment</h3>

                </div>

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

                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-2">
                        <label for="supplier_name" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                        <input wire:model.defer="supplier_name"
                               wire:click="searchableOpenModal('supplier_id', 'supplier_name', 'supplier')" readonly
                               type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="supplier_name">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="closing_balance" class="block text-sm font-medium text-gray-700">Closing Balance</label>
                        <input   value="{{ $closing_balance }}" readonly
                                 type="text" autocomplete="off"
                                 class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                 id="closing_balance">
                    </div>



                    <div class="col-span-6 sm:col-span-2">
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input wire:model.lazy="payment_date" type="text" autocomplete="off" readonly
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="payment_date">
                    </div>


                    <div class="col-span-6 ">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            id="description" cols="30" wire:model.defer="description" rows="4"></textarea>
                    </div>

                </div>
            </div>


            <table class="min-w-full divide-y divide-gray-200 rounded-md ">
                <thead  class="bg-white">
                <tr  >
                    <th scope="col" colspan="9" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        <i>Un Paid Purchase Orders</i>
                    </th>
                </tr>
                <tr  class="bg-gray-50">

                    <th scope="col" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        #
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        PO #
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Supplier Invoice
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        GRN #
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Delivery Date
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        Amount
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        Tax
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        Gross Amount
                    </th>

                </tr>
                </thead>
                <tbody class="   bg-white ">

                @foreach($purchase_orders as $key => $m)
                    <tr>
                        <td class="px-3 py-3   text-sm font-medium text-gray-500">

                            <input id="orders{{$loop->iteration}}" value="{{ $m['id'] }}"
                                   name="orders{{$loop->iteration}}" wire:model="selected_orders" type="checkbox"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </td>
                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            PO {{ $m['id'] }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['supplier_invoice'] }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['grn_no'] }}
                        </td>

                        <td class="px-3 py-3   text-sm text-gray-500">
                            @if(!empty($m['delivery_date']))
                                {{ date('d M Y',strtotime($m['delivery_date'])) }}
                            @endif
                        </td>


                        <td class="px-3 py-3    text-sm text-gray-500">
                            {{ number_format($m['total_cost'],2) }}
                        </td>
                        <td class="px-3 py-3    text-sm text-gray-500">
                            @php
                            $tax = 0;
                            if(!empty($m['advance_tax'])){
                                $tax = $m['total_cost'] * ($m['advance_tax']/100);
                            }
                            @endphp
                            {{ number_format($tax,2) }}
                        </td>
                        <td class="px-3 py-3    text-sm text-gray-500">
                            {{ number_format($m['total_cost'] + $tax,2) }}
                        </td>


                    </tr>
                @endforeach
                <tr  class="bg-white">
                    <th scope="col" colspan="9" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        &nbsp;
                    </th>
                </tr>
                <tr  class="bg-white">
                    <th scope="col" colspan="9" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        <i>Un Adjusted Returns</i>
                    </th>
                </tr>
                <tr  class="bg-gray-50">

                    <th scope="col" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">

                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        #
                    </th>

                    <th scope="col"  colspan="6" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        Description
                    </th>

                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        Amount
                    </th>

                </tr>
                @foreach($returns as $key => $m)
                    <tr >
                        <td class="px-3 py-3   text-sm font-medium text-gray-500">

                            <input id="returns{{$loop->iteration}}" value="{{ $m['id'] }}"
                                   name="returns{{$loop->iteration}}" wire:model="selected_returns" type="checkbox"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </td>
                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td  colspan="6" class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['description'] }}
                        </td>



                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ number_format($m['total'],2) }}
                        </td>


                    </tr>
                @endforeach
                <tr>
                    <th colspan="9">&nbsp;</th>
                </tr>
                <tr  >
                    <th scope="col" colspan="8" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        Selected Orders
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ count($selected_orders) }}
                    </th>
                </tr>
                <tr  >
                    <th scope="col" colspan="8" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        (Payable Amount)
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        @php
                            $total_payable= collect($purchase_orders)->whereIn('id',$selected_orders)->sum('total_cost') + collect($purchase_orders)->whereIn('id',$selected_orders)->sum('tax_amount');
                        @endphp
                        ({{ number_format($total_payable,2) }})
                    </th>
                </tr>
                <tr  >
                    <th scope="col" colspan="8" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        Selected Returns
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ count($selected_returns) }}
                    </th>
                </tr>
                <tr  >
                    <th scope="col" colspan="8" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        Receivable Amount
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ number_format(collect($returns)->whereIn('id',$selected_returns)->sum('total'),2) }}
                    </th>
                </tr>


                <tr  >
                    <th scope="col" colspan="8" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        (Net Payable) / Receivable
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ \Devzone\Ams\Helper\GeneralJournal::numberFormat(-$total_payable + collect($returns)->whereIn('id',$selected_returns)->sum('total'),2) }}
                    </th>
                </tr>
                <tr>
                    <th colspan="9">&nbsp;</th>
                </tr>
                </tbody>
            </table>




            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit"
                        class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    <div wire:loading>
                        Adding ...
                    </div>
                    <div wire:loading.remove>
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
            }, 100);
        });
    });

    // document.addEventListener("keydown", event => {
    //     if (event.keyCode == 13) {
    //         alert('ads');
    //     }
    // });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<script>
    let from_date = new Pikaday({
        field: document.getElementById('payment_date'),
        format: "DD MMM YYYY"
    });



    from_date.setDate(new Date('{{ $payment_date }}'));
</script>
