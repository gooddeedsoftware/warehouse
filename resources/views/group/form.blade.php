@extends('layouts.layouts')
@section('title',trans('permission_group.group.title'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('permission_group.group.title') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('permission_group.group.title') !!}</p>
    <p>{!!trans('permission_group.group.help') !!}</p>
@stop

@section('content')
    {!! Form::open( array('route' => 'main.group.store','class'=>'form-horizontal','id'=>"groupForm") ) !!}
		@include('group/partials/_form', ['submit_text' => trans('main.create').' '.trans('permission_group.group.title'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
