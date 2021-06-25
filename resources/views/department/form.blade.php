@extends('layouts.layouts')
@section('title',__('main.departments'))
@section('content')
    {!! Form::open( array('route' => 'main.department.store','class'=>'form-horizontal departmentform', 'id'=>"departmentform") ) !!}
    @include('department/partials/_form', ['submit_text' => trans('main.create').' '.__('main.department'),'btn'=>trans('main.create')])
    {!! Form::close() !!}
@stop
