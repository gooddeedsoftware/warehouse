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
    {!! Form::open(array('route' => array('main.company.update',$company->id),'method'=>'PUT','class' => 'form-horizontal  row-border','id'=>"companyForm",'files' => true)) !!}
    	@include('company/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.company'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
