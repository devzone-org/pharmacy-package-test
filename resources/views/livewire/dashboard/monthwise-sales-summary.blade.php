<div>
    <dl class="mt-2 grid grid-cols-1 gap-1 sm:grid-cols-2 lg:grid-cols-7">
        <div class="relative bg-white shadow rounded-lg overflow-hidden">
            <div class="top-0 bg-gray-50 px-5 py-4">
                <div class="text-sm">
                    <p class="text-lg leading-6 font-medium text-gray-900">
                        Months
                    </p>
                </div>
            </div>
            <div class="">
                @foreach($data as $key=>$d)
                    <dd class="px-4 py-2 flex items-baseline @if($key==0) bg-green-700 @elseif($key==1) bg-green-600 @else bg-green-500 @endif">
                        <p class="text-base font-semibold  text-white">
                            @if(date('M, Y',strtotime($d['date']))==date('M, Y'))
                                Current Month
                            @else Previous Month @endif - {{date('M, Y',strtotime($d['date']))}}
                        </p>
                    </dd>
                    @if(!$loop->last)
                        <hr>
                    @endif
                @endforeach
            </div>
        </div>
        @for($i=1;$i<=6;$i++)
            <div class="relative bg-white shadow rounded-lg overflow-hidden">
                <div class="top-0 bg-gray-50 px-5 py-4">
                    <div class="text-sm">
                        <p class="text-lg leading-6 font-medium text-gray-900">
                            @if($i==1)
                                Sales - # of Sales
                            @elseif($i==2)
                                COS
                            @elseif($i==3)
                                Sales Return
                            @elseif($i==4)
                                Gross Profit - Margin (%)
                            @elseif($i==5)
                                Purchases
                            @elseif($i==6)
                                Stock Closing Balance
                            @endif
                        </p>
                    </div>
                </div>
                <div class="">
                    @foreach($data as $key=>$d)
                        <dd class="px-4 py-2 flex items-baseline @if($key==0) bg-green-700 @elseif($key==1) bg-green-600 @else bg-green-500 @endif">
                            <p class="text-base font-semibold text-white">
                                @if($i==1)
                                    {{number_format($d['total_after_refund'],2)}} (PKR) - # {{number_format($d['no_of_sale'])}}
                                @elseif($i==2)
                                    {{number_format($d['cos'],2)}} (PKR)
                                @elseif($i==3)
                                    {{number_format($d['total_refund'],2)}} (PKR)
                                @elseif($i==4)
                                    @php
                                    $divide=empty($d['total_after_refund']) ? 1 : $d['total_after_refund'];
                                    @endphp
                                    {{number_format($d['total_profit'],2)}} (PKR) - {{number_format((($d['total_after_refund']-$d['cos'])/$divide)*100,2)}} %
                                @elseif($i==5)
                                    {{number_format($d['purchase'],2)}} (PKR)
                                @elseif($i==6)
                                    {{number_format($d['closing_balance'],2)}} (PKR)
                                @endif

                            </p>
{{--                            <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">--}}
{{--                                <!-- Heroicon name: solid/arrow-sm-up -->--}}
{{--                                <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500"--}}
{{--                                     xmlns="http://www.w3.org/2000/svg"--}}
{{--                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
{{--                                    <path fill-rule="evenodd"--}}
{{--                                          d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"--}}
{{--                                          clip-rule="evenodd"/>--}}
{{--                                </svg>--}}
{{--                                <span class="sr-only">--}}
{{--                                    Increased by--}}
{{--                                </span>--}}
{{--                                122--}}
{{--                            </p>--}}
                        </dd>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        @endfor

        {{--        <div class="relative bg-white shadow rounded-lg overflow-hidden">--}}
        {{--            <div class="top-0 bg-gray-50 px-5 py-4">--}}
        {{--                <div class="text-sm">--}}
        {{--                    <p class="font-medium  text-indigo-600 hover:text-indigo-500"> View all</p>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--            <div class="pt-2 px-4 pb-1">--}}
        {{--                <dt>--}}
        {{--                    <p class="ml-1 text-sm font-medium text-gray-500 truncate">Avg. Click Rate</p>--}}
        {{--                </dt>--}}
        {{--                <dd class="ml-1 pb-1 flex items-baseline">--}}
        {{--                    <p class="text-2xl font-semibold text-gray-900">--}}
        {{--                        24.57%--}}
        {{--                    </p>--}}
        {{--                    <p class="ml-2 flex items-baseline text-sm font-semibold text-red-600">--}}
        {{--                        <!-- Heroicon name: solid/arrow-sm-down -->--}}
        {{--                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg"--}}
        {{--                             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
        {{--                            <path fill-rule="evenodd"--}}
        {{--                                  d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"--}}
        {{--                                  clip-rule="evenodd"/>--}}
        {{--                        </svg>--}}
        {{--                        <span class="sr-only">--}}
        {{--                        Decreased by--}}
        {{--                    </span>--}}
        {{--                        3.2%--}}
        {{--                    </p>--}}
        {{--                </dd>--}}
        {{--                <dt>--}}
        {{--                    <p class="ml-1 text-sm font-medium text-gray-500 truncate">Avg. Click Rate</p>--}}
        {{--                </dt>--}}
        {{--                <dd class="ml-1 pb-1 flex items-baseline">--}}
        {{--                    <p class="text-2xl font-semibold text-gray-900">--}}
        {{--                        24.57%--}}
        {{--                    </p>--}}
        {{--                    <p class="ml-2 flex items-baseline text-sm font-semibold text-red-600">--}}
        {{--                        <!-- Heroicon name: solid/arrow-sm-down -->--}}
        {{--                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg"--}}
        {{--                             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
        {{--                            <path fill-rule="evenodd"--}}
        {{--                                  d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"--}}
        {{--                                  clip-rule="evenodd"/>--}}
        {{--                        </svg>--}}
        {{--                        <span class="sr-only">--}}
        {{--                        Decreased by--}}
        {{--                    </span>--}}
        {{--                        3.2%--}}
        {{--                    </p>--}}
        {{--                </dd>--}}
        {{--                <dt>--}}
        {{--                    <p class="ml-1 text-sm font-medium text-gray-500 truncate">Avg. Click Rate</p>--}}
        {{--                </dt>--}}
        {{--                <dd class="ml-1 pb-1 flex items-baseline">--}}
        {{--                    <p class="text-2xl font-semibold text-gray-900">--}}
        {{--                        24.57%--}}
        {{--                    </p>--}}
        {{--                    <p class="ml-2 flex items-baseline text-sm font-semibold text-red-600">--}}
        {{--                        <!-- Heroicon name: solid/arrow-sm-down -->--}}
        {{--                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg"--}}
        {{--                             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
        {{--                            <path fill-rule="evenodd"--}}
        {{--                                  d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"--}}
        {{--                                  clip-rule="evenodd"/>--}}
        {{--                        </svg>--}}
        {{--                        <span class="sr-only">--}}
        {{--                        Decreased by--}}
        {{--                    </span>--}}
        {{--                        3.2%--}}
        {{--                    </p>--}}
        {{--                </dd>--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </dl>
</div>