@extends('pharmacy::layouts.master')

@section('title') View Sale @endsection

@section('content')



    @livewire('sales.view',['sale_id'=>$id])


@endsection
