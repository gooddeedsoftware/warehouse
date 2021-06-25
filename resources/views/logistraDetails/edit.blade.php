@extends('layouts.layouts')
@section('title',__('main.logistraDetails'))
@section('content')
    {!! Form::open(array('route' => array('main.logistraDetails.update',$logistraDetails->id),'method'=>'PUT','class' => 'form-horizontal logistraDetailsform','id'=>"logistraDetailsform")) !!}
    @include('logistraDetails/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.logistraDetails'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
