@extends('pharmacy::layouts.master')

@section('title') Add new Purchase Order @endsection

@section('content')
    <div class="max-w-screen mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('purchases.purchase-add')
    </div>
@endsection
