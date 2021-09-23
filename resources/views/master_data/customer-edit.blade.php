@extends('pharmacy::layouts.master')

@section('title') Customer Edit @endsection

@section('content')
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('master-data.customer-edit',['id'=>$id])
    </div>
@endsection
