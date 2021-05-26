@extends('pharmacy::layouts.master')

@section('title') Purchases @endsection

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @include('pharmacy::purchases.links')
    </div>
@endsection
