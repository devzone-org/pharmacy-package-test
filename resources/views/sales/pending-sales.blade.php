@extends('pharmacy::layouts.master')

@section('title') Pending Sales @endsection

@section('content')
    <div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
    @livewire('sales.pending')
    </div>
@endsection
