@extends('pharmacy::layouts.master')

@section('title') View Sale @endsection

@section('content')
    @livewire('sales.transaction',['sale_id'=>$id])
@endsection
