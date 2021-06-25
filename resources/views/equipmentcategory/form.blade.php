
@extends('layouts.layouts')
@section('title', __('main.equipmentcategory'))
@section('header')
	<h3>
		<i class="icon-message"></i>{!! __('main.equipmentcategory') !!}
	</h3>
@stop

@section('help')
	<p class="lead">{!! __('main.equipmentcategory') !!}</p>
	<p>{!! __('main.help') !!}</p>
@stop

@section('content')
	{!! Form::open( array('route' => 'main.equipmentcategory.store','class'=>'form-horizontal', 'id' => 'equipmentcategoryForm') ) !!}
		@include('equipmentcategory/partials/_form', ['submit_text' => trans('main.create').' '. __('main.equipmentcategory'),'btn'=>trans('main.create')])
	{!! Form::close() !!}
@stop
