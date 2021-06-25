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
<style type="text/css">
	.toggle.btn {
    	min-width: 10.7rem !important;
	}
</style>
<div class="container warehouseContainer">
    <div class="card">
        <div class="card-header warehouseContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">{!! trans('main.stock') !!}</a>
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
                    <a class="nav-link" href="{!! route('main.productpackage.index') !!}">{!! trans('main.productpackage') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('whs_history') }}">{!! trans('main.history') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
        	@php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            {!! Form::open(array('route' => array('main.warehousedetails.search', $query_string), 'id' => 'stock_search_form')) !!} 
                <div class="row">
                    <div class="col-3 col-sm-6 col-md-2 form-group">
                       	<button type="button" class="btn btn-primary export_btn" id="export_btn">{!! trans('main.export') !!}</button>
                    </div>
                    <div class="col-9 col-sm-6 col-md-4 text-right form-group">
					 	{!! Form::checkbox('show_on_ordered1', '0', '', array("data-toggle" => "toggle",  'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.show_all'), "data-off" => trans('main.show_on_order'), "id" => "show_on_ordered1")) !!}
					 	{!! Form::hidden('show_on_ordered', @Session::get('warehousedetails_search')['show_on_ordered'] ? @Session::get('warehousedetails_search')['show_on_ordered'] : '' , array('id'=>'show_on_ordered')) !!}
					</div>
					<div class="col-md-3 form-group">
						{!!Form::select('search_by_warehouse',@$warehouse, @Session::get('warehousedetails_search')['search_by_warehouse'], array('class'=>'form-control','id'=>'search_by_warehouse','placeholder'=>trans('main.selected')))!!}
					</div>
					<div class="col-md-3">
						<div class="form-group input-group">
							{!! Form::text('stock_search', @Session::get('warehousedetails_search')['stock_search'], array('id'=>'stocksearch','class' => 'form-control searchField','placeholder'=>trans('main.search') )) !!}
							<div class="input-group-append">
								<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
							</div>
						</div>
					</div>
                </div>
            {!! Form::close() !!}
            <div class="table-responsive">
	            <table class="table table-striped table-hover" id='stock_table'>
	                <thead>
	                    <tr>
	                        <th>
	                            <a >{!! trans('main.product_number') !!}</a>
	                        </th>
	                        <th>
	                            <a>{!! trans('main.description') !!}</a>
	                        </th>
	                        <th>
	                            <a>{!! trans('main.onstock') !!}</a>
	                        </th>
	                        @if (@Session::get('warehousedetails_search')['show_on_ordered'] == 1 || @Session::get('warehousedetails_search')['search_by_warehouse'] == '') 
		                        <th>
		                            <a>{!! trans('main.onorder') !!}</a>
		                        </th>
	                        @endif
	                        <th>
                    			<a>{!! trans('main.customer_picked_qty') !!}</a>
                			</th>
                			<th>
                    			<a>{!! trans('main.customer_order') !!}</a>
                			</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    @if (@$stocks)
	                        @foreach (@$stocks['all'] as $stock)
	                            @if (@$stock->id != "")
		                            <tr data-toggle="collapse" data-target="#collapse_{!! @$stock->id !!}" class="clickable" data-collapse-group="collapse_tr" onclick="getOnstockDetails('{!! @$stock->id !!}')" product_id="{!! @$stock->id !!}">
		                                <td><a>{!! @$stock->product_number !!} </a></td>
		                                <td><a>{!! str_limit(@$stock->description,40) !!}</a></td>
		                                <td><a data-toggle="collapse" data-target="#collapse_{!! @$stock->id !!}" class="clickable" data-collapse-group="collapse_tr" onclick="getOnstockDetails('{!! @$stock->id !!}')">{!! number_format(@$stock->on_stock, 2, ',', ' ') !!}</a></td>
		                                @if (@Session::get('warehousedetails_search')['show_on_ordered'] == 1 || @Session::get('warehousedetails_search')['search_by_warehouse'] == '')
		                                	<td class="customer_on_order">{!! number_format((float)@$stock->on_order,2, ',', ' ') !!}</td>
										@endif
		                                <td class="customer_order_qty" customer_order_qty_val='1' customer_order="{!! @$stock->customer_order !!}">
		                                	<span>
		                                		@if (@$stock->customer_order > 0)
		                                			{!! number_format((float)@$stock->customer_order,2, ',', ' ') !!}
		                                		@endif
		                                	</span>
		                                </td>
		                                <td class="sale_order_qty">
		                                	{!! number_format((float)@$sale_orders[@$stock->id],2, ',', ' ') !!}
		                                </td>
		                            </tr>
		                            <tr id="collapse_{!! @$stock->id !!}" class="collapse" >
		                            	<td id="stock_td_{!! @$stock->id !!}" colspan="6">
			                                {!! @$stock->product_details !!}
			                            </td>
	                            	</tr>
	                            @endif
	                        @endforeach
	                    @endif
	                </tbody>
	            </table>
	        </div>
	        @if (@$stocks)
			    @include('common.pagination',array('paginator'=>@$stocks['all'], 'formaction' => 'stock_search_form'))
			@endif
        </div>
    </div>
</div>
{!! Form::open(array('route' => 'main.export', 'id' => 'stock_export_form', 'class' => 'hide_div')) !!}
    @if (@$stocks && count($stocks[0]) > 0)
        @foreach($stocks[0] as $stock)
            {!! Form::hidden('export_ids[]', @$stock->id ) !!}
        @endforeach
    @endif
    {!! Form::hidden('object_id', @$search_by_warehouse ) !!}
    {!! Form::hidden('model', 'stock') !!}
{!! Form::close() !!}
@endsection

<a data-backdrop="static" data-keyboard="false" data-toggle="modal"  data-target="#customer_order_modal" id="customer_order_modal_btn" style="display: none;">
Test
</a>
<div class="modal fade" id="customer_order_modal" role="dialog" aria-labelledby="addNewModalLabel">
    <div class="modal-dialog">
       	<div class="modal-content">
			<div class="modal-header">
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			</div>
    	</div>
    </div>
</div>

@section('page_js')
<script type="text/javascript">
	var token = "{!! csrf_token() !!}";
	var url = "{!! URL::to('/') !!}";
	var show_on_ordered = "{!! @Session::get('warehousedetails_search')['show_on_ordered'] !!}";

	$(document).ready(function () {
		$("#stock_table").tablesorter();
		if (show_on_ordered > 0) {
			$("#show_on_ordered1").parent().removeClass("btn-default off");
			$("#show_on_ordered1").parent().addClass("btn-primary");
			$("#show_on_ordered1").attr("checked", true);
			$("#show_on_ordered1").val(1);
		}
	});

	// submit form when slider is chnaged
	$("#show_on_ordered1").change(function () {
		var show_on_ordered_val = $('#show_on_ordered').val();
		if (show_on_ordered_val == 1) {
			$(this).val(0);
			$('#show_on_ordered').val(0);
		} else {
			$(this).val(1);
			$('#show_on_ordered').val(1);
		}
		$("#stock_search_form").submit();
	});

	$("#search_by_warehouse").on("change", function (e) {
		$("#stock_search_form").submit();
	});

	$("#export_btn").on("click", function (e) {
		$('#stock_export_form').submit();
	});

	//loading the product detail
	function getOnstockDetails(stock_id, obj) {
		if (stock_id) {
			displayBlockUI();
			var warehouse_id = $("#search_by_warehouse").val();
			$.ajax({
				type : 'POST',
				url : "{!! route('main.warehousedetails.getOnstockDetails') !!}",
				data : {
					_token:'{!! csrf_token() !!}',
					'stock_id' : stock_id,
					'warehouse_id' : warehouse_id
				},
				async : false,
				success : function (response) {
					if (response) {
						var decoded_data = $.parseJSON(response);
						if (decoded_data['status'] == SUCCESS) {
							$("#stock_td_"+stock_id).html(decoded_data['data']);
						} else if (decoded_data['data'] == ERROR){
							$("#stock_td_"+stock_id).html(decoded_data['message']);
						}
					}
					setTimeout($.unblockUI, 200);
				},
				error : function () {
					setTimeout($.unblockUI, 200);
				}
			});
		}
	}


	$(document).on("click", ".customer_order_qty", function () {
		var type = $(this).attr('customer_order_qty_val');
		if ($(this).attr('customer_order') != '' && $(this).attr('customer_order') != undefined && $(this).attr('customer_order') > 0) {
			if (type == 1) {
				var product_id = $(this).closest('tr').attr('product_id');
				showCustomerOrderDetails('{!! URL::to('/getcustomerorder/product') !!}/'+product_id);
			} else {
				var product_id = $(this).closest('tr').attr('product_id');
				var location_id = $(this).closest('tr').attr('location_id');
				var warehouse_id = $(this).closest('tr').attr('warehouse_id');
				if (product_id && location_id && warehouse_id) {
					showCustomerOrderDetails('{!! URL::to('/getcustomerorders/bywarehouse') !!}/'+product_id+'/'+location_id+'/'+warehouse_id);
				}
			}
		}
	});
	
	function showCustomerOrderDetails(customerOrderURL) {
		$(".subPanelModel").modal("show");
	    $('#subPanelContent').load(customerOrderURL, function() {
	    });
	}


	$(document).on("click", ".customer_on_order", function () {
		var product_id = $(this).closest('tr').attr('product_id');
		$(".subPanelModel").modal("show");
	    $('#subPanelContent').load('{!! URL::to('/getOnOrderDetails') !!}/'+product_id, function() {
	    });
	});

	
	$(document).on("click", ".sale_order_qty", function () {
		var product_id = $(this).closest('tr').attr('product_id');
		$(".subPanelModel").modal("show");
	    $('#subPanelContent').load('{!! URL::to('/getSaleOrderDetails') !!}/'+product_id, function() {
	    });
	});

</script>
@endsection
