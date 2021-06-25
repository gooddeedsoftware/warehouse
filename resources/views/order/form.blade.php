@extends('layouts.layouts')

@section('header')
    <h3><i class="icon-message"></i>{!! @$is_offer == 1 ?  __('main.offer') :  __('main.order') !!}</h3>
@stop

@section('help')
    <p class="lead">{!! @$is_offer == 1 ?  __('main.offer') :  __('main.order') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
            	<b>{{  trans('main.create').' '.strtolower( @$is_offer == 1 ?  __('main.offer') :  __('main.order') ) }}</b>
            </div>
            <div class="card-body">
            	{!! Html::style('css/signature-pad.css') !!}
                @if( @$is_offer == 1 )
                    {!! Form::open( array('route' => 'main.offer.store','id'=>'orderform','name'=>'orderform','class'=>'form-horizontal','data-toggle'=>"validator", 'files' => true, 'onsubmit' => 'order_submit_btn.disabled=true;return true;') ) !!}
                @else
                    {!! Form::open( array('route' => 'main.order.store','id'=>'orderform','name'=>'orderform','class'=>'form-horizontal','data-toggle'=>"validator", 'files' => true, 'onsubmit' => 'order_submit_btn.disabled=true;return true;') ) !!}
                @endif
    			
    		        @include('order/partials/_form', ['btn' => trans('main.create') , 'btn_create_and_close' => __('main.create_and_close')])
    		        {!! Form::hidden('form_secret', @$form_secret) !!}
    		    {!! Form::close() !!}
        	</div>
        </div>
    </div>
@stop