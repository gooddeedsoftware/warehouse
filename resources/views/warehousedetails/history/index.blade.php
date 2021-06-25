@extends('layouts.layouts')
@section('title',trans('main.warehouse'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.warehouse') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.warehouse') !!}</p>
<p>{!!trans('main.warehouse') !!}</p>
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
                    <a class="nav-link"  href="{!! route('main.productpackage.index') !!}">{!! trans('main.productpackage') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active"  href="{{ url('whs_history') }}">{!! trans('main.history') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            {!! Form::open(array('route' => array('main.whs_history.search', $query_string), 'id' => 'whs_history_search_form')) !!}
            <div class="row">
                <div class="col-md-8">
                </div>
                <div class="col-md-4">
                    <div class="form-group input-group">
                        {!! Form::text('whs_history_search', @Session::get('warehouse_history_search')['whs_history_search'], array('id'=>'whs_history_search','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(trans('main.product')) )) !!}
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
             <div class="table-responsive">
                <table class="table table-striped table-hover" id="product_package_table">
                    <thead>
                        <tr>
                            <th>
                                <a>@sortablelink('productnumber', trans('main.product_number'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('description', trans('main.description'))</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(@$products)
                        @foreach (@$products as $product)
                            @if (@$product->product && @$product->product->deleted_at == null)
                                <tr data-toggle="collapse" data-target="#collapse_{!! @$product->id !!}"  style="cursor: pointer;">
                                    <td><a> {!! @$product->product->product_number !!} </a></td>
                                    <td><a> {!! htmlspecialchars(@$product->product->description) !!} </a></td>
                                </tr>
                                <tr id="collapse_{!! @$product->id !!}" class="collapse">
                                    <td colspan="2">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="product_package_table">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            {!! trans('main.date') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.ordered_by') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.order_type') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.order_number') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.source_warehouse') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.source_location') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.received_warehouse') !!}
                                                        </th>
                                                        <th>
                                                            {!! trans('main.received_location') !!}
                                                        </th>
                                                        @if ($product->sn != 1)
                                                            <th>
                                                                {!! trans('main.rec_qty') !!} 
                                                            </th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->history_details as $history_data)
                                                        <tr>
                                                            <td> {!! date('d.m.Y',strtotime(@$history_data->action_date)) !!} </td>
                                                            <td> {!! @$users[@$history_data->user] !!} </td>
                                                            <td>{!! @$order_types[@$history_data->order_type] !!}</td>
                                                            <td>
                                                                @if (@$history_data->order_type == 5) 
                                                                    <a href="{{route('main.order.edit', array($history_data->order_id)) }}"> {!! @$sale_orders[@$history_data->order_id] !!}</a>
                                                                @else 
                                                                    @if (@$history_data->order_type == 1)
                                                                        <a href="{!! route('main.warehouseorder.editTransferOrder', array(@$history_data->order_id))!!}" class="edit_warehouse_order">{!! @$whs_orders[@$history_data->order_id] !!}</a>
                                                                    @elseif(@$history_data->order_type == 4)
                                                                        <a href="{!! route('main.warehouseorder.editReturnOrder', array(@$history_data->order_id))!!}" class="editReturnOrder">{!! @$whs_orders[@$history_data->order_id] !!}</a>
                                                                    @elseif(@$history_data->order_type == 3)
                                                                        <a href="{!! route('main.warehouseorder.editSupplierOrder', array(@$history_data->order_id))!!}" class="edit_warehouse_order">{!! @$whs_orders[@$history_data->order_id] !!}</a>
                                                                    @elseif(@$history_data->order_type == 2)
                                                                        <a href="{!! route('main.warehouseorder.editAdjustmentOrder', array(@$history_data->id))!!}">{!! @$whs_orders[@$history_data->order_id] !!}</a>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>{!! @$history_data->from_warehouse ? @$warehouses[@$history_data->from_warehouse] : '' !!}</td>
                                                            <td>{!! @$history_data->from_location ? @$locations[@$history_data->from_location] : '' !!}</td>
                                                            <td>{!! @$history_data->destination_warehouse ? @$warehouses[@$history_data->destination_warehouse] : '' !!}</td>
                                                            <td>{!! @$history_data->destination_location ? @$locations[@$history_data->destination_location] : '' !!}</td>
                                                            @if ($product->sn != 1)
                                                                <td>{!! number_format(@$history_data->received_qty, '2', ',', ' ') !!}</td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            @if (@$products)
                @include('common.pagination',array('paginator'=>@$products, 'formaction' => 'whs_history_search_form'))
            @endif
        </div>
    </div>
</div>

@endsection
