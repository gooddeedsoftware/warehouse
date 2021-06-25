@extends('layouts.layouts')
@section('title',__('main.customer'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!__('main.customer') !!}
    </h3>
@stop
@section('help')
    <p class="lead">{!!__('main.customer') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop

@section('content')
{!! Html::style('css/signature-pad.css') !!}
    {!! Form::open( array('route' => 'main.customer.store','class'=>'form-horizontal customer_form','id'=>"customerForm")) !!}
    	@include('customer/partials/_form', ['submit_text' => trans('main.create').' '.__('main.customer'),'btn' => trans('main.create'), 'create_or_update_close' => __('main.create_and_close')])
    {!! Form::close() !!}
@stop
