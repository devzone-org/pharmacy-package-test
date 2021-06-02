@extends('pharmacy::layouts.master')

@section('title') Comparison Purchase Order Details @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('purchases.purchase-compare',['purchase_id'=>$id])
    </div>
@endsection
