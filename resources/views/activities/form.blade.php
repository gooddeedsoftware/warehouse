@extends('layouts.layouts')
@section('title',  __('main.activities'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!  __('main.activity') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!  __('main.activity') !!}</p>
    <p>{!!  __('main.help') !!}</p>
@stop

@section('content')
    {!! Form::open( array('route' => 'main.activities.store','class'=>'form-horizontal activityform','id' => "activityform") ) !!}
		@include('activities/partials/_form', ['submit_text' => trans('main.create').' '.  __('main.activity'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
