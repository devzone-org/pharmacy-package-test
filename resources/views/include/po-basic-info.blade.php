<div x-data="{ open: @entangle('basic_info') }" x-cloak x-show="open"
     class="fixed z-40 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open"
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

            <div class="  px-2 pt-2 pb-2">


                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6  ">
                        <label for="dateo" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                        <input wire:model.lazy="delivery_date" type="date" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="dateo">
                    </div>


                    <div class="col-span-6 ">
                        <label for="supplier_invoiceo" class="block text-sm font-medium text-gray-700">Supplier
                            Invoice</label>
                        <input wire:model.lazy="supplier_invoice" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="supplier_invoiceo">
                    </div>

                    <div class="col-span-6 ">
                        <label for="grn_noo" class="block text-sm font-medium text-gray-700">GRN #</label>
                        <input wire:model.lazy="grn_no" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="grn_noo">
                    </div>

                    <div class="col-span-6">
                        <button type="button" wire:click="updateBasicInfo"
                                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
