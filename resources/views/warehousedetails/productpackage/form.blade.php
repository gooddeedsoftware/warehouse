	@extends('layouts.layouts')
@section('title',trans('main.productpackage'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('main.productpackage') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('main.productpackage') !!}</p>
@stop

@section('content')
    {!! Form::open( array('route' => 'main.productpackage.store','class'=>'form-horizontal','id' => 'productPackage_form') ) !!}
		@include('warehousedetails/productpackage/partials/_form', ['submit_text' => trans('main.create').' '.trans('main.productpackage'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
