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



        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">View Supplier Payment</h3>
                </div>


                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Supplier Name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $supplier_name }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Pay From
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $pay_from_name }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Payment Date
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(!empty($payment_date))
                                {{ date('d M, Y',strtotime($payment_date)) }}
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Status
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">

                            @if(!empty($payment_details['approved_at']))
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-green-100 text-green-800">
                                  Approved
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-red-100 text-red-800">
  Not Approved
</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Description
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $payment_details['description'] ?? '-' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Created By
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $created_by->name }} <br>
                            @if(!empty($payment_date))
                                {{ date('d M, Y H:i A',strtotime($payment_details['created_at'])) }}
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            Approved By
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(!empty($approved_by))
                                {{ $approved_by->name }} <br>
                                @if(!empty($payment_date))
                                    {{ date('d M, Y H:i A',strtotime($payment_details['approved_at'])) }}
                                @endif
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

                </tr>
                </thead>
                <tbody class="   bg-white ">
                <tr   class="bg-gray-50">
                    <th scope="col" colspan="7" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        <i>Purchase Orders</i>
                    </th>
                </tr>
                @foreach($purchase_orders as $key => $m)
                    <tr>

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


                        <td class="px-3 py-3    text-sm text-gray-500">
                            {{ number_format($m['total_cost'],2) }}
                        </td>


                    </tr>
                @endforeach
                <tr  class="bg-gray-50">
                    <th scope="col" colspan="7" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                        <i>Supplier Returns</i>
                    </th>
                </tr>
                @foreach($returns as $key => $m)
                    <tr >

                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td  colspan="4" class="px-3 py-3   text-sm text-gray-500">
                            {{ $m['description'] }}
                        </td>



                        <td class="px-3 py-3   text-sm text-gray-500">
                            {{ number_format($m['total'],2) }}
                        </td>


                    </tr>
                @endforeach
                <tr>
                    <th colspan="6">&nbsp;</th>
                </tr>
                <tr  >
                    <th scope="col" colspan="5" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        Selected Orders
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ count($selected_orders) }}
                    </th>
                </tr>
                <tr  >
                    <th scope="col" colspan="5" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        (Payable Amount)
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        ({{ number_format(collect($purchase_orders)->whereIn('id',$selected_orders)->sum('total_cost'),2) }})
                    </th>
                </tr>
                <tr  >
                    <th scope="col" colspan="5" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        Selected Returns
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ count($selected_returns) }}
                    </th>
                </tr>
                <tr  >
                    <th scope="col" colspan="5" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        Receivable Amount
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ number_format(collect($returns)->whereIn('id',$selected_returns)->sum('total'),2) }}
                    </th>
                </tr>


                <tr  >
                    <th scope="col" colspan="5" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                        (Net Payable) / Receivable
                    </th>
                    <th scope="col"  class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                        {{ \Devzone\Ams\Helper\GeneralJournal::numberFormat(-collect($purchase_orders)->whereIn('id',$selected_orders)->sum('total_cost') + collect($returns)->whereIn('id',$selected_returns)->sum('total'),2) }}
                    </th>
                </tr>
                <tr>
                    <th colspan="6">&nbsp;</th>
                </tr>
                </tbody>
            </table>

        </div>



</div>
