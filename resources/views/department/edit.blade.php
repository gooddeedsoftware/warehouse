@extends('layouts.layouts')
@section('title',__('main.departments'))
@section('content')
    {!! Form::open(array('route' => array('main.department.update',$departments->id),'method'=>'PUT','class' => 'form-horizontal departmentform','id'=>"departmentform")) !!}
    @include('department/partials/_form', ['submit_text' => trans('main.editupdate').' '.__('main.department'),'btn'=>trans('main.update')])
    {!! Form::close() !!}
@stop
