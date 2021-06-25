@extends('layouts.layouts')
@section('title', __('main.users'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!! __('main.user') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!! __('main.user') !!}</p>
    <p>{!! __('main.help') !!}</p>
@stop

@section('content')
{!! Html::style('css/signature-pad.css') !!}
    {!! Form::open(array('route' => array('main.user.update',$users->id),'method'=>'PUT','class' => 'form-horizontal row-border userform','id'=>"userform",'files' => true)) !!}
    @include('user/partials/_form', ['submit_text' => trans('main.editupdate').' '. __('main.user'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
