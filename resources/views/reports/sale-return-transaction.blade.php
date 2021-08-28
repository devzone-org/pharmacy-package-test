@extends('pharmacy::layouts.master')

@section('title') Pharmacy Sales Return Transaction Report @endsection

@section('content')
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('report.sales-return-transaction')
    </div>
@endsection
