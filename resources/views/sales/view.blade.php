@extends('pharmacy::layouts.master')

@section('title') View Sale @endsection

@section('content')



    @livewire('sales.view',['sale_id'=>$id,'admission_id'=>request('admission_id'),'procedure_id'=>request('procedure_id')])


@endsection
