@extends('layouts.layouts')
@section('title',__('main.warehouse'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!__('main.warehouse') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!__('main.warehouse') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop

@section('content')
{!! Html::style('css/signature-pad.css') !!}
    {!! Form::open( array('route' => 'main.warehouse.store','class'=>'form-horizontal', 'id'=>"warehouseForm",) ) !!}
	@include('warehouse/partials/_form', ['submit_text' => trans('main.create').' '.__('main.warehouse'),'btn'=>trans('main.create')])

    {!! Form::close() !!}
@stop
