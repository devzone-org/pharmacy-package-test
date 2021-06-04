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
                    <h3 class="text-lg leading-6 font-medium text-gray-900">View Supplier Payment</h3>
                </div>


                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-2">
                        <label for="supplier_name" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                        <input wire:model.defer="supplier_name"
                                readonly
                               type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="supplier_name">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="pay_from" class="block text-sm font-medium text-gray-700">Pay From</label>
                        <input wire:model.defer="pay_from_name"
                                readonly
                               type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="pay_from">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input wire:model.defer="payment_date" type="date" autocomplete="off"
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
                <thead class="bg-gray-50">
                <tr>



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
                        Payable Amount
                    </th>


                </tr>
                </thead>
                <tbody class="   bg-white divide-y divide-gray-200 ">
                @foreach($purchase_orders as $key => $m)
                    <tr class="">

                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['id'] }}
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


                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ number_format($m['total_cost'],2) }}
                        </td>


                    </tr>
                @endforeach
                <tr class="bg-gray-50">

                    <th scope="col" colspan="5" class="px-3 py-3 text-center text-sm font-medium text-gray-500   ">
                        Total
                    </th>
                    <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                        {{ number_format(collect($purchase_orders)->sum('total_cost'),2) }}
                    </th>


                </tr>
                </tbody>
            </table>


        </div>
    </form>


</div>
