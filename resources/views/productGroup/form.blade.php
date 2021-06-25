@extends('layouts.layouts')
@section('title', __('main.productGroup'))
@section('header')
	<h3>
		<i class="icon-message"></i>{!! __('main.productGroup') !!}
	</h3>
@stop
@section('help')
	<p class="lead">{!! __('main.productGroup') !!}</p>
	<p>{!! __('main.help') !!}</p>
@stop
@section('content')
	{!! Form::open( array('route' => 'main.productGroup.store','class'=>'form-horizontal', 'id' => 'productGroupForm') ) !!}
		@include('productGroup/partials/_form', ['submit_text' => trans('main.create').' '. __('main.productGroup'),'btn'=>trans('main.create')])
	{!! Form::close() !!}
@stop
