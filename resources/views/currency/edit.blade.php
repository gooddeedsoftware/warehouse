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
    {!! Form::open(array('route' => array('main.currency.update',$currency->id),'method'=>'PUT','class' => 'form-horizontal  row-border currencyform', 'id'=>"currencyform") ) !!}
    @include('currency/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.currency'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
