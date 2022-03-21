@extends('pharmacy::layouts.master')

@section('title') Add/Edit Open Returns @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('sales.open-returns-add',['id'=>request('id')])
    </div>
@endsection
