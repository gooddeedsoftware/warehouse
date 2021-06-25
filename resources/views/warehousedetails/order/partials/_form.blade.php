
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
                @else
                    <b>{!! trans('main.create_order') !!} </b>
                @endif
            </div>
            <div class="card-body">
                    @if (@$warehouseorder->id)
                        {!! Form::open(array('route' => array('main.warehouseAdjustmentOrder.update',$warehouseorder->id),'method'=>'PUT','id'=>'warehouseorderform','name'=>'warehouseorderform','class' => 'form-horizontal  row-border')) !!}
                        {!! Form::hidden('order_id',@$warehouseorder->id,array('id'=>'rder_id_value')) !!}
                    @else
                        {!! Form::open(array('route' => 'main.warehouseAdjustmentOrder.store','id'=>'warehouseorderform','name'=>'warehouseorderform','class'=>'form-horizontal')) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                             {!! Form::hidden('order_type', 2,array('id'=>'order_type')) !!}

                            <div class="form-group row">
                                {!! Form::label('order_date', trans('main.order_date'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                    {!! Form::text('order_date',@$warehouseorder->order_date ? $warehouseorder->order_date : date('d.m.Y'),array('class'=>'form-control','id'=>'warehouse_order_date' )) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('priority', trans('main.priority'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                    {!! Form::select('priority',@$priorities,@$warehouseorder->priority ? $warehouseorder->priority : '02',array('class'=>'form-control','id'=>'priority')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('warehouse', trans('main.warehouse'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                      {!! Form::select('warehouse', @$warehouses, @$warehouseorder->warehouse,array('class'=>'form-control','id'=>'warehouse' ,'placeholder' => trans('main.selected'))) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('order_status', trans('main.order_status'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                    {!! Form::select('order_status', @$status,@$warehouseorder->order_status,array('class'=>'form-control','id'=>'order_status')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('order_comment', trans('main.comments'), array('class' => 'col-md-2 col-form-label text-md-right')) !!}
                                <div class="col-md-9">
                                    {!! Form::textarea('order_comment', @$warehouseorder->order_comment, array('class' => 'form-control','rows' => 3)) !!}
                                </div>
                            </div>
                        </div>
                        <!-- Product details table -->
                        <div class="col-md-12  table-responsive">
                            <a href='#' class="btn btn-primary form-group" id="cloneProductTableBtn" style="display: none;" onclick="createNewProductTableRow();">{!! trans('main.addnew') !!}</a>
                            <table class="table table-responsive" id="warehouse_product_order_table">
                                <thead>
                                    <tr>
                                        <th width="30%">{!! trans('main.product') !!}</th>

                                        <th width="15%">{!! trans('main.location') !!}</th>

                                        <th width="13%">{!! trans('main.qty') !!}<label id="order_qty_symbol" style="display: none;">(+/-)</label></th>

                                        <th width="18%">{!! trans('main.comment') !!}</th>

                                        <th width="10%" class='rec_qty_td rec_qty_element hide-div'>{!! trans('main.rec_qty') !!}</th>

                                        <th width="10%" class='rec_date hide-div'>{!! trans('main.rec_date') !!}</th>
                                        <th width="4%"></th>
                                    </tr>
                                </thead>
                                <tbody id="warehouse_product_order_table_body">
                                    @if (@$warehouseorder_product_details) 
                                        @php $i = 0; @endphp
                                        @foreach($warehouseorder_product_details as $key => $value)
                                            <tr class="product_tr" data-id="1">
                                                <td>
                                                    {!! Form::select('order_product', [@$value->product_id => $value->product_text], @$value->product_id ,array('class'=>'form-control order_product', "onchange" => 'productChange(this);', 'disabled')) !!}

                                                </td>
                                                <td>
                                                    {!! Form::select('product_location', @$destination_locations, @$value->location_id ,array('class'=>'form-control select2 product_location')) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('qty', number_format(@$value->qty, 2, ',', ''),array('class'=>'form-control qty', 'onkeyup' => 'checkLocationIsSeleted(this);' )) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('comment', @$value->comment, array('class'=>'form-control comment')) !!}
                                                </td>

                                                <td class="rec_qty_element hide-div">
                                                    {!! Form::text('supplier_receive_input', number_format(@$value->qty, 2, ',', ''),array('class'=>'form-control supplier_receive_input')) !!}
                                                </td>
                                                <td class='rec_date hide-div'>
                                                    <input type="text" class="form-control" value="{{ formatDateFromDatabase(@$warehouseorder->updated_at) }}" />
                                                </td>
                                                <td>
                                                    <i class="delete-icon fa fa-trash deleteRowBtn" onclick="deleteProductRow(this,'');"></i>
                                                </td>
                                            </tr>
                                            @php $i++; @endphp
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- Product table end -->

                      
                    </div>
                    {!! Form::hidden('hidden_warehouse_table_row_count', '', array('class'=>'form-control','id'=>'hidden_warehouse_table_row_count' )) !!}
                    {!! Form::hidden('submit_button_value', '', array('class'=>'form-control','id'=>'submit_button_value' )) !!}
                    {!! Form::textarea('product_details', @$warehouseorder['product_details'], array('class'=>'form-control hide_div','id'=>'product_details' )) !!}
                    {!! Form::textarea('products_array', @$products, array('class'=>'form-control hide_div','id'=>'products_array' )) !!}
                    {!! Form::textarea('locations_array', @$locations, array('class'=>'form-control hide_div','id'=>'locations_array' )) !!}
                    {!! Form::textarea('destination_locations_array', @$destination_locations, array('class'=>'form-control hide_div','id'=>'destination_locations_array' )) !!}
                    {!! Form::textarea('warehouses_array', @$warehouses, array('class'=>'form-control hide_div','id'=>'warehouses_array' )) !!}
                    {!! Form::textarea('all_locations_array', @$all_locations, array('class'=>'form-control hide_div','id'=>'all_locations_array' )) !!}
                    {!! Form::hidden('supplier_warhouse_id', @$warehouseorder['destination_warehouse'], array('class'=>'form-control','id'=>'supplier_warhouse_id' )) !!}
                {!! form::close() !!}
                <div class="col-l text-sm-center">
                    <button type="button" class="btn btn-primary warehouseorder_submit_btn" id="warehouseorder_submit_btn" value="0">{!! $btn !!}</button>
                    @if (@$warehouseorder['id'])
                        <button type="button" class="btn btn-primary warehouseorder_submit_btn" id="warehouseorder_submit_btn" value="1">{!! $btn. ' & '. trans('main.close') !!}</button>
                    @endif
                    <a type="button" class="btn btn-danger" href="{!! route('main.warehouseorder.index') !!}">{!!trans('main.cancel') !!}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
<script type="text/javascript">
    var url = "{!! URL::to('/') !!}";
    var token = "{!! csrf_token() !!}";
    var confirm_delete = "{!! trans('main.deletefile') !!}";
    var whs_order_id = "{!! @$warehouseorder['id'] !!}";
    var product_location_validation = "{!! trans('main.fill_production_location') !!}";
    var checkproduct_exist_url = "{!! route('main.warehouseDetailsController.getLocations') !!}";
    var arichive_confimation_message = "{!! trans('main.arichive_confimation_message') !!}";
    var please_select_atleast_one_product = "{!! trans('main.please_select_atleast_one_product') !!}";
    var destination_warehosue_is_required  = "{!! trans('main.destination_warehosue_is_required') !!}";
    var source_warehosue_is_required = "{!! trans('main.source_warehosue_is_required') !!}";
    var add_product_package = "{!!trans('main.add_package') !!}";
    var not_allowed_to_change_status = "{!!trans('jslang.not_allowed_to_change_status') !!}";
    var something_went_wrong = "{!!trans('main.something_went_wrong') !!}";
    var select_location = "{!! trans('jslang.select_location') !!}";
    var product_not_avaliable = "{!!trans('jslang.product_not_avaliable') !!}";
    var stock_not_availabl = "{!!trans('jslang.stock_not_available') !!}";
    var order_qty_not_less_rec_qty = "{!!trans('jslang.order_qty_not_less_rec_qty') !!}";
    var rect_qty_not_greater_order_qty = "{!!trans('jslang.rect_qty_not_greater_order_qty') !!}";
    var package_qty_same = "{!!trans('jslang.package_qty_same') !!}";
    $(document).ready(function () {
        $("#warehouse_order_date").datetimepicker({
            format: 'DD.MM.YYYY',
            locale: "en-gb"
        });
        var id = "{!! @$warehouseorder['id'] !!}";
        if (id) {
            setFields();
        }
    });
</script>
{!! Html::script('js/adjustment.warehouse.v2.js') !!}
@endsection
