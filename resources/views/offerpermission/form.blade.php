@extends('layouts.layouts')
@section('title',trans('permission_group.permissiontitle'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('permission_group.permissiontitle') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('permission_group.permissiontitle') !!}</p>
    <p>{!!trans('permission_group.permissionhelp') !!}</p>
@stop
@section('content')
    {!! Form::open( array('route' => 'main.offerpermission.store','class'=>'form-horizontal','id'=>"permission_group_form") ) !!}
 		@include('offerpermission/partials/_form', ['submit_text' => trans('main.create').' '.trans('permission_group.permissionstitle'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
