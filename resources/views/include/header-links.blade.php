<a href="{{ url('pharmacy') }}"
   class="{{ Request::segment(1)=='pharmacy' && empty(Request::segment(2))? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
<div class="relative" x-data="{open:false}">

    <button type="button" @click="open=true;"
            class="{{ Request::segment(1)=='pharmacy' && Request::segment(2) == 'purchases' ? $a_current : $a_default }}  flex justify-center px-3 py-2 rounded-md text-sm font-medium"
            aria-expanded="false">
        <span>Purchases</span>

        <svg class="text-gray-400 ml-2 h-5 w-5 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"/>
        </svg>
    </button>

    <div x-show="open" @click.away="open=false"

         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"

         class="absolute  z-10 -ml-4 mt-3 transform px-2 w-screen max-w-md sm:px-0 lg:ml-0 lg:left-1/2 lg:-translate-x-1/2">
        <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                <a href="{{ url('pharmacy/purchases') }}"
                   class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='purchases' && empty(Request::segment(3))  ? 'bg-gray-100' : ''}} ">

                    <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>


                    <div class="ml-4">
                        <p class="text-base font-medium text-gray-900">
                            Purchase Orders
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            List of all purchase orders.
                        </p>
                    </div>
                </a>


                <a href="{{ url('pharmacy/purchases/payments') }}"
                   class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='purchases' && (Request::segment(3) == 'payments')  ? 'bg-gray-100' : ''}} ">
                    <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                    <div class="ml-4">
                        <p class="text-base font-medium text-gray-900">
                            Payments
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Make supplier payments.
                        </p>
                    </div>
                </a>


                <a href="{{ url('pharmacy/purchases/refund') }}"
                   class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='purchases' && (Request::segment(3) == 'refund')  ? 'bg-gray-100' : ''}} ">

                    <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                    <div class="ml-4">
                        <p class="text-base font-medium text-gray-900">
                            Returns
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Here you can return items to suppliers.
                        </p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
<div class="relative" x-data="{open:false}">

    <button type="button" @click="open=true;"
            class="{{ Request::segment(1)=='pharmacy' && Request::segment(2) == 'sales' ? $a_current : $a_default }}  flex justify-center px-3 py-2 rounded-md text-sm font-medium"
            aria-expanded="false">
        <span>Sales</span>

        <svg class="text-gray-400 ml-2 h-5 w-5 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"/>
        </svg>
    </button>

    <div x-show="open" @click.away="open=false"

         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"

         class="absolute  z-10 -ml-4 mt-3 transform px-2 w-screen max-w-md sm:px-0 lg:ml-0 lg:left-1/2 lg:-translate-x-1/2">
        <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                <a href="{{ url('pharmacy/sales') }}"
                   class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='sales' && empty(Request::segment(3))  ? 'bg-gray-100' : ''}} ">

                    <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>


                    <div class="ml-4">
                        <p class="text-base font-medium text-gray-900">
                            Sales
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Generate sale receipt.
                        </p>
                    </div>
                </a>


            </div>

        </div>
    </div>
</div>
<a href="{{ url('pharmacy/master-data') }}"
   class="{{ Request::segment(1)=='pharmacy' && Request::segment(2) == 'master-data' ? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Master
    Data</a>

