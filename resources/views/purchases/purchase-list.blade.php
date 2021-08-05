@extends('pharmacy::layouts.master')

@section('title') List of Purchase Orders @endsection

@section('content')
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('purchases.purchase-list')
    </div>
@endsection
