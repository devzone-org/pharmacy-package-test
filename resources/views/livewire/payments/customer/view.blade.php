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
            <span class="ml-4">Customer Payments</span>
        </h3>
    </div>


    <div class="shadow sm:rounded-md sm:overflow-hidden">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">View Customer Payment</h3>
            </div>

            @php
                $first=collect($payments)->first();
            @endphp
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Customer Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $first['customer_name'] }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Receiving In
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $first['account_name'] }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Receiving Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(!empty($first['receiving_date']))
                            {{ date('d M, Y',strtotime($first['receiving_date'])) }}
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Status
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">

                        @if(!empty($first['approved_at']))
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
                        {{ $first['description'] ?? '-' }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Created By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $first['added_by'] }} <br>
                        @if(!empty($first['receiving_date']))
                            {{ date('d M, Y',strtotime($first['receiving_date'])) }}
                        @endif
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Approved By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(!empty($first['approved_at']))
                            {{ $first['approved_by'] }} <br>
                            {{ date('d M, Y H:i A',strtotime($first['approved_at'])) }}
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
        <table class="min-w-full divide-y divide-gray-200 rounded-md ">
            <thead class="bg-white">
            <tr>
                <th scope="col" colspan="8" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    <i>Receipts</i>
                </th>
            </tr>
            <tr class="bg-gray-50">

                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    #
                </th>

                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Receipt #
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Invoice #
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    Cash
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    on Account
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    Refund
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    Total Receivable
                </th>
                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                    Date
                </th>

            </tr>
            </thead>
            <tbody class="bg-white">

            @foreach($payments as $key => $p)
                <tr>

                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $p['receipt_no'] }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $p['invoice_no'] }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $p['receive_amount'] }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{ $p['gross_total']-$p['receive_amount'] }}
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        -
                    </td>
                    <td class="px-3 py-3   text-sm text-gray-500">
                        {{$p['gross_total']-$p['receive_amount']}}
                    </td>

                    <td class="px-3 py-3   text-sm text-gray-500">
                        @if(!empty($p['sale_at']))
                            {{ date('d M Y h:i A',strtotime($p['sale_at'])) }}
                        @endif
                    </td>


                </tr>
            @endforeach

            <tr class="bg-white">
                <th scope="col" colspan="8" class="w-10 px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                    &nbsp;
                </th>
            </tr>
            <tr>
                <th scope="col" colspan="7" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                    Selected Orders
                </th>
                <th scope="col" class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                    {{ collect($payments)->count() }}
                </th>
            </tr>

            <tr>
                <th scope="col" colspan="7" class="px-3 py-3 text-right text-sm font-medium text-gray-500   ">
                    Receivable Amount
                </th>
                <th scope="col" class=" px-3 py-3 border text-left text-sm font-medium text-gray-500   ">
                    {{ number_format(collect($payments)->sum('gross_total')-collect($payments)->sum('receive_amount'),2) }}
                </th>
            </tr>
            <tr>
                <th colspan="8">&nbsp;</th>
            </tr>
            </tbody>
        </table>
    </div>


</div>