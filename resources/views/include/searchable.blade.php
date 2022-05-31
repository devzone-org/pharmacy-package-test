<div x-data="{ open: @entangle('searchable_modal') }" x-cloak x-show="open"
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
        <div @click.away="open = false;" @keydown.escape="open = false;" x-show="open" x-description="Modal panel, show/hide based on modal state."
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg  text-left overflow-hidden shadow-xl transform transition-all
              sm:my-18 sm:align-middle w-full sm:max-w-4xl "
             role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div class="   px-2 pt-2 pb-2">
                <div class="">
                    <label for="status"
                           class="block text-sm font-medium text-gray-600">Search {{ ucwords(str_replace('_', ' ', $searchable_type))  }}</label>
                    @if($searchable_type == 'item')
                        <input type="text"
                               wire:model.debounce.500ms="searchable_query"
                               id="searchable_query"
                               class="shadow-sm mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               autocomplete="off">
                    @else
                        <input type="text"
                               wire:model.debounce.500ms="searchable_query"
                               wire:keydown.arrow-up="decrementHighlight"
                               wire:keydown.arrow-down="incrementHighlight"
                               wire:keydown.enter="searchableSelection"
                               wire:keydown.escape="searchableReset"
                               wire:keydown.tab="searchableReset"
                               id="searchable_query"
                               class="shadow-sm mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               autocomplete="off">
                    @endif
                </div>
            </div>

            <div class="h-96  overflow-scroll">
                @if($searchable_loading==false)
                    @if(!empty($searchable_data))
                        @if($searchable_type == 'item')
                            <table class="mt-3 min-w-full divide-y divide-gray-200" x-data="{h_light: 0}">
                                <thead class="bg-gray-50">
                                <tr>
                                    @foreach($searchable_column[$searchable_type] as $c)
                                        <th scope="col"
                                            class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                            {{ ucwords($c) }}
                                        </th>
                                    @endforeach
                                    <th scope="col"
                                        class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                        Order Qty
                                    </th>


                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($searchable_data as $key=> $a)
                                    <tr class="hover:bg-indigo-600 hover:text-white" :class="{'bg-indigo-600 text-white' : h_light=={{$key}}, 'text-gray-500' : h_light!={{$key}}}"
                                        >
                                        @foreach($searchable_column[$searchable_type] as $key1 => $c)
                                            @php
                                                $classes = '';
        if ($key1 == 0){
            $classes = 40;
         }elseif ($key1 == 1){
            $classes = 5;
         }elseif ($key1 == 2){
            $classes = 5;
         }elseif ($key1 == 3){
            $classes = 20;
         }elseif ($key1 == 4){
            $classes = 5;
         }elseif ($key1 == 5){
            $classes = 5;
         }elseif ($key1 == 6){
            $classes = 5;
         }
                                            @endphp
                                            <td class="px-2 whitespace-nowrap text-sm" style="width: {{$classes}}%; white-space: initial">

                                                    @if($c=='retail_price')
                                                        {{ empty($a[$c]) ? $a['product_price'] : $a[$c] }}

                                                    @else
                                                        {{ $a[$c] }}

                                                    @if($c == 'item' && !empty($a['salt']))

                                                    <br>
                                                    <button type="button" class="text-left text-xs text-blue-500 hover:text-white focus:ring-indigo-500 focus:border-indigo-500" wire:click="itemSalt('{{$a['salt']}}')" >
                                                            ({{$a['salt']}})
                                                    </button>
                                                    @endif

                                                @endif

                                            </td>
                                        @endforeach
                                        <td class="px-2 py-1 whitespace-nowrap text-sm " style="width: 15%">
                                            <input type="number" min="0" @keyup.tab="h_light={{$key}}"  wire:model.defer="product_qty" wire:keydown.enter="searchableSelection('{{$key}}')"
                                                   class="px-2 py-1 relative text-black focus:ring-gray-200 focus:border-indigo-500 w-full text-sm border-gray-300 rounded"
                                                   autocomplete="off">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="mt-3 min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    @foreach($searchable_column[$searchable_type] as $c)
                                        <th scope="col"
                                            class="px-2 py-2 text-left text-xs font-medium text-gray-500   tracking-wider">
                                            {{ ucwords($c) }}
                                        </th>
                                    @endforeach


                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($searchable_data as $key=> $a)
                                    <tr class="hover:bg-indigo-600 hover:text-white cursor-point er  {{ $highlight_index === $key ? 'bg-indigo-600 text-white' : ' text-gray-500' }}"
                                        wire:click="searchableSelection('{{ $key }}')">
                                        @foreach($searchable_column[$searchable_type] as $c)
                                            <td class="px-2 py-2 whitespace-nowrap text-sm @if($searchable_type == 'product')@if($a['control_medicine'] == 't') text-red-500 @endif @endif">
                                                @if($searchable_type=='item')
                                                    @if($c=='retail_price')
                                                        {{ empty($a[$c]) ? $a['product_price'] : $a[$c] }}
                                                    @else
                                                        {{ $a[$c] }}
                                                    @endif
                                                @else
                                                    {{ $a[$c] }}
                                                @endif

                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    @else
                        @if(strlen($searchable_query) < 2)
                            <p class="text-sm opacity-25 pt-0 p-3 ">Please enter {{ 2 - strlen($searchable_query) }}
                                or more
                                {{ (2 - strlen($searchable_query)) > 1 ? 'characters' : 'character' }}</p>
                        @else
                            <p class="text-sm opacity-25 pt-0 p-3">{{ empty($searchable_data) ? 'No Record Found': '' }}</p>
                        @endif
                    @endif
                @else
                    <p class="text-center text-sm text-gray-600 mb-3">Loading...</p>
                @endif
                @if(!isset($this->hide_add_patient_searchable))
                    @if($searchable_type=='patient')
                        <p class="text-indigo-600 pt-0 p-3 font-bold cursor-pointer inline-block float-right"
                           wire:click="openAddModel">+ Add New Patient</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
