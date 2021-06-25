
@extends('layouts.layouts')
@section('title',trans('main.ccsheet'))
@section('header')
	<h3>
		<i class="icon-message"></i>{!!trans('main.ccsheet') !!}
	</h3>
@stop

@section('help')
	<p class="lead">{!!trans('main.ccsheet') !!}</p>
	<p>{!!trans('main.ccsheet') !!}</p>
@stop

@section('content')
	{!! Form::open( array('route' => 'main.ccsheet.store','class'=>'form-horizontal', 'id' => 'ccsheet_form') ) !!}
		@include('ccsheet/partials/_form', ['submit_text' => trans('main.create').' '.trans('main.ccsheet'),'btn'=>trans('main.create')])
	{!! Form::close() !!}
@stop
