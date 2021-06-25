
@extends('layouts.layouts')
@section('title',trans('main.supplier.stitle'))
@section('header')
	<h3>
		<i class="icon-message"></i>{!!trans('main.supplier.title') !!}
	</h3>
@stop

@section('help')
	<p class="lead">{!!trans('main.supplier.title') !!}</p>
	<p>{!!trans('main.supplier.help') !!}</p>
@stop

@section('content')
	{!! Form::open( array('route' => 'main.supplier.store','class'=>'form-horizontal') ) !!}
	@include('supplier/partials/_form', ['submit_text' => trans('main.create').' '.trans('main.supplier.stitle'),'btn'=>trans('main.create')])
	{!! Form::close() !!}
@stop
