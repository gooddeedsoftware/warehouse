@extends('layouts.layouts')
@section('title',__('main.accplan'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!__('main.accplan') !!}
    </h3>
@stop
@section('help')
    <p class="lead">{!!__('main.accplan') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop

@section('content')
{!! Html::style('css/signature-pad.css') !!}
    {!! Form::open(array('route' => array('main.accplan.update',$accplan->id),'method'=>'PUT','class' => 'form-horizontal accplanform', 'id'=> "accplanform") ) !!}
    	@include('accplan/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.accplan'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
