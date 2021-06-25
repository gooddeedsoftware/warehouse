@extends('layouts.layouts')
@section('title',__('main.location'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!__('main.location') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!__('main.location') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop

@section('content')
    {!! Form::open( array('route' => 'main.location.store','class'=>'form-horizontal','id'=>"locationForm") ) !!}
		@include('location/partials/_form', ['submit_text' => trans('main.create').' '.__('main.location'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
