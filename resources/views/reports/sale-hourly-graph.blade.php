@extends('pharmacy::layouts.master')

@section('title') Pharmacy Sales Hourly Graph Report @endsection

@section('content')
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('report.sale-hourly-graph')
    </div>
@endsection
