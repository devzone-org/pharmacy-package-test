<div>
    <form wire:submit.prevent="create">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add Category</h3>

                </div>

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <!-- Heroicon name: x-circle -->
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    @php
                                        $count = count($errors->all());
                                    @endphp
                                    There {{ $count > 1 ? "were {$count} errors": "was {$count} error" }}
                                    with
                                    your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">

                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(!empty($success))
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <!-- Heroicon name: check-circle -->
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
                                    <button type="button" wire:click="$set('success', '')"
                                            class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                        <span class="sr-only">Dismiss</span>
                                        <!-- Heroicon name: x -->
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20"
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

                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input wire:model="name" type="text" autocomplete="off"
                               class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               id="name">
                    </div>


                    <div class="col-span-6 sm:col-span-2">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model="status" id="status"
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="t">Active</option>
                            <option value="f">Inactive</option>
                        </select>
                    </div>


                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit"
                        class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    Add
                </button>
            </div>
        </div>
    </form>


    <div class="mt-5 shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Categories</h3>
                            </div>


                        </div>


                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    #
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Name
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                    Status
                                </th>

                                <th scope="col" class="relative px-3 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $m)
                                <tr class>
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->name }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if($m->status == 't')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                              Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
  Inactive
</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3   text-right text-sm font-medium">
                                        <a href="#" wire:click="openEditModel('{{ $m->id }}')"
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <div x-data="{ open: @entangle('edit_modal') }" x-cloak x-show="open"
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
                 class="inline-block align-bottom bg-white rounded-lg  text-left
                 overflow-hidden shadow-xl transform transition-all
                 sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full  "
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class=" p-4">

                    <div class="mb-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Manufacture</h3>
                    </div>

                    @if (!empty($error))
                        <div class="rounded-md bg-red-50 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <!-- Heroicon name: x-circle -->
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-2">

                                    <div class=" text-sm text-red-700">

                                        {{ $error }}

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(!empty($success))
                        <div class="rounded-md bg-green-50 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <!-- Heroicon name: check-circle -->
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
                                        <button type="button" wire:click="$set('success', '')"
                                                class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                            <span class="sr-only">Dismiss</span>
                                            <!-- Heroicon name: x -->
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 20 20"
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
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input wire:model="ename" type="text" autocomplete="off"
                                   class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   id="name">
                        </div>



                        <div class="col-span-6 sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="estatus" id="status"
                                    class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="t">Active</option>
                                <option value="f">Inactive</option>
                            </select>
                        </div>


                    </div>

                    <div class="  py-3   text-right  ">
                        <button type="button" wire:click="updateManufacture"
                                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                            Update
                        </button>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
