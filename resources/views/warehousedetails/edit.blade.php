@extends('layouts.layouts')
@section('title',trans('main.supplier'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('main.supplier') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('main.supplier') !!}</p>
    <p>{!!trans('main.supplier.help') !!}</p>
@stop

@section('content')
    {!! Form::open(array('route' => array('main.supplier.update',$supplier->id),'method'=>'PUT','class' => 'form-horizontal  row-border')) !!}
    @include('supplier/partials/_form', ['submit_text' => trans('main.editupdate').' '.trans('main.supplier'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
