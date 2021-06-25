@extends('layouts.layouts')
@section('title',trans('main.equipment'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('main.equipment') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('main.equipment') !!}</p>
    <p>{!!trans('main.equipment') !!}</p>
@stop

@section('content')
{!! Form::open( array('route' => 'main.equipment.store','class'=>'form-horizontal equipmentform', 'id' => 'equipmentform') ) !!}
	@include('equipment/partials/_form', ['submit_text' => trans('main.create').' '.trans('main.equipment'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
