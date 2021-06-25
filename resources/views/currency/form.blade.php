
@extends('layouts.layouts')
@section('title',__('main.currency'))
@section('header')
	<h3>
		<i class="icon-message"></i>{!!__('main.currency') !!}
	</h3>
@stop

@section('help')
	<p class="lead">{!!__('main.currency') !!}</p>
	<p>{!!__('main.help') !!}</p>
@stop

@section('content')
	{!! Form::open( array('route' => 'main.currency.store','class'=>'form-horizontal currencyform', 'id'=>"currencyform") ) !!}
	@include('currency/partials/_form', ['submit_text' => trans('main.create').' '.__('main.currency'),'btn'=>trans('main.create')])
	{!! Form::close() !!}
@stop
