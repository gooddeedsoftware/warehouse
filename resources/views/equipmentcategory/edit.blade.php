@extends('layouts.layouts')
@section('title', __('main.equipmentcategory'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!! __('main.equipmentcategory') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!! __('main.equipmentcategory') !!}</p>
    <p>{!! __('main.help') !!}</p>
@stop

@section('content')
    {!! Form::open(array('route' => array('main.equipmentcategory.update',$equipmentcategory->id),'method'=>'PUT','class' => 'form-horizontal  row-border' , 'id' => 'equipmentcategoryForm')) !!}
    	@include('equipmentcategory/partials/_form', ['submit_text' => trans('main.editupdate').' '. __('main.equipmentcategory'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
