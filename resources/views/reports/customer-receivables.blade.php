@extends('pharmacy::layouts.master')

@section('title') Customer Receiveables Report @endsection

@section('content')
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('report.customer-receivables')
    </div>
@endsection
