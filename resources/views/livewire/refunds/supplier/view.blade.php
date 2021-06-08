<div>

    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/purchases/refund') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Supplier Refunds</span>
        </h3>
    </div>


        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">View Supplier Refund Details</h3>
                </div>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Supplier Name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $refund->supplier_name }}
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Refund Receive In
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $refund->account_name }}
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Refund Receiving Date
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(!empty( $refund->receiving_date))
                                {{ date('d M, Y',strtotime( $refund->receiving_date))  }}
                            @endif

                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Status
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($refund->is_receive=='t')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-green-100 text-green-800">
  Received
</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-red-100 text-red-800">
 Not Received
</span>
                            @endif
                        </dd>
                    </div>


                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Created By
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $refund->created_by }}<br>

                            {{ date('d M, Y H:i A',strtotime( $refund->created_at))  }}
                        </dd>
                    </div>


                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Approved By
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(!empty($refund->approved_by))
                                {{ $refund->approved_by }}<br>

                                {{ date('d M, Y H:i A',strtotime( $refund->approved_at))  }}
                            @endif

                        </dd>
                    </div>


                </dl>

            </div>

                        <table class="min-w-full divide-y divide-gray-200 rounded-md ">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    #
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Product Name
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    PO #
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Supply Price
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Available Qty
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Return Qty
                                </th>
                            </tr>
                            </thead>
                            <tbody class="   bg-white divide-y divide-gray-200 ">
                            @foreach($refund_details->toArray() as $key => $m)
                                <tr class="">

                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m['product_name'] }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m['po_id'] }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m['supply_price'] }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m['available'] }}
                                    </td>



                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m['qty'] }}
                                    </td>



                                </tr>
                            @endforeach

                            </tbody>
                        </table>


        </div>


</div>
