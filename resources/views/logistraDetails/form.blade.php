@extends('layouts.layouts')
@section('title',__('main.logistraDetailss'))
@section('content')
    {!! Form::open( array('route' => 'main.logistraDetails.store','class'=>'form-horizontal logistraDetailsform', 'id'=>"logistraDetailsform") ) !!}
    @include('logistraDetails/partials/_form', ['submit_text' => trans('main.create').' '.__('main.logistraDetails'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
