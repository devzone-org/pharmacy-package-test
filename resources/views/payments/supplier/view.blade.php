@extends('pharmacy::layouts.master')

@section('title') View Supplier Payments @endsection

@section('content')
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('payments.supplier.view',['payment_id'=>$id])
    </div>
@endsection
