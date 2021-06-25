@extends('layouts.layouts')
@section('title', __('main.grossMargin'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!! __('main.grossMargin') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!! __('main.grossMargin') !!}</p>
    <p>{!! __('main.help') !!}</p>
@stop

@section('content')
    {!! Form::open(array('route' => array('main.grossMargin.update',$grossMargin->id),'method'=>'PUT','class' => 'form-horizontal  row-border' , 'id' => 'grossMarginForm')) !!}
    	@include('grossMargin/partials/_form', ['submit_text' => trans('main.editupdate').' '. __('main.grossMargin'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
