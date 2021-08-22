<div>


    <div class="flex flex-col mt-5">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="  min-w-full   ">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3  text-center text-sm font-medium text-gray-900 uppercase tracking-wider">

                            </th>
                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                Sales
                            </th>
                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                No. of Sales
                            </th>
                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                COS
                            </th>

                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                Sales Return
                            </th>

                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                Gross Profit
                            </th>

                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                Margin (%)
                            </th>

                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                Purchases
                            </th>

                            <th scope="col"
                                class="px-6 py-3   text-center text-sm font-medium text-gray-900 uppercase tracking-wider">
                                Closing Stock
                            </th>

                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y-4 divide-white">
                        @foreach($data as $key => $d)
                            <tr  style=" @if($key==0) background-color:rgba(38, 159, 251,0.5); @elseif($key==1)  background-color:rgba(38, 231, 165,0.5); @else  background-color:rgba(254, 187, 59,0.5); @endif">
                                <td class="px-2 py-1  text-white whitespace-nowrap text-lg    text-right  text-gray-900">
                                    {{ $d['date'] }}
                                </td>
                                <td class="px-2 py-1  font-medium whitespace-nowrap text-lg text-center text-gray-900">
                                   PKR {{ number_format($d['total_after_refund']) }}
                                </td>
                                <td class="px-2 py-1   font-medium  whitespace-nowrap text-lg text-center text-gray-900">
                                    {{ number_format($d['no_of_sale']) }}
                                </td>
                                <td class="px-2 py-1  font-medium   whitespace-nowrap text-lg text-center text-gray-900">
                                  PKR  {{ number_format($d['cos']) }}
                                </td>

                                <td class="px-2 py-1  font-medium   whitespace-nowrap text-lg text-center text-gray-900">
                                   PKR {{ number_format($d['total_refund']) }}
                                </td>

                                <td class="px-2 py-1   font-medium  whitespace-nowrap text-lg text-center text-gray-900">
                                  PKR  {{ number_format($d['total_profit']) }}
                                </td>

                                <td class="px-2 py-1   font-medium  whitespace-nowrap text-lg text-center text-gray-900">
                                    @if($d['total_after_refund']>0)
                                       PKR {{ number_format( (($d['total_after_refund'] - $d['cos']) / $d['total_after_refund'])  * 100) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-2 py-1  font-medium   whitespace-nowrap text-lg text-center text-gray-900">
                                   PKR {{ number_format($d['purchase']) }}
                                </td>

                                <td class="px-2 py-1  font-medium   whitespace-nowrap text-lg text-center text-gray-900">
                                   PKR {{ number_format($d['closing_balance']) }}
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
