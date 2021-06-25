@extends('layouts.layouts')
@section('title',trans('main.productpackage'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('main.productpackage') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('main.productpackage') !!}</p>
    <p>{!!trans('main.productpackage.help') !!}</p>
@stop

@section('content')
    {!! Form::open(array('route' => array('main.productpackage.update',$product->id),'method'=>'PUT','class' => 'form-horizontal', 'id' => 'productPackage_form')) !!}
    	@include('warehousedetails/productpackage/partials/_form', ['submit_text' => trans('main.editupdate').' '.trans('main.productpackage'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
