@extends('layouts.layouts')
@section('title',trans('main.warehouse'))
@section('header')
<h3><i class="icon-message"></i>{!!trans('main.warehouse') !!}</h3>
@stop
@section('help')
<p class="lead">{!!trans('main.warehouse') !!}</p>
@stop

@section('content')
<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!! trans('main.order') !!} {!! @$warehouseorder->order_number !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('order_type', trans('main.order_type'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::select('order_type', @$order_type, @$warehouseorder['order_type'],array('class'=>'form-control', 'disabled' => 'disabled')) !!}
                        </div>
                    </div>
                     <div class="form-group row">
                        {!! Form::label('order_number', trans('main.customer_order_no'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::text('order_number', @$warehouseorder['customer_order_number'],array('class'=>'form-control', 'disabled' => 'disabled')) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('order_date', trans('main.order_date'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::text('order_date',@$warehouseorder['order_date'],array('class'=>'form-control warehosue_header_fields', 'disabled' => 'disabled')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('order_status', trans('main.order_status'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::select('order_status', @$status,@$warehouseorder['order_status'],array('class'=>'form-control', 'disabled' => 'disabled')) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="warehouse_product_order_table">
                    <thead>
                        <tr>
                            <th width="35%"><a>{!! trans('main.product') !!}</a></th>
                            <th width="15"><a>{!! trans('main.qty') !!}</a></th>
                            <th width="20%"><a>{!! trans('main.warehouse') !!}</a></th>
                            <th width="20%"><a>{!! trans('main.location') !!}</a></th>
                        </tr>
                    </thead>
                    <tbody id="warehouse_product_order_table_body">
                        @if(@$product_details)
                            @foreach($product_details as $product)
                                <tr>
                                    <td>
                                        {!! @$product_drop_downs[$product->product_id] !!}
                                    </td>
                                    <td>
                                        {!! number_format(@$product->return_qty,"0", ",", "") !!}
                                    </td>
                                    <td>
                                        {!! @$warehouses[$product->warehouse] !!}
                                    </td>
                                    <td>
                                        {!! @$all_locations[$product->location] !!}
                                    </td>
                                <tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <a type="button" class="btn btn-danger" href="{!! route('main.warehouseorder.index') !!}">{!!trans('main.back') !!}</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
    {!! Html::script('js/transfer.order.warehouse.js') !!}
@endsection
