<nav class="lg:py-2 lg:flex lg:space-x-8" aria-label="Global">
    <a href="{{ url('pharmacy') }}"
       class="{{ Request::segment(1)=='pharmacy' && empty(Request::segment(2))? $a_current : '' }} rounded-md py-2 px-3 inline-flex items-center text-sm font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 active:bg-gray-50">
        Dashboard </a>
    <div class="relative z-20" x-data="{open:false}">
        <button @click="open=true;"
                class="{{ Request::segment(1)=='pharmacy' && Request::segment(2) == 'sales' ? $a_current : '' }} cursor-pointer  rounded-md py-2 px-3 inline-flex items-center text-sm font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 focus-within:bg-gray-50 focus-within:outline-none"
                aria-expanded="false">
            <span>Sales</span>

            <svg class="text-gray-500 ml-2 h-5 w-5 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        </button>

        <div x-show="open" x-cloak @click.away="open=false"

             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"

             class="absolute  z-10 -ml-4 mt-3 transform px-2 w-screen max-w-md sm:px-0 lg:ml-0 lg:left-1/2 lg:-translate-x-1/2">
            <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                <a href="{{ url('pharmacy/pending-sales') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='pending-sales' && empty(Request::segment(3))  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>

                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Pending Sales
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                History of all Pending sales.
                            </p>
                        </div>
                    </a>


                    <a href="{{ url('pharmacy/sales') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='sales' && empty(Request::segment(3))  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>

                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sale History
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                History of all sales.
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/sales/add') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='sales' && (Request::segment(3)== 'add')  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Add Sale
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Generate sale receipt.
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/sales/open-returns') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='sales' && (Request::segment(3)== 'open-returns')  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Open Returns
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Return products without invoice.
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/sales/admissions') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='sales' && (Request::segment(3)== 'admissions')  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Inter Transfer IPD Medicines
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Medicine used in Admissions Procedures.
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/customer/payments') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='customer' && (Request::segment(3)== 'payments')  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Customer Payments
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Make payments from customers.
                            </p>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
    <div class="relative z-20" x-data="{open:false}">
        <button @click="open=true;"
                class="{{ Request::segment(1)=='pharmacy' && Request::segment(2)=='purchases' ? $a_current : '' }} cursor-pointer  rounded-md py-2 px-3 inline-flex items-center text-sm font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 focus-within:bg-gray-50 focus-within:outline-none"
                aria-expanded="false">
            <span>Purchases</span>

            <svg class="text-gray-500 ml-2 h-5 w-5 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        </button>

        <div x-show="open" x-cloak @click.away="open=false"

             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"

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

                    <a href="{{ url('pharmacy/purchases/add')}}?loose_purchase=t"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='purchases' && Request::segment(3) == 'loose-purchase' ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                              Loose Purchase Orders
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Make loose purchase orders.
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
                    <a href="{{ url('pharmacy/purchases/stock-adjustment') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='purchases' && (Request::segment(3) == 'stock-adjustment')  ? 'bg-gray-100' : ''}} ">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Stock Adjustment
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Here you can adjust the stock.
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/purchases/expiry-adjustment') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='purchases' && (Request::segment(3) == 'expiry-adjustment')  ? 'bg-gray-100' : ''}} ">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Expiry Adjustment
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Here you can adjust the expiry dates of products.
                            </p>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
    <div class="relative z-20" x-data="{open:false}">

        <button type="button" @click="open=true;"
                class="{{ Request::segment(1)=='pharmacy' && Request::segment(2) == 'report' ? $a_current : '' }} cursor-pointer  rounded-md py-2 px-3 inline-flex items-center text-sm font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 focus-within:bg-gray-50 focus-within:outline-none"
                aria-expanded="false">
            <span>Reports</span>

            <svg class="text-gray-400 ml-2 h-5 w-5 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        </button>

        <div x-show="open" x-cloak @click.away="open=false"

             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"

             class="absolute  z-10 -ml-4 mt-3 transform px-2 w-screen max-w-md sm:px-0 lg:ml-0 lg:left-1/2 lg:-translate-x-1/2">
            <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                    <a href="{{ url('pharmacy/report/sale-transaction') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-transaction')  ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sales Transaction Report
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/sale-return-transaction') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-return-transaction')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sales Return Transaction Report
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/sale-summary') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-summary')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sales Summary
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/sale-doctorwise') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-doctorwise')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sales Doctor Wise
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/sale-productwise') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-productwise')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sales Product Wise
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/sale-hourly-graph') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-hourly-graph')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sales Hourly Graph
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/purchase-summary') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'purchase-summary')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Purchase Summary
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/purchases-details') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'purchases-details')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Purchases Details
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/stock-register') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'stock-register')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Stock Register
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/stock-in-out') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'stock-in-out')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Stock Movement
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/stock-reorder-level') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'stock-reorder-level')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Stock Reorder Level
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/stock-near-expiry') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'stock-near-expiry')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Stock Near Expiry
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/inter-transfer-IPD-medicines') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'inter-transfer-IPD-medicines')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Inter Transfer IPD Medicines
                            </p>
                        </div>
                    </a>
                    <a href="{{ url('pharmacy/report/inventory-ledger') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'inventory-ledger')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Inventory Ledger
                            </p>
                        </div>
                    </a>

                    <a href="{{ url('pharmacy/report/sale-purchase-narcotic-drugs') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'sale-purchase-narcotic-drugs')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Sale/Purchase Narcotic Drugs
                            </p>
                        </div>
                    </a>

                    <a href="{{ url('pharmacy/report/customer-receivables') }}"
                       class="-m-3 p-3 flex items-center rounded-lg hover:bg-gray-100 {{ Request::segment(2)=='report' && (Request::segment(3) == 'customer-receivables')   ? 'bg-gray-100' : ''}} ">
                        <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                        </svg>
                        <div class="ml-4">
                            <p class="text-base font-medium text-gray-900">
                                Customer Receivables
                            </p>
                        </div>
                    </a>

                </div>

            </div>
        </div>
    </div>

</nav>
