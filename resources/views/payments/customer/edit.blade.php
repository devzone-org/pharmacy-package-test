@extends('pharmacy::layouts.master')

@section('title') Edit Customers Payment @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('payments.customer.edit',['id'=>$id])
    </div>
@endsection
