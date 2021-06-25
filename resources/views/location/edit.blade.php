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
    {!! Form::open(array('route' => array('main.location.update',$location->id),'method'=>'PUT','class' => 'form-horizontal row-border','id'=> "locationForm")) !!}
    	@include('location/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.location'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
