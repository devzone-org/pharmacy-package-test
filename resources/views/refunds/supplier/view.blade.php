@extends('pharmacy::layouts.master')

@section('title') View Supplier Refund  @endsection

@section('content')
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('refunds.supplier.view',['primary_id'=>$id])
    </div>
@endsection
