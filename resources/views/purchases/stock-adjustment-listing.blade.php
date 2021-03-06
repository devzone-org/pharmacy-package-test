@extends('pharmacy::layouts.master')

@section('title') Stock Adjustment @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('purchase.stock-adjustment-listing')
    </div>
@endsection
