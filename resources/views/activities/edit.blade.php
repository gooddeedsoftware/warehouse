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
    {!! Form::open(array('route' => array('main.activities.update',$activities->id),'method'=>'PUT','class' => 'form-horizontal row-border activityform','id'=>"activityform")) !!}
    	@include('activities/partials/_form', ['submit_text' => trans('main.editupdate').' '.  __('main.activity'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
