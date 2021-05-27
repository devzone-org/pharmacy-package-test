@extends('pharmacy::layouts.master')

@section('title') Purchase Order Details @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('purchases.purchase-view',['purchase_id'=>$id])
    </div>
@endsection
