@extends('pharmacy::layouts.master')

@section('title') Admissions Pharmacy @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @livewire('sales.admission-pharmacy')
    </div>
@endsection
