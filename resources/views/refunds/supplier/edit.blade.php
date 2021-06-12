@extends('pharmacy::layouts.master')

@section('title') Edit Supplier Return @endsection

@section('content')
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('refunds.supplier.edit',['primary_id'=>$id])
    </div>
@endsection
