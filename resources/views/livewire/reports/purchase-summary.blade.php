<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-10 gap-6">
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
                        <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                        <input type="date" wire:model.defer="from" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="col-span-8 sm:col-span-2">
                        <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                        <input type="date" wire:model.defer="to" autocomplete="off"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                @endif
                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 sm:p-6 ">
                            <h3 class="text-lg leading-6  text-center font-medium text-gray-900">{{ env('APP_NAME') }}</h3>
                            <p class="text-md leading-6  text-center  text-gray-900">Purchase Summary Report</p>
                            <p class="text-md leading-6  text-center  text-gray-900">Statement period
                                from {{ date('d M, Y',strtotime($from)) }} to {{ date('d M, Y',strtotime($to)) }}</p>
                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Sr #
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500   ">
                                    Supplier Name
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500   ">
                                    Order Placement Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500   ">
                                    PO Created By
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    PO Approved By
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    PO #
                                </th>

                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Order Receiving Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    GRN #
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Supplier Invoice #
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Supplier Invoice Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Invoice Payment Status
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Invoice Payment Date
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Total PO Value
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500    ">
                                    Total COS
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @if(!empty($report))
                                @foreach($report as $r)
                                    <tr>
                                        <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                            {{ $loop->iteration  }}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['supplier_name']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{!empty($r['placement_date']) ? date('d M Y',strtotime($r['placement_date'])) : '-'}}
                                        </td>
                                        <td class="px-3 py-3  text-center text-sm text-gray-500">
                                            {{$r['created_by']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['approved_by']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['po_no']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{!empty($r['receiving_date']) ? date('d M Y',strtotime($r['receiving_date'])) : '-' }}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['grn_no']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{$r['supplier_invoice']}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{!empty($r['invoice_date']) ? date('d M Y',strtotime($r['invoice_date'])) : '-'}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            @if($r['is_paid']=='t')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                              Paid
                                            </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                              Unpaid
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{!empty($r['payment_date']) ? date('d M Y',strtotime($r['payment_date'])) : '-'}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['po_value'],2)}}
                                        </td>
                                        <td class="px-3 py-3 text-center  text-sm text-gray-500">
                                            {{number_format($r['cos'],2)}}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50">
                                    <th scope="col" colspan="12" class="px-3 py-3 text-left text-sm font-medium text-gray-500">

                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                        {{number_format(collect($report)->sum('po_value'),2)}}
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-500">
                                        {{number_format(collect($report)->sum('cos'),2)}}
                                    </th>
                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
