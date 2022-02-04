@extends('pharmacy::layouts.master')

@section('title') Pharmacy Customer Receivables Report @endsection

@section('content')
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('report.customer-receivables')
    </div>
@endsection