
@extends('layouts.layouts')
@section('title',__('main.country'))
@section('header')
	<h3>
		<i class="icon-message"></i>{!!__('main.country') !!}
	</h3>
@stop

@section('help')
	<p class="lead">{!!__('main.country') !!}</p>
	<p>{!!__('main.help') !!}</p>
@stop

@section('content')
	{!! Form::open( array('route' => 'main.country.store','class'=>'form-horizontal', 'id' => 'countryForm') ) !!}
		@include('country/partials/_form', ['submit_text' => trans('main.create').' '.__('main.country'),'btn'=>trans('main.create')])
	{!! Form::close() !!}
@stop
