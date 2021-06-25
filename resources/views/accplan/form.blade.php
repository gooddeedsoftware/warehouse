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
    {!! Form::open( array('route' => 'main.accplan.store','class'=>'form-horizontal accplanform', 'id'=> "accplanform") ) !!}
		@include('accplan/partials/_form', ['submit_text' => trans('main.create').' '.__('main.accplan'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
