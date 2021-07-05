@extends('pharmacy::layouts.master')

@section('title') Add Sale @endsection

@section('content')

    @livewire('sales.add',['admission_id'=>request('admission_id'),'procedure_id'=>request('procedure_id')])

@endsection
