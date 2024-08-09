<div class="max-w-full">
    <div class="  flex border-t bg-gray-100">

        <div class="  md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">

                <div class="flex flex-col flex-grow border-r border-gray-200   overflow-y-auto">
                    <div class="h-full   bg-white p-2 overflow-y-auto">
                        <div class=" mt-6">
                            <div>
                                <dl class="space-y-4 ">

                                    <div class="cursor-pointer   pt-2">
                                        <dt class="text-sm font-medium  text-gray-500   sm:flex-shrink-0">
                                            Referred By
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                {{ $referred_by }}
                                            </p>
                                        </dd>
                                    </div>

                                    <div class="cursor-pointer">
                                        <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:flex-shrink-0">
                                            Patient Name
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                {{ $patient_name ?? 'Walk-in' }}
                                            </p>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Sale On Credit
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                <input type="checkbox" wire:model.defer="on_credit"
                                                       class="focus:ring-red-500 h-8 w-8 text-red-600 border-gray-300 rounded">

                                            </p>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Credit Limit
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                PKR {{ number_format($credit_limit) }}
                                            </p>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Closing Balance
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                PKR {{ number_format($closing_balance) }}
                                            </p>
                                        </dd>
                                    </div>

                                    <div class=" border-t pt-2">
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Date
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                {{ date('d M, Y h:i A',strtotime($sale_at)) }}
                                            </p>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500   sm:flex-shrink-0">
                                            Sale By
                                        </dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2">
                                            <p>
                                                {{ $sale_by }}
                                            </p>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="flex flex-col   flex-1 overflow-hidden">
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6 px-4">
                    <div class="lg:flex col-span-12  lg:justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl mb-3 font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">

                                @if($on_credit) Credit @endif Sale Invoice # {{ $sale_id }}

                            </h2>
                        </div>
                        <div class="mt-5 flex lg:mt-0 lg:ml-4 ">

                           <span class="ml-3">
                   <a class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      href="javascript:void(0);"
                      onclick="window.open('{{ url('pharmacy/print/sale/').'/'.$sale_id }}','receipt-print','height=150,width=400');">
                       Print Sale
                   </a>
                </span>

                        </div>

                    </div>

                    <main class="col-span-12">


                        <div class="bg-white  overflow-hidden  shadow rounded-lg">
                            <table class="min-w-full table-fixed divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="  px-2   border-r py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                                        Item
                                    </th>
                                    <th scope="col"
                                        class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Qty
                                    </th>
                                    <th scope="col"
                                        class="w-28 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Unit Price
                                    </th>
                                    <th scope="col"
                                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Total
                                    </th>

                                    <th scope="col"
                                        class="w-20 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Disc %
                                    </th>
                                    <th scope="col" title="Total After Disc"
                                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Disc PKR
                                    </th>

                                    <th scope="col" title="Total After Disc"
                                        class="w-32 px-2 py-2   border-r text-center text-md font-medium text-gray-500  tracking-wider">
                                        Net Total
                                    </th>

                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @if(!empty($sales))
                                    <tr>
                                        <th colspan="8" class="px-2 py-2 text-left border-r text-md text-gray-900">
                                            <i>Sale Entries</i>
                                        </th>
                                    </tr>
                                @endif
                                @foreach($sales as $key => $s)
                                    <tr class="">
                                        <td class="px-2 py-2  text-center  border-r text-md font-medium text-gray-900">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-2 text-left border-r text-md text-gray-500">
                                            {{ $s['product_name'] }}

                                        </td>
                                        <td class="px-2   text-center   border-r  text-md text-gray-500">

                                            {{ number_format($s['qty']) }}
                                        </td>
                                        <td class="px-2  text-center  border-r text-md text-gray-500">

                                            {{ number_format($s['retail_price'],2) }}
                                        </td>
                                        <td class="px-2    text-center border-r text-md text-gray-500">
                                            {{ number_format($s['total'],2) }}
                                        </td>

                                        <td class="px-2  text-center border-r text-md text-gray-500">
                                            {{ number_format($s['disc'],2) }}
                                        </td>
                                        <td class="px-2   text-center border-r text-md text-gray-500">
                                            {{ number_format($s['total'] - $s['total_after_disc'],2) }}
                                        </td>

                                        <td class="px-2   text-center border-r text-md text-gray-500">
                                            {{ number_format($s['total_after_disc'],2) }}
                                        </td>

                                    </tr>
                                @endforeach
                                @if(!empty($sales))
                                    <tr>
                                        <th scope="col" colspan="2"
                                            class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col"
                                            class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                                            {{ collect($sales)->sum('qty') }}
                                        </th>
                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                                        </th>
                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format(collect($sales)->sum('total'),2) }}
                                        </th>

                                        <th scope="col"
                                            class="w-7 px-2  border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                                        </th>

                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format(collect($sales)->sum('total') - collect($sales)->sum('total_after_disc'),2) }}
                                        </th>


                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format(collect($sales)->sum('total_after_disc'),2) }}

                                        </th>
                                    </tr>
                                @endif

                                @if(!empty($refunds))
                                    <tr>
                                        <th colspan="8" class="px-2 py-2 text-left border-r text-md text-red-600">
                                            <i>Sale Returns Entries</i>
                                        </th>
                                    </tr>
                                @endif
                                @php
                                    $total_refund = 0;
                                @endphp
                                @foreach($refunds as $key => $s)
                                    <tr class="">
                                        <td class="px-2 py-2  text-center  border-r text-md font-medium text-gray-900">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-2 text-left border-r text-md text-gray-500">
                                            {{ $s['product_name'] }}

                                        </td>
                                        <td class="px-2   text-center   border-r  text-md text-gray-500">

                                            {{ number_format($s['refund_qty']) }}
                                        </td>
                                        <td class="px-2  text-center  border-r text-md text-gray-500">

                                            {{ number_format($s['retail_price'],2) }}
                                        </td>
                                        <td class="px-2    text-center border-r text-md text-gray-500">
                                            {{ number_format($s['refund_qty'] *$s['retail_price'] ,2) }}
                                        </td>

                                        <td class="px-2  text-center border-r text-md text-gray-500">
                                            {{ number_format($s['disc'],2) }}
                                        </td>
                                        <td class="px-2   text-center border-r text-md text-gray-500">
                                            {{ number_format(($s['refund_qty'] *$s['retail_price']) - ($s['refund_qty'] * $s['retail_price_after_disc']),2) }}
                                        </td>

                                        <td class="px-2   text-center border-r text-md text-gray-500">
                                            {{ number_format($s['refund_qty'] * $s['retail_price_after_disc'],2) }}
                                        </td>
                                        @php
                                            $total_refund = $total_refund + ($s['refund_qty'] * $s['retail_price_after_disc']);
                                        @endphp
                                    </tr>
                                @endforeach
                                @if(!empty($refunds))
                                    <tr>
                                        <th scope="col" colspan="2"
                                            class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col"
                                            class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                                            {{ collect($refunds)->sum('refund_qty') }}
                                        </th>
                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                                        </th>
{{--                                        @dd($refunds)--}}
                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format(collect($refunds)->sum('total'),2) }}
                                        </th>

                                        <th scope="col"
                                            class="w-7 px-2  border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                                        </th>

                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format(collect($refunds)->sum('total') - $total_refund,2) }}
                                        </th>


                                        <th scope="col"
                                            class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                                            {{ number_format($total_refund,2) }}

                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <th colspan="8" class="px-2 py-2 text-left border-r text-md text-red-600">
                                        &nbsp;
                                    </th>
                                </tr>

                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">
                                        Sale Sub Total
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        {{ number_format($first['sub_total'],2) }}
                                    </th>
                                </tr>

                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">
                                        Discount
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        {{ number_format($first['sub_total'] - $first['gross_total'],2) }}
                                    </th>
                                </tr>

                                @php
                                    $val = 0;
                                    if (!empty($first['rounded_inc'])){
                                        $val = $first['rounded_inc'];
                                    }elseif (!empty($first['rounded_dec'])){
                                        $val = -1 * $first['rounded_dec'];
                                    }
                                @endphp

                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">
                                        Sale after Discount @if($val != 0) <br><span
                                                class="text-xs"> (After Round Off)</span> @endif
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        {{ number_format($first['gross_total']+$val ,2) }}
                                    </th>
                                </tr>
                                @php
                                    $refunded = 0;
                                @endphp
                                @if(!empty($first['refunded_id']))
                                    @php
                                        $total_refund = \Devzone\Pharmacy\Models\Sale\SaleRefundDetail::from('sale_refund_details as sr')
                                        ->join('sale_details as sd','sd.id','=','sr.sale_detail_id')
                                        ->where('sr.sale_id',$first['refunded_id'])
                                        ->where('sr.refunded_id',$sale_id)
                                        ->select(
                                        \Illuminate\Support\Facades\DB::raw('sum(sr.refund_qty * sd.retail_price_after_disc) as refund')
                                        )
                                        ->first();

                                        $refunded = $total_refund['refund'];
                                    @endphp
                                @endif
                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">
                                        Sale Returns
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        ({{ number_format($refunded,2) }})
                                    </th>
                                </tr>

                                @php
                                    $val = 0;
                                    $after_roundoff = 0;
                                    if (!empty($first['rounded_inc'])){
                                        $val = $first['rounded_inc'];
                                    }elseif (!empty($first['rounded_dec'])){
                                        $val = -1 * $first['rounded_dec'];
                                    }
                                    $after_roundoff = $refunded - ($first['gross_total'] + $val);
                                    if($first['is_credit'] != 'f'){
                                        $after_roundoff = $refunded - $first['gross_total'];
                                    }
                                @endphp

                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">
                                        Net Sales
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        @if($first['gross_total']- $refunded>0)
                                            {{ number_format(abs($after_roundoff),2) }}
                                        @else

                                    ({{ number_format(abs($after_roundoff),2) }})
                                        @endif
                                    </th>
                                </tr>

                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">

                                        @if($after_roundoff > 0)
                                            (Refund)
                                        @else
                                            @if($first['is_credit'] == 'f')
                                                Cash
                                            @endif
                                        @endif
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        @if($after_roundoff > 0)
                                            ({{ number_format(abs($after_roundoff),2) }})
                                        @else
                                            @if($first['is_credit'] == 'f')
                                                {{ number_format(abs($after_roundoff),2) }}

                                            @endif
                                        @endif
                                    </th>
                                </tr>

                                <tr>
                                    <th colspan="5" class="px-2 py-2 text-right border-r text-md text-gray-900">
                                        Credit
                                    </th>
                                    <th colspan="3" class="px-2 py-2 text-center border-r text-md text-gray-900">
                                        @if($first['is_credit'] == 't')
                                            {{ number_format(abs($refunded - $first['gross_total']),2) }}
                                        @else
                                            -
                                        @endif
                                    </th>
                                </tr>


                                </tbody>
                            </table>
                        </div>


                    </main>
                </div>
            </main>
        </div>
    </div>
</div>