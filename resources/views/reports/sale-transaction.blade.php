@extends('pharmacy::layouts.master')

@section('title') Pharmacy Sales Transaction Report @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('report.sales-transaction')
    </div>
@endsection
