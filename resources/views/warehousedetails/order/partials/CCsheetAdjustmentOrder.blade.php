
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
                <b>{!! trans('main.create_order') !!} </b>
            </div>
            <div class="card-body">
                {!! Form::open(array('route' => 'main.warehouseAdjustmentOrder.store','id'=>'warehouseorderform','name'=>'warehouseorderform','class'=>'form-horizontal')) !!}
            	  	<div class="row">
                        <div class="col-md-6">
                             {!! Form::hidden('order_type', 2,array('id'=>'order_type')) !!}

                            <div class="form-group row">
                                {!! Form::label('order_date', trans('main.order_date'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                    {!! Form::text('order_date', date('d.m.Y'),array('class'=>'form-control', 'disabled', 'id'=>'warehouse_order_date' )) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('priority', trans('main.priority'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                    {!! Form::select('priority',@$priorities, '02',array('class'=>'form-control', 'disabled','id'=>'priority')) !!}
                                </div>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('warehouse', trans('main.warehouse'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                      {!! Form::select('warehouse', @$warehouses, @$warehouseorder['warehouse'],array('class'=>'form-control', 'disabled','id'=>'warehouse' ,'placeholder' => trans('main.selected'))) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('order_status', trans('main.order_status'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                                <div class="col-md-6">
                                    {!! Form::select('order_status', @$status, "03",array('class'=>'form-control', 'disabled','id'=>'order_status')) !!}
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
                   </div>

                    <div class="col-md-12  table-responsive">
                        <table class="table table-responsive" id="warehouse_product_order_table">
                            <thead>
                                <tr>
                                    <th width="30%">{!! trans('main.product') !!}</th>

                                    <th width="15%">{!! trans('main.location') !!}</th>

                                    <th width="13%">{!! trans('main.qty') !!}<label id="order_qty_symbol" style="display: none;">(+/-)</label></th>

                                    <th width="18%">{!! trans('main.comment') !!}</th>

                                    <th width="10%" class='rec_qty_td rec_qty_element'>{!! trans('main.rec_qty') !!}</th>

                                    <th width="10%" class='rec_date'>{!! trans('main.rec_date') !!}</th>
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
                                                {!! Form::select('product_location', @$destination_locations, @$value->location_id ,array('class'=>'form-control select2 product_location', 'disabled')) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('qty', number_format(@$value->qty, 2, ',', ''),array('class'=>'form-control qty', 'disabled', 'onkeyup' => 'checkLocationIsSeleted(this);' )) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('comment', @$value->comment, array('class'=>'form-control comment')) !!}
                                            </td>

                                            <td class="rec_qty_element">
                                                {!! Form::text('supplier_receive_input', number_format(@$value->qty, 2, ',', ''),array('class'=>'form-control supplier_receive_input', 'disabled')) !!}
                                            </td>
                                            <td class='rec_date'>
                                                <input type="text" class="form-control" value="{{ date('d.m.Y') }}" disabled/>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                        @php $i++; @endphp
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {!! Form::hidden('ccsheet_id', @$ccsheet_id, array('class'=>'form-control','id'=>'ccsheet_id' )) !!}
                    {!! Form::textarea('product_details', '', array('class'=>'form-control hide_div','id'=>'product_details' )) !!}
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
		$(document).on("click", ".warehouseorder_submit_btn", function() {
		    displayBlockUI();
		    $('.form-control').removeAttr('disabled');
		    $("#product_details").val("");
		    var order_product_table_row_count = $("#hidden_warehouse_table_row_count").val();
		    var product_data = [];
		    $('.product_tr').each(function() {
		        if ($(this).closest('tr').find('.order_product').val() && $(this).closest('tr').find('.order_product').val() != "Select" && $(this).closest('tr').find('.order_product').val() != "velg") {
		            product_data.push({
		                "product_id": $(this).closest('tr').find('.order_product').val(),
		                "product_text": $(this).closest('tr').find('.order_product option:selected').text(),
		                "location_id": $(this).closest('tr').find('.product_location').val(),
		                "location_text": $(this).closest('tr').find('.product_location option:selected').text(),
		                "qty": replaceComma($(this).closest('tr').find('.qty').val()),
		                "comment": $(this).closest('tr').find('.comment').val(),
		            });
		        }
		    });
		    if (product_data) {
		        $("#product_details").val(JSON.stringify(product_data));
		    }
		    $("#warehouseorderform").submit();
		});
	</script>
@endsection
