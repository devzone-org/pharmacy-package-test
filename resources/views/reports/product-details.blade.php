@extends('pharmacy::layouts.master')

@section('title') Product Details Report @endsection

@section('content')
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('report.product-details')
    </div>
@endsection
