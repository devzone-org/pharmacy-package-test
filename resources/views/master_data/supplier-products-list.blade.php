@extends('pharmacy::layouts.master')

@section('title') Supplier Products List @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('master-data.supplier-products-list')
    </div>
@endsection
