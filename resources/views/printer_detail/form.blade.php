@extends('layouts.layouts')
@section('title',__('main.printer_details'))
@section('content')
    {!! Form::open( array('route' => 'main.printer_detail.store','class'=>'form-horizontal printer_detailform', 'id'=>"printer_detailform") ) !!}
    @include('printer_detail/partials/_form', ['submit_text' => trans('main.create').' '.__('main.printer_detail'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
