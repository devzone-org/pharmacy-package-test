<div>
    <div class="pb-5 border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <a href="{{ url('pharmacy/master-data') }}" class="p-3 bg-gray-200 border-2 rounded-md  border-gray-400 cursor-pointer hover:bg-gray-300 ">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path
                        fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path></svg>
            </a>
            <span class="ml-4">Master Data</span>
        </h3>
    </div>


    <div class=" shadow sm:rounded-md sm:overflow-hidden">
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                        <div class="bg-white py-6 px-4 space-y-6 sm:p-6 ">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Product List</h3>
                                <a href="{{ url('pharmacy/master-data/products/add') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add new Product
                                </a>
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
                                    Generic
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500   ">
                                   Pieces in Packing
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Barcode
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Supplier Cost
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Retail Price
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Manufacture
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Category
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Rack
                                </th>

                                <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500    ">
                                    Status
                                </th>
                                <th scope="col" class="relative px-3 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $m)
                                <tr>
                                    <td class="px-3 py-3   text-sm font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->name }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->salt }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->packing }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->barcode }}
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($m->cost_of_price,2) }}
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ number_format($m->retail_price,2) }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->m_name }}
                                    </td>

                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->c_name }}
                                    </td>
                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        {{ $m->r_name }} - {{ $m->tier }}
                                    </td>



                                    <td class="px-3 py-3   text-sm text-gray-500">
                                        @if($m->status == 't')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
  Active
</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
  Inactive
</span>
                                        @endif
                                    </td>


                                    <td class="px-3 py-3   text-right text-sm font-medium">
                                        <a href="{{ url('pharmacy/master-data/products/edit/') }}/{{$m->id}}"
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                        @if($products->hasPages())
                        <div class="bg-white p-3 border-t">
                            {{ $products->links() }}
                        </div>
                            @endif
                    </div>
                </div>
            </div>
        </div>


    </div>




</div>
