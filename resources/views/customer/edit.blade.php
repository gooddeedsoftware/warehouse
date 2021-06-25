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
    {!! Form::open(array('route' => array('main.customer.update',$customer->id),'method'=>'PUT','class' => 'form-horizontal  row-border customer_form','id'=>"customerForm")) !!}
        @include('customer/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.stitle'),'btn'=>trans('main.update'), 'create_or_update_close' => __('main.update_and_close')])
    {!! Form::close() !!}
@stop

<div class="hide_class">
	{!! Form::open(array('route' => array('main.order.index'), 'class' => 'form', 'method' => 'GET', 'id' => 'view_customer_order_form')) !!}
        	{{ Form::hidden('customer_id', '', array('id' => 'hidden_customer_id'))}}
    {!! Form::close() !!}
</div>

<div class="hide_class">
    {!! Form::open(array('route' => array('main.equipment.index'), 'class' => 'form', 'method' => 'GET', 'id' => 'view_customer_equipment_form')) !!}
            {{ Form::hidden('customer_id', '', array('id' => 'hidden_customer_id', 'class' => 'hidden_customer_id'))}}
    {!! Form::close() !!}
</div>
