<div class="max-w-3xl mx-auto mt-5   lg:max-w-7xl   lg:grid lg:grid-cols-12 lg:gap-4">

    <main class="col-span-12 ">


        <div class="lg:flex  lg:justify-between  ">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl mb-3 font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Sale Invoice
                </h2>

            </div>
            <div class="mt-5 flex lg:mt-0 lg:ml-4 ">

                <span class="">
      <button type="button" wire:click="searchableOpenModal('referred_by_id', 'referred_by_name', 'referred_by')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">


        Add Referred By
      </button>
    </span>

                <span class="ml-3">
      <button type="button" wire:click="searchableOpenModal('patient_id', 'patient_name', 'patient')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">


        Add Patient
      </button>
    </span>


                <span class="ml-3">
      <button type="button" wire:click="searchableOpenModal('product_id', 'product_name', 'item')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>

        Search Item (F1)
      </button>
    </span>


                <span class="ml-3">
      <button type="button" wire:click="saleComplete"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
               xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

        Complete Sale (F2)
      </button>
    </span>

            </div>

        </div>

        <div class="grid mb-3  bg-white gap-x-4 gap-y-8 grid-cols-4   shadow rounded-md p-3">
            <div class="">
                <dt class="text-sm font-medium text-gray-500">
                    Referred By
                </dt>
                <dd class="mt-1 text-xl font-medium text-gray-900">
                    {{ $referred_by_name ?? '-' }}
                </dd>
            </div>
            <div class="">
                <dt class="text-sm font-medium text-gray-500">
                    Patient Name
                </dt>
                <dd class="mt-1 text-xl font-medium text-gray-900">
                    {{ $patient_name ?? 'Walk-in' }}
                </dd>
            </div>
            <div class="">
                <dt class="text-sm font-medium text-gray-500">
                    Date
                </dt>
                <dd class="mt-1 text-xl font-medium text-gray-900">
                    {{ date('d M, Y') }}
                </dd>
            </div>
            <div class="">
                <dt class="text-sm font-medium text-gray-500">
                    Sale By
                </dt>
                <dd class="mt-1 text-xl font-medium text-gray-900">
                    {{ Auth::user()->name }}
                </dd>
            </div>
        </div>


        @if(!empty($success))
            <div class="rounded-md mb-5 bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/check-circle -->
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                             fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ $success }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" wire:click="$set('success','')"
                                    class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                <span class="sr-only">Dismiss</span>
                                <!-- Heroicon name: solid/x -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($error))
            <div class="rounded-md mb-5 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">

                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ $error }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" wire:click="$set('error','')"
                                    class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                <span class="sr-only">Dismiss</span>
                                <!-- Heroicon name: solid/x -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif


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
                        Gross Total
                    </th>
                    <th scope="col"
                        class="w-10 cursor-pointer px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">

                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sales as $key => $s)
                    <tr>
                        <td class="px-2  text-center  border-r text-md font-medium text-gray-900">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-2  text-left   border-r text-md text-gray-500">
                            {{ $s['item'] }}
                        </td>
                        <td class="px-2   text-left   border-r  text-md text-gray-500">
                            <input type="number" wire:model.lazy="sales.{{ $key }}.s_qty" onClick="this.select();"
                                   class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                   autocomplete="off">
                        </td>
                        <td class="px-2  text-center  border-r text-md text-gray-500">
                            <input type="number" wire:model.lazy="sales.{{ $key }}.retail_price"
                                   onClick="this.select();"
                                   class="p-0 focus:ring-0 block w-full  text-md border-0 text-center "
                                   autocomplete="off">

                        </td>
                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                            {{ number_format($s['total'],2) }}
                        </td>
                        <td class="px-2  text-center border-r text-md text-gray-500">
                            <input type="number" step="0.01" wire:model.lazy="sales.{{ $key }}.disc"
                                   onClick="this.select();"
                                   class="text-center p-0 focus:ring-0 block w-full   text-md border-0  "
                                   autocomplete="off">
                        </td>
                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                            {{ number_format($s['total'] - $s['total_after_disc'],2) }}
                        </td>
                        <td class="px-2 bg-gray-50  text-center border-r text-md text-gray-500">
                            {{ number_format($s['total_after_disc'],2) }}
                        </td>
                        <td class="  w-10 cursor-pointer px-2 py-3   border-r text-center text-md font-medium text-red-700  tracking-wider  ">
                            <svg wire:click="removeEntry('{{ $key }}')" class="w-5 h-5 " fill="currentColor"
                                 viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th scope="col" colspan="2"
                        class="w-7 px-2   border-r py-2 text-right text-md font-medium text-gray-500  tracking-wider">
                        Total
                    </th>
                    <th scope="col"
                        class="w-10   px-2 py-2   border-r text-center text-md font-medium text-gray-500 uppercase tracking-wider">
                        {{ collect($sales)->sum('s_qty') }}
                    </th>
                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
                    </th>
                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-center text-md font-medium text-gray-500  tracking-wider">
                        {{ number_format(collect($sales)->sum('total'),2) }}
                    </th>

                    <th scope="col"
                        class="w-7 px-2   border-r py-2 text-right text-xs font-medium text-gray-500  tracking-wider">
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

                <tr>
                    <th class="p-2">&nbsp;</th>
                </tr>
                <tr class="bg-gray-50">
                    <th rowspan="4" colspan="3"
                        class="  border-r   bg-white text-md font-medium text-gray-500  tracking-wider">

                        <textarea name="" cols="30" rows="5" id="remarks"
                                  class="p-0 focus:ring-0 block w-full border-0 text-md resize-none h-40  "></textarea>

                    </th>
                    <th scope="col" colspan="4"
                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                        Sub Total
                    </th>
                    <th scope="col" colspan="2"
                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales)->sum('total'),2) }}
                    </th>
                </tr>

                <tr>
                    <th scope="col" colspan="4"
                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                        Discount (F3) (%)
                    </th>
                    <th scope="col" colspan="2"
                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                        <input type="number" wire:model.lazy="discount" min="0" max="100" onClick="this.select();"
                               id="discount"
                               class="p-0 focus:ring-0 block w-full  text-xl border-0 font-medium text-gray-500 text-center "
                               autocomplete="off">
                    </th>
                </tr>
                <tr class="bg-gray-50">
                    <th scope="col" colspan="4"
                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                        Gross Total
                    </th>
                    <th scope="col" colspan="2"
                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format(collect($sales)->sum('total_after_disc'),2) }}
                    </th>
                </tr>

                <tr>
                    <th scope="col" colspan="4"
                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                        Received Amount (F4)
                    </th>
                    <th scope="col" colspan="2"
                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                        <input type="number" wire:model.debounce.300ms="received" onClick="this.select();" id="received"
                               wire:keydown.enter="saleComplete"
                               class="p-0 focus:ring-0 block w-full  text-xl border-0 font-medium text-gray-500 text-center "
                               autocomplete="off">
                    </th>
                </tr>

                <tr class="bg-gray-50">
                    <th scope="col" colspan="2"
                        class="w-7 px-2    py-2 text-left text-md font-medium text-gray-500  tracking-wider">
                        Remarks (F5)
                    </th>

                    <th scope="col" colspan="5"
                        class="w-7 px-2   border-r py-2 text-right text-xl font-medium text-gray-500  tracking-wider">
                        Change
                    </th>
                    <th scope="col" colspan="2"
                        class="w-10   px-2 py-2   border-r text-center text-xl font-medium text-gray-500 uppercase tracking-wider">
                        {{ number_format($payable,2) }}
                    </th>
                </tr>
                </tbody>
            </table>
        </div>


    </main>

    <div x-data="{ open: @entangle('choose_till') }" x-cloak x-show="open"
         class="fixed z-40 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-description="Background overlay, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
            <div @click.away="open = false;" x-show="open" x-description="Modal panel, show/hide based on modal state."
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="h-1/3 inline-block align-bottom bg-white rounded-lg  text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full  "
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="p-3">
                    <fieldset>
                        <h3 class="mb-3 text-md font-medium text-gray-500">List of All Tills</h3>
                        <div class="bg-white rounded-md -space-y-px">
                            @foreach($tills as $key => $t)
                                <label
                                    class="border-gray-200 rounded-tl-md rounded-tr-md relative border p-4 flex cursor-pointer">
                                    <input type="radio" wire:model="till_id" name="till_id" value="{{ $t['id'] }}"
                                           class="h-4 w-4 mt-0.5 cursor-pointer text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                           aria-labelledby="privacy-setting-0-label"
                                           aria-describedby="privacy-setting-0-description">
                                    <div class="ml-3 flex flex-col">
                                        <span id="till-{{ $key }}-label"
                                              class="text-gray-900 block text-sm font-medium">
                                          {{ $t['name'] }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <button type="button" wire:click="updateTill" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500">
                        Update Till
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('pharmacy::include.searchable')
</div>

<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('focusInput', postId => {
            setTimeout(() => {
                document.getElementById('searchable_query').focus();
            }, 100);
        });
    });

    document.addEventListener("keydown", event => {
        if (event.keyCode == 112) {
            event.preventDefault();
            event.stopPropagation();
            window.livewire.emit('openSearch');
        }

        if (event.keyCode == 113) {
            event.preventDefault();
            event.stopPropagation();
            window.livewire.emit('saleComplete');
        }

        if (event.keyCode == 114) {
            event.preventDefault();
            event.stopPropagation();
            const input = document.getElementById('discount');
            input.focus();
            input.select();

        }

        if (event.keyCode == 115) {
            event.preventDefault();
            event.stopPropagation();
            const input = document.getElementById('received');
            input.focus();
            input.select();
        }

        if (event.keyCode == 116) {
            event.preventDefault();
            event.stopPropagation();
            const input = document.getElementById('remarks');
            input.focus();
            input.select();

        }
    });
</script>
