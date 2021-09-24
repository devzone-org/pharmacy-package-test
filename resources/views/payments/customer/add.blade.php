@extends('pharmacy::layouts.master')

@section('title') Add Customers Payment @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('payments.customer.add')
    </div>
@endsection
