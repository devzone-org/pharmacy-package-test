<div>
    <div class="shadow mb-5 overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
            <div class="grid grid-cols-8 gap-6">


                <div class="col-span-8 sm:col-span-2">
                    <label for="from" class="block text-sm font-medium text-gray-700">Sale From</label>
                    <input type="text" wire:model.lazy="from" id="from" readonly autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="to" class="block text-sm font-medium text-gray-700">Sale To</label>
                    <input type="text" wire:model.lazy="to" readonly id="to" autocomplete="off"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select type="text" wire:model.defer="status"  id="type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value=""></option>
                        <option value="e-pre">E-Prescription</option>
                        <option value="n-pre">Pending</option>
                    </select>
                </div>

                <div class="col-span-8 sm:col-span-2">
                    <button type="button" wire:click="search" wire:loading.attr="disabled"
                            class="bg-white mt-6 py-2 px-4 rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                        <div wire:loading wire:target="search">
                            Searching ...
                        </div>
                        <div wire:loading.remove wire:target="search">
                            Search
                        </div>
                    </button>
                </div>


            </div>
        </div>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Pending Sales</h3>

                            </div>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    #
                                </th>


                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Sale At
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Sale By
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Patient
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Referred by
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Sub Total
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Discount
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Net Sale
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Type
                                </th>


                                <th scope="col" class="relative px-3 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                            @foreach($history as $h)
                                <tr>
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration + ( $history->firstItem() - 1)   }}
                                    </td>

                                    @php
                                        $time_pass=\Carbon\Carbon::create($h->sale_at)->diffForHumans();
                                    @endphp
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ date('d M, y h:i A',strtotime($h->sale_at)) }}
                                        @if(\Carbon\Carbon::create($h->sale_at)->diffInMinutes(now()) >= 10)
                                            <br>
                                            <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                {{$time_pass}}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">

                                        {{ $h->sale_by }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if(!empty($h->patient_name))
                                            {{ $h->patient_name }}
                                            <br>
                                            {{ $h->mr_no }}
                                        @else
                                            Walk in
                                        @endif

                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $h->referred_by }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->sub_total,2) }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($h->sub_total - $h->gross_total,2) }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-500">
                                        {{ number_format($h->gross_total,2) }}
                                    </td>

                                    <td class="px-3 py-3 text-sm text-gray-500">
                                        @if(empty($h->opd_id))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-green-100 text-green-800">
                                                E-Prescription
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3   text-right text-sm font-medium">
                                        <div class="flex flex-row-reverse">
                                            <a href="#" class="text-red-600" wire:click="delete('{{ $h->id }}')">
                                                Delete</a>
                                            <a class="text-indigo-600 pr-2"
                                               href="{{ url('pharmacy/sales/add') }}?pending_sale_id={{ $h->id }}">Complete
                                                |</a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        @if($history->hasPages())
                            <div class="bg-white border-t px-3 py-2">
                                {{ $history->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>


    </div>

</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <script>
        let from_date = new Pikaday({
            field: document.getElementById('from'),
            format: "DD MMM YYYY"
        });

        let to_date = new Pikaday({
            field: document.getElementById('to'),
            format: "DD MMM YYYY"
        });

        from_date.setDate(new Date('{{ $from }}'));
        to_date.setDate(new Date('{{ $to }}'));
    </script>


