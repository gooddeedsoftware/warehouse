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
{!! Form::open(array('route' => array('main.equipment.update',$equipment->id),'method'=>'PUT','class' => 'form-horizontal row-border equipmentform', 'id' => 'equipmentform')) !!}
    @include('equipment/partials/_form', ['submit_text' => trans('main.editupdate').' '.trans('main.equipment'),'btn'=>trans('main.update')])
{!! Form::close() !!}
@stop
