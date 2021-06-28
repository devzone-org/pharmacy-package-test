@if ($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
                <div class=" text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
        </div>
    </div>
@endif
