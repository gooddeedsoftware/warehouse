@extends('layouts.layouts')
@section('title', __('main.productGroup'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!! __('main.productGroup') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!! __('main.productGroup') !!}</p>
    <p>{!! __('main.help') !!}</p>
@stop

@section('content')
    {!! Form::open(array('route' => array('main.productGroup.update',$productGroup->id),'method'=>'PUT','class' => 'form-horizontal  row-border' , 'id' => 'productGroupForm')) !!}
    	@include('productGroup/partials/_form', ['submit_text' => trans('main.editupdate').' '. __('main.productGroup'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
