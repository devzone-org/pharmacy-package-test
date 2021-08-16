@extends('pharmacy::layouts.master')

@section('title') Dashboard @endsection

@section('content')

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        @livewire('dashboard.date')
        @livewire('dashboard.monthwise-sales-summary')
        @livewire('dashboard.customised-sales-summary')
        @livewire('dashboard.customised-sales-summary-userwise')
        @livewire('dashboard.customised-sales-summary-doctorwise')
        @livewire('dashboard.top-selling-products',['report_type' => 'revenue'])
        @livewire('dashboard.top-selling-products',['report_type' => 'profit'])
        @livewire('dashboard.expired-products')



        <!-- Required chart.js -->

    </div>


@endsection

