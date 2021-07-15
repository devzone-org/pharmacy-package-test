<fieldset>
    <div>
        <legend class="text-base font-medium text-gray-900">Inter Transfer IPD Medicine</legend>
        <p class="text-sm text-gray-500">Medicine will be transfer on the basis of </p>
    </div>
    <div class="mt-4 space-y-4">
        <div class="flex items-center">
            <input id="push-everything" wire:model="type" value="retail_price" name="push-notifications" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
            <label for="push-everything" class="ml-3 block text-sm font-medium text-gray-700">
                Retail Price
            </label>
        </div>
        <div class="flex items-center">
            <input id="push-email" wire:model="type" value="cost_of_price" name="push-notifications" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
            <label for="push-email" class="ml-3 block text-sm font-medium text-gray-700">
                Cost of Price
            </label>
        </div>



    </div>
</fieldset>
