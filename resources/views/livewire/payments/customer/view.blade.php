<div>

    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/customer/payments') }}"
               class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"></path>
                </svg>
            </a>
            <span class="ml-4">Customer Payment Details</span>
        </h3>
    </div>


    <div class="shadow sm:rounded-md sm:overflow-hidden">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">View Customer Payment</h3>
            </div>

            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Customer
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer_payment['customer'] }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Receive In
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">

                        {{ $customer_payment['account_name'] }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Payment Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ date('d M, Y',strtotime($customer_payment['receiving_date'])) }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Status
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(empty($customer_payment['approved_at']))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-red-100 text-red-800">
                                 Not Approved
                                </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-green-100 text-green-800">
                                  Approved
                                </span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Description
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer_payment['description'] }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Amount
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ number_format($customer_payment['total_receive'],2) }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Created By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer_payment['added_by_name'] }} <br>
                        {{ date('d M, Y',strtotime($customer_payment['created_at'])) }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Approved By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $customer_payment['approved_by_name'] }} <br>
                        {{ date('d M, Y',strtotime($customer_payment['approved_at'])) }}
                    </dd>
                </div>
            </dl>
        </div>
        <table class="min-w-full divide-y divide-gray-200 rounded-md ">
            <thead class="bg-white">
            <tr>
                <th scope="col" colspan="7" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    <i>Un Paid Invoices</i>
                </th>
            </tr>
            <tr class="bg-gray-50">

                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    #
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    Receipt #
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    Cash
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    On Account
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    Refund
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    Total Receivable
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500">
                    Date
                </th>
            </tr>
            </thead>
            <tbody class="   bg-white ">
            @foreach($payments as $key=>$p)
                <tr>

                    <td class="px-3 py-3 text-sm font-medium text-gray-500">
                        {{$loop->iteration}}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        {{$p['id']}}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        {{number_format($p['receive_amount'],2)}}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        {{number_format($p['on_account'],2)}}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        {{number_format($p['refunded'],2)}}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        {{number_format($p['on_account']-$p['refunded'],2)}}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        {{date('d M Y h:i A',strtotime($p['sale_at']))}}
                    </td>
                </tr>
            @endforeach
            <tr>
                <th colspan="7">&nbsp;</th>
            </tr>
            <tr>
                <th scope="col" colspan="6" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                    Selected Receipt
                </th>
                <th scope="col" class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                    {{collect($this->payments)->where('selected',true)->count()}}
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="6" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                    Sub Total
                </th>
                <th scope="col" class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                    {{collect($this->payments)->where('selected',true)->sum('on_account')}}
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="6" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                    Refunded
                </th>
                <th scope="col" class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                    {{collect($this->payments)->where('selected',true)->sum('refunded')}}
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="6" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                    Total Receivable
                </th>
                <th scope="col" class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                    {{collect($this->payments)->where('selected',true)->sum('on_account') - collect($this->payments)->where('selected',true)->sum('refunded')}}
                </th>
            </tr>
            <tr>
                <th colspan="7">&nbsp;</th>
            </tr>
            </tbody>
        </table>
    </div>


</div>
