@extends('pharmacy::layouts.master')

@section('title') Product Add @endsection

@section('content')
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('master-data.products-add')
    </div>
@endsection
