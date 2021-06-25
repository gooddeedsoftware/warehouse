@extends('layouts.layouts')
@section('title',trans('main.ccsheet'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('main.ccsheet') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('main.ccsheet') !!}</p>
@stop

@section('content')
    {!! Form::open(array('route' => array('main.ccsheet.update',$ccsheet->id),'method'=>'PUT','class' => 'form-horizontal  row-border', 'id' => 'ccsheet_form')) !!}
    	@include('ccsheet/partials/_form', ['submit_text' => trans('main.editupdate').' '.trans('main.ccsheet'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
