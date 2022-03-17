@extends('pharmacy::layouts.master')

@section('title') Edit Purchase Order @endsection

@section('content')
    <div class="max-w-screen mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('purchases.purchase-edit',['purchase_id'=>$id])
    </div>
@endsection
