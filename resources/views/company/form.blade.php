@extends('layouts.layouts')
@section('title',__('main.company'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!__('main.company') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!__('main.company') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop

@section('content')
    {!! Form::open( array('route' => 'main.company.store','class'=>'form-horizontal','id' => "companyForm",'files' => true) ) !!}
    	@include('company/partials/_form', ['submit_text' => trans('main.create').' '.__('main.company'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
