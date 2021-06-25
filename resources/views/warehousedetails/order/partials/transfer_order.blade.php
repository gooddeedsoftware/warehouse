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
                @if (@$warehouseorder->id)
                    <b>{!! trans('main.order') !!} {!! @$warehouseorder->order_number !!}</b>
                    {!! Form::hidden('order_id',@$warehouseorder->id,array('id'=>'trasnfer_order_id_value')) !!}
                @else
                    <b>{!! trans('main.create_transfer_order') !!} </b>
                @endif
            </div>
            <div class="card-body" >
                @if (@$warehouseorder->id)
                    {!! Form::open(array('route' => array('main.warehouseorder.update',$warehouseorder->id),'method'=>'PUT','id'=>'warehousetransferorderform','name'=>'warehousetransferorderform','class' => 'form-horizontal  row-border','data-toggle'=>"validator", 'files' => true)) !!}
                @else
                    {!! Form::open( array('route' => 'main.warehouseorder.store','id'=>'warehousetransferorderform','name'=>'warehousetransferorderform','class'=>'form-horizontal','data-toggle'=>"validator", 'files' => true) ) !!}
                @endif
                <div class="row">

                    {!! Form::hidden('order_type', 1 ,array('id'=>'order_type')) !!}

                    <div class="col-md-6">
                        <div class="form-group row">
                            {!! Form::label('order_date', trans('main.order_date'), array('class' => 'col-md-4 col-form-label text-md-right','style' => 'text-align:left')) !!}
                            <div class="col-md-6">
                                {!! Form::text('order_date',@$warehouseorder->order_date ? $warehouseorder->order_date : date('d.m.Y'),array('class'=>'form-control warehosue_header_fields','id'=>'warehouse_order_date' )) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            {!! Form::label('source_warehouse', trans('main.source_whs'), array('class' => 'col-md-4 col-form-label text-md-right','style' => 'text-align:left')) !!}
                            <div class="col-md-6">
                                {!! Form::select('source_warehouse', @$transfer_order_warehouses, @$warehouseorder['source_warehouse'],array('class'=>'form-control transfer_source_warehouse warehosue_header_fields','placeholder'=>trans('main.selected'),'id'=>'transfer_source_warehouse')) !!}  
                            </div>
                        </div>


                         <div class="form-group row">
                            {!! Form::label('order_status', trans('main.order_status'), array('class' => 'col-md-4 col-form-label text-md-right','style' => 'text-align:left')) !!}
                            <div class="col-md-6">
                                {!! Form::select('order_status', @$status,@$warehouseorder['order_status'],array('class'=>'form-control','id'=>'transfer_order_status')) !!}
                                {!! Form::hidden('order_status', @$warehouseorder['order_status'],array('id'=>'transfer_order_status_hidden_value')) !!}
                            </div>
                        </div>

                    </div>

                     <div class="col-md-6">
                        <div class="form-group row">
                            {!! Form::label('priority', trans('main.priority'), array('class' => 'col-md-4 col-form-label text-md-right','style' => 'text-align:left')) !!}
                            <div class="col-md-6">
                                {!! Form::select('priority',@$priorities, @$warehouseorder->priority ? $warehouseorder->priority : '02',array('class'=>'form-control warehosue_header_fields','id'=>'priority')) !!}
                            </div>
                        </div>

                         <div class="form-group row">
                            {!! Form::label('destination_warehouse', trans('main.dest_whs'), array('class' => 'col-md-4 col-form-label text-md-right','style' => 'text-align:left')) !!}
                            <div class="col-md-6">
                                 {!! Form::select('destination_warehouse', @$warehouses,@$warehouseorder['destination_warehouse'],array('class'=>'form-control warehosue_header_fields','id'=>'destination_warehouse', 'placeholder' => trans('main.selected') )) !!}
                            </div>
                        </div>


                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            {!! Form::label('order_comment', trans('main.comments'), array('class' => 'col-md-2 col-form-label text-md-right')) !!}
                            <div class="col-md-9">
                                {!! Form::textarea('order_comment',@$warehouseorder->order_comment, array('class' => 'form-control','rows' => 3)) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Product details table -->
                <div class="table-responsive">
                        <a class="btn btn-primary form-group" id="transfer_cloneProductTableBtn" onclick="createNewTransferProductTableRow();" href="#">{!! trans('main.addnew') !!}</a>
                        <table class="table " id="warehouse_product_order_table" style ="width: 100% !important ">
                            <thead>
                                <tr>
                                    <th width="25%">{!! trans('main.product') !!}</th>
                                    <th width="5%">{!! trans('main.qty') !!}</th>
                                    <th width="18%" class='product_comment_td'>{!! trans('main.comment') !!}</th>
                                    <th width="12%" id="location_th" style="display: none;">{!! trans('main.location') !!}</th>
                                    <th width="5%" class='picked_qty_td' style="display: none;">{!! trans('main.picked_qty') !!}</th>
                                    <th width="5%" class='rec_qty_td' style="display: none;">{!! trans('main.rec_qty') !!}</th>
                                    <th width="11%" class='rec_location_td' style="display: none;">{!! trans('main.location') !!}</th>
                                    <th width="9%" class='rec_date_td' style="display: none;">{!! trans('main.rec_date') !!}</th>
                                </tr>
                            </thead>
                            <tbody id="warehouse_product_order_table_body">
                            </tbody>
                        </table>
                </div>    
                <!-- Product table end -->

                <div class="col-l text-sm-center">
                    <a role="button" id="receive_order_btn" class="btn btn-primary receive_order_btn form-group" href="javascript:;">{!! trans('main.receive') !!}</a>
                </div>
                {!! Form::hidden('submit_button_value', '', array('class'=>'form-control','id'=>'submit_button_value' )) !!}
                {!! Form::hidden('hidden_transfer_table_row_count', '', array('class'=>'form-control','id'=>'hidden_transfer_table_row_count' )) !!}
                {!! Form::textarea('product_details', @$warehouseorder['product_details'], array('class'=>'form-control hide_div','id'=>'product_details' )) !!}
                {!! Form::textarea('products_array', @$products, array('class'=>'form-control hide_div','id'=>'products_array' )) !!}
                {!! Form::textarea('locations_array', @$locations, array('class'=>'form-control hide_div','id'=>'locations_array' )) !!}
                {!! Form::textarea('destination_locations_array', @$destination_locations, array('class'=>'form-control hide_div','id'=>'destination_locations_array' )) !!}
                 <div class="col-l text-sm-center">
                    @if (@$warehouseorder->id)
                        <button value="update" type="button" class="btn btn-primary transferorder_submit_btn" value="0">{!! @$btn !!}</button>
                        <input type="hidden" name="update" id="update" value="0" type="button" />
                        <button name="save_and_close" type="button" class="btn btn-primary transferorder_submit_btn" value="1">{!! trans("main.update_and_close") !!}</button>
                    @else
                        <button name="update" type="button" class="btn btn-primary transferorder_submit_btn">{!! @$btn !!}</button>
                    @endif
                        <button type="button" class="btn btn-danger" onclick="redirectToWarehouseDetail('{!! route('main.warehouseorder.index') !!}');"  >{!!trans('main.cancel') !!}</button>
                </div>
                {!! form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    .datetimepicker {
        z-index: 100000;
    }
    .bootstrap-timepicker-widget {
        z-index: 100000 !important;
        color:#000 !important;
    }
           
    .datepicker {
        z-index:1051;
    }
</style>
@section('page_js')
<script type="text/javascript">
    var url = "{!! URL::to('/') !!}";
    var token = "{!! csrf_token() !!}";
    var confirm_delete = "{!! trans('main.deletefile') !!}";
    var product_location_validation = "{!! trans('main.fill_production_location') !!}";
    var order_quantity_less_validation = "{!! trans('main.order_quantity_greater_than_picked_qty ') !!}";
    var notallowed_to_change_the_status = "{!! trans('main.notallowed_to_change_the_status') !!}";
    var order_quantity_should_not_less_than_received_quantity = "{!! trans('main.order_quantity_should_not_less_than_received_quantity') !!}";
    var please_select_location = "{!! trans('main.please_select_location') !!}";
    var picked_qty_not_greater_than_order_qty = "{!! trans('main.picked_qty_not_greater_than_order_qty') !!}";
    var picked_quantity_must_lesser_than_stock = "{!! trans('main.picked_quantity_must_lesser_than_stock') !!}";
    var please_select_atleast_one_product = "{!! trans('main.please_select_atleast_one_product') !!}";
    var serial_number_and_received_location_are_needed = "{!! trans('main.serial_number_and_received_location_are_needed') !!}";
    var please_select_serial_number = "{!! trans('main.please_select_serial_number') !!}";
    var arichive_confimation_message = "{!! trans('main.arichive_confimation_message') !!}";
    var destination_warehosue_is_required  = "{!! trans('main.warehouseorder.destination_warehosue_is_required') !!}";
    var source_warehosue_is_required = "{!! trans('main.warehouseorder.source_warehosue_is_required') !!}";
    var qty_alert = "{!! trans('main.qty_greater_than_0') !!}";
    
    $(document).ready(function () {
        // date picker
        $("#warehouse_order_date").datetimepicker({
            format: 'DD.MM.YYYY',
            locale: "en-gb"
        });
        var id = "{!! @$warehouseorder['id'] !!}";
        showHideTransferOrderFields("{!! @$warehouseorder['order_type'] !!}");
        if (id) {
            editTransferWarehouseOrder('{!! @$warehouseorder->order_type !!}','{!! @$warehouseorder->order_status !!}','');
        }

    });
</script>
{!! Html::script('js/transfer.order.warehouse.js') !!}
@endsection
