@extends('pharmacy::layouts.master')

@section('title') Dashboard @endsection

@section('content')

    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">

        @livewire('dashboard.date')
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                @livewire('dashboard.monthwise-sales-summary')
            </div>
            <div class="col-span-3">

                @livewire('dashboard.customised-sales-summary')
            </div>
            <div class="col-span-3">
                @livewire('dashboard.customised-sales-summary-userwise')
{{--                @livewire('dashboard.customised-sales-returns')--}}
            </div>
            <div class="col-span-6">
                @livewire('dashboard.customised-sales-summary-doctorwise')
            </div>
            <div class="col-span-3">
                @livewire('dashboard.top-selling-products',['report_type' => 'revenue'])
            </div>
            <div class="col-span-3">
                @livewire('dashboard.top-selling-products',['report_type' => 'profit'])
            </div>
        </div>




    @livewire('dashboard.expired-products')



    <!-- Required chart.js -->

    </div>


@endsection

