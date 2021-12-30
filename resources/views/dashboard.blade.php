@extends('pharmacy::layouts.master')

@section('title') Dashboard @endsection

@section('content')

    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">

        {{--        @livewire('dashboard.date')--}}
        <div class="grid grid-cols-6 gap-3">
            @can('12.last-3-month-stats')
                <div class="col-span-6">
                    @livewire('dashboard.monthwise-sales-summary')
                </div>
            @endcan
            @can('12.sales-summary-stats')
                <div class="col-span-3">
                    @livewire('dashboard.customised-sales-summary')
                </div>
            @endcan
            @can('12.sales-summary-saleman-stats')
                <div class="col-span-3">
                    @livewire('dashboard.customised-sales-summary-userwise')
                </div>
            @endcan
            @can('12.sales-summary-dr-wise-stats')
                <div class="col-span-3">
                    @livewire('dashboard.customised-sales-summary-doctorwise')
                </div>
            @endcan
            @can('12.top-supplier-payable-stats')
                <div class="col-span-3">
                    @livewire('dashboard.top-supplier-payables')
                </div>
            @endcan
            @can('12.top5-sell-product-income')
                <div class="col-span-3">
                    @livewire('dashboard.top-selling-products',['report_type' => 'revenue'])
                </div>
            @endcan
            @can('12.top5-sell-product-profit')
                <div class="col-span-3">
                    @livewire('dashboard.top-selling-products',['report_type' => 'profit'])
                </div>
            @endcan
            @can('12.expired-products')
                <div class="col-span-6">
                    @livewire('dashboard.expired-products')
                </div>
            @endcan
                @can('12.hourly-sale-stats')
            <div class="col-span-6">
                @livewire('dashboard.hourly-trends')

            </div>
                    @endcan

        </div>


        <!-- Required chart.js -->

    </div>


@endsection

