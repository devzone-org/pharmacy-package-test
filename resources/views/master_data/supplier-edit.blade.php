@extends('pharmacy::layouts.master')

@section('title') Supplier Edit @endsection

@section('content')
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('master-data.supplier-edit',['primary_id'=>$id])
    </div>
@endsection
