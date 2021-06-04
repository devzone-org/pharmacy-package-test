@extends('pharmacy::layouts.master')

@section('title') Supplier Payments @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @livewire('payments.supplier.list')
    </div>
@endsection
