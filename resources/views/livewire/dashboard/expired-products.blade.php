<div class="bg-white    shadow sm:rounded-md sm:overflow-hidden">
    <div class="p-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Expired Products</h2>
    </div>


    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                #
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Product
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Supplier
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Expiry Date
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Last Sold
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Quantity
            </th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $d)
            <tr class="@if($loop->even)  bg-gray-50 @endif hover:bg-gray-200">

                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                    {{$loop->iteration}}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                    {{$d['product']}}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{!empty($d['supplier']) ? $d['supplier'] : '-'}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{!empty($d['expiry']) ? date('d M Y',strtotime($d['expiry'])) : '-'}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{!empty($d['sale_at']) ? date('d M Y',strtotime($d['sale_at'])) : '-'}}
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                    {{number_format($d['qty'])}}
                </td>

            </tr>
        @endforeach

        </tbody>
    </table>


</div>
