@extends('layouts.layouts')
@section('title',__('main.printer_detail'))
@section('content')
    {!! Form::open(array('route' => array('main.printer_detail.update',$printer_detail->id),'method'=>'PUT','class' => 'form-horizontal printer_detailform','id'=>"printer_detailform")) !!}
    @include('printer_detail/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.printer_detail'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
