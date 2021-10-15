<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">
                <div class="col-span-8 sm:col-span-2">
                    <label for="customer_name"
                           class="block text-sm font-medium text-gray-700">
                        Customer Name
                    </label>
                    <input type="text" wire:model.defer="customer_name" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label for="customer_mrn"
                           class="block text-sm font-medium text-gray-700">
                        MRN #
                    </label>
                    <input type="text" wire:model.defer="customer_mrn" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="col-span-8 sm:col-span-2">
                    <label for="care_of"
                           class="block text-sm font-medium text-gray-700">Care Of</label>
                    <select wire:model.defer="care_of" id="care_of"
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value=""></option>
                        @foreach($all_employees as $emps)
                            <option value="{{ $emps['id'] }}">{{ $emps['name'] }}</option>
                        @endforeach
                    </select>
                </div>
{{--                <div class="col-span-8 sm:col-span-2">--}}
{{--                    <label for="per_page"--}}
{{--                           class="block text-sm font-medium text-gray-700">Per Page</label>--}}
{{--                    <select wire:model.defer="per_page"--}}
{{--                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">--}}
{{--                        <option value="10">10</option>--}}
{{--                        <option value="20">20</option>--}}
{{--                        <option value="30">30</option>--}}
{{--                        <option value="40">40</option>--}}
{{--                        <option value="50">50</option>--}}
{{--                    </select>--}}
{{--                </div>--}}

                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search"
                            class="bg-white mt-6 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>

                    <button type="button" wire:click="resetSearch"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5  shadow sm:rounded-md">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2  sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="bg-white py-6 px-4 space-y-6 sm:p-6  rounded-t-md">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Customer Receivables
                            </h3>
                        </div>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                Sr #
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                MRN #
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                Customer Name
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                Receivable (PKR)
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                Care Of
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 ">
                        @foreach($report as $rep)
                            @php
                                list($name, $mrn) = explode("-", $rep->name);
                            @endphp
                            <tr class="hover:bg-gray-100">
                                <td class="px-3 py-3 text-center  text-sm font-medium text-gray-500">
                                    {{$loop->iteration}}
                                </td>
                                <td class="px-3 py-3 text-center  text-sm font-medium text-gray-500">
                                    {{$name}}
                                </td>
                                <td class="px-3 py-3 text-center text-sm text-gray-500">
                                    {{$mrn}}
                                </td>
                                <td class="px-3 py-3 text-center text-sm text-gray-500">
                                    {{number_format($rep->rec)}}
                                </td>
                                <td class="px-3 py-3 text-center text-sm text-gray-500">
                                    {{$rep->care_of}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
{{--                        <tfoot>--}}
{{--                        <tr class="bg-gray-50">--}}
{{--                            <td class="px-3 py-3 text-sm font-medium text-center text-gray-900"--}}
{{--                                colspan="5">Grand Total--}}
{{--                            </td>--}}
{{--                            <td class="px-3 py-3 text-sm font-medium text-center text-gray-900">--}}
{{--                                <abbr style="text-decoration:none" title="Total Charges">--}}
{{--                                    {{number_format(collect($tests->items())->sum('total'),2)}}--}}
{{--                                </abbr>--}}
{{--                            </td>--}}
{{--                            <td class="px-3 py-3 text-sm font-medium text-center text-gray-900">--}}
{{--                                <abbr style="text-decoration:none" title="Total Discounted">--}}
{{--                                    {{number_format(collect($tests->items())->sum('discount'),2)}}--}}
{{--                                </abbr>--}}
{{--                            </td>--}}
{{--                            <td class="px-3 py-3 text-sm font-medium text-center text-gray-900">--}}
{{--                                <abbr style="text-decoration:none" title="Total Refunded">--}}
{{--                                    {{number_format(collect($tests->items())->sum('refunded'),2)}}--}}
{{--                                </abbr>--}}
{{--                            </td>--}}
{{--                            <td class="px-3 py-3 text-sm font-medium text-center text-gray-900">--}}
{{--                                <abbr style="text-decoration:none" title="Net Amount">--}}
{{--                                    {{number_format($net_total, 2)}}--}}
{{--                                </abbr>--}}
{{--                            </td>--}}
{{--                            <td class="px-3 py-3 text-sm font-medium text-center text-gray-900">--}}
{{--                                <abbr style="text-decoration:none" title="Total Received Amount">--}}
{{--                                    --}}{{--                                    {{number_format(collect($reports)->sum('paid'),2)}}--}}
{{--                                </abbr>--}}
{{--                            </td>--}}

{{--                            <td colspan="4"--}}
{{--                                class="px-3 py-3 text-sm font-medium text-right text-gray-900">--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        </tfoot>--}}
                    </table>
                    <div class="bg-white p-3 border-t rounded-b-md  ">
{{--                        {{ $report->links() }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>