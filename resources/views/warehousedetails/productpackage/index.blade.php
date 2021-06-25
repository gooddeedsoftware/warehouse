@extends('layouts.layouts')
@section('title',trans('main.productpackage'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.productpackage') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.productpackage') !!}</p>
<p>{!!trans('main.productpackage') !!}</p>
@stop

@section('content')
<div class="container warehouseContainer">
    <div class="card">
        <div class="card-header warehouseContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link " href="{!! route('main.warehousedetails.index') !!}">{!! trans('main.stock') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.warehouseorder.index') !!}">{!! trans('main.whs_orders') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/ccsheet') }}">{!! trans('main.ccsheet') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.product.index') !!}">{!! trans('main.products') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">{!! trans('main.productpackage') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{{ url('whs_history') }}">{!! trans('main.history') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            {!! Form::open(array('route' => array('main.productpackage.search', $query_string), 'id' => 'product_pacakage_search_form')) !!}
                <div class="row">
                    <div class="col-3 col-md-8">
                        <div class="form-group">
                            <a class="btn btn-primary" href="{!! route('main.productpackage.create') !!}" >
                                <i class="d-block d-sm-none fa fa-plus"></i>
                                <div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(trans('main.productpackage')) !!} </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-9 col-md-4 float-right">
                        <div class="form-group input-group">
                            {!! Form::text('search', @Session::get('productpackage_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(trans('main.productpackage')) )) !!}
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a>@sortablelink('product_number', trans('main.product_number'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('description', trans('main.description'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('sale_price', trans('main.sale_price'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('accPlan', trans('main.account_no'))</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(@$products)
                        @foreach (@$products as $product)
                            <tr>
                                <td>
                                    <a href="{!! route('main.productpackage.edit', array(@$product->id))!!}">{!! @$product->product_number !!}</a>
                                </td>
                                <td> {!! htmlspecialchars(@$product->description) !!} </td>
                                <td> {!! htmlspecialchars(@$product->sale_price) !!} </td>
                                <td> {!! htmlspecialchars(@$product->AccountNo) !!} - {!! htmlspecialchars(@$product->Name) !!} </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
             @include('common.pagination',array('paginator'=>@$products, 'formaction' => 'product_pacakage_search_form'))
        </div>
    </div>
</div>
@endsection