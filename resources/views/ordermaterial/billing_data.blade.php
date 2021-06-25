@extends('layouts.layouts')
@section('title',__('main.product'))
@section('header')
	<h3><i class="icon-message"></i>{!!__('main.product') !!}</h3>
	{!!__('main.product') !!}
@stop

@section('help')
	<p class="lead">{!!__('main.product') !!}</p>
	<p>{!!__('main.orders.help') !!}</p>
@stop
<style type="text/css">
	.billing_data_table {
		table-layout: fixed !important; width: 100% !important
	}
	.billing_data_table input {
		width: 100% !important;
	}
	table.billing_data_table tbody tr td {
		padding: 6px !important;
		padding-left: 3px !important;
		padding-right: 3px !important;
	}
	.custom-align {
		text-align: right !important; 
	}
</style>
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header cutomerOrderContainer-Header">
             <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                	<a class="nav-link"  href="{!! route('main.order.edit', @$order_id) !!}">
                		<span class="d-none d-sm-block">{!! __('main.order') !!}</span>
                        <i class="d-block d-sm-none fa fa-file"></i>
                	</a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.ordermaterial.listOrderMaterials', @$order_id) !!}">
                        <span class="d-none d-sm-block">{!! __('main.materials') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="#">
                       	<span class="d-none d-sm-block">{!! __('main.billing_data') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
        	<div class="row form-group">
				<div class="col-xs-12 col-sm-12">
        			<a class="btn btn-primary" href="javascript:;" id="send_to_uni">
	        			{{ __('main.send_to_uni') }}
        			</a>

        			<a class="btn btn-primary" href="javascript:;" id="update_invoice_number">
	        			{{ __('main.update_invoice_number') }}
        			</a>
        			<b><label id="invoice_number_label">{{ @$orders->invoice_number }}</label></b>
				</div>
		    </div>
		    <div class="table-responsive">
				<table class="table billing_data_table">
					<thead>
						<tr>
							<th width="2%">
								<input id="select_all_products" type="checkbox" class="mb-1">
							</th>
							<th width="7%">{!! __('main.art') !!}</th>
							<th width="15%">{!!__('main.description') !!}</th>
							<th width="6%">{!!__('main.ord_qty') !!}</th>
							<th width="5%">{!!__('main.return') !!}</th>
							<th width="5%">{!!__('main.inv_qty') !!}</th>
							<th width="5%">{!!__('main.unit') !!}</th>
							<th width="6%">{!!__('main.price') !!}</th>
							<th width="5%">{!!__('main.rabatt') !!}</th>
							<th width="5%">{!!__('main.ex_vat') !!}</th>
							<th width="5%">{!!__('main.vat') !!}</th>
							<th width="15%">{!!__('main.tracking_number') !!}</th>
							<th width="7%">{!!__('main.uni_status') !!}</th>
						</tr>
					</thead>
					<tbody>
						@if (@$ordermaterials)
							@foreach ($ordermaterials as $ordermaterial)
								@if ($ordermaterial->is_text == 1)
									<tr>
										<td>
											@if ($ordermaterial->approved_product == 1 && !$ordermaterial->uni_status)
												<input class="approve_product" type="checkbox" value='{!! $ordermaterial->id !!}' id='approve_product_{!! $ordermaterial->id !!}'>
											@endif
										</td>
                                        <td class="break-word" colspan="10">
											{{ @$ordermaterial->product_text }}
										</td>
										<td>
											{{ $ordermaterial->track_number }}
										</td>
										<td>
											{{ $ordermaterial->sent_id }}
										</td>
                                    </tr>
								@else
								 	@if (@$ordermaterial->product->description != "Hentes")
										@php
											$description = @$ordermaterial->product_description ? $ordermaterial->product_description : @$ordermaterial->product->description;
											
											$invoice_quantity = @$ordermaterial->quantity;
											if (@$ordermaterial->invoice_quantity > 0) {
												$invoice_quantity = @$ordermaterial->invoice_quantity;
											}
											$unit = @$ordermaterial->unit;
						                    $sale_price = $ordermaterial->offer_sale_price;
							                $discount = @$ordermaterial->discount;
											$vat = @$ordermaterial->vat;
										 @endphp
										<tr class="material_class_{!! $ordermaterial->invoiced !!}">
											<td width="3%">
												@if ($ordermaterial->approved_product == 1 && !$ordermaterial->uni_status)
													<input class="approve_product" type="checkbox" value='{!! $ordermaterial->id !!}' id='approve_product_{!! $ordermaterial->id !!}'>
												@endif
											</td>
											<td width="9%"> {!! Form::text('product_number', @$ordermaterial->product->product_number,array('class'=>'product_number width', 'disabled' => 'disabled')) !!}</td>
											<td width="14%"> {!! Form::text('description', $description,array('class'=>'description width', 'disabled' => 'disabled')) !!}</td>
											<td width="6%">
												<input size="10" class="custom-align quantity validateNumbersWithComma" type="text"  value="{!! number_format(@$ordermaterial->order_quantity, 2, ',', '') !!}" disabled>
											</td>
											<td width="6%"> {!! Form::text('return_quantity', number_format(@$ordermaterial->return_quantity,2, ",", ""), array('class'=>'order_quantity width custom-align', 'disabled' => 'disabled')) !!} </td>
											<td width="6%"> 
												<input size="10" class="custom-align invoice_quantity validateNumbersWithComma" readonly disabled type="text"  value="{!! number_format(@$invoice_quantity, 2, ',', '') !!}" onblur="calculateMVAValue(this);">
											<td width="6%"> 
												{!! Form::text('unit', @$invoice_units[$ordermaterial->unit], array('class'=>'unit width', 'disabled' => 'disabled' )) !!} 
											</td>
											<td width="6%">
												<input size="10" class="custom-align sale_price validateNumbersWithComma" readonly disabled type="text"  value="{!! number_format($ordermaterial->offer_sale_price, '2', ',', '') !!}" onblur="calculateMVAValue(this);">
											<td width="6%">
												<input size="10" class="custom-align discount validateNumbersWithComma" readonly disabled type="text"  value="{!! number_format(@$discount, '2', ',', '') !!}" onblur="calculateMVAValue(this);">
											</td>
											<td width="9%"> {!! Form::text('ex_vat', '', array('class'=>'width ex_vat custom-align', 'disabled' => 'disabled')) !!} </td>
											<td width="7%"> 
												<input size="10" class="custom-align vat validateNumbersWithComma" readonly disabled type="text"  value="{!! number_format(@$vat, '2', ',', '') !!}" onblur="calculateMVAValue(this);">
												<button id="material_{!! $ordermaterial->id !!}" data-id="{!! $ordermaterial->id !!}" class="save_billing_data" data-invoiced="{!! @$ordermaterial->invoiced !!}" style="display: none;"></button>
											</td>
											<td width="7%">
												{{ $ordermaterial->track_number }}
											</td>
											<td width="7%">
												{{ $ordermaterial->sent_id }}
											</td>
										</tr>
									@endif
								@endif
							@endforeach
						@endif
						</tr>
					</tbody>
				</table>
			</div>
			<div class="row">
				<div class="col-sm-12 col-md-4">
					<b>{!! __('main.last_updated') !!}: <label id="updated_time">{!! @$last_updated_time !!}</label>, <label id="updated_user">{!! @$last_updated_by !!}</label></b>
				</div>
				<div class="col-sm-12 col-md-4">
					<b>{!! __('main.invoice_ex_vat') !!}:  <label id="invoiced_ex_vat"></label></b>
				</div>
				<div class="col-sm-12 col-md-4">
					<b>{!! __('main.billing_total_ex_vat') !!}:  <label id="billing_ex_vat"></label></b>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="hide_class">
    {!! Form::open( array('route' => 'main.order.createOrderinUNI','class'=>'send_to_uni','id'=>'send_to_uni_form') ) !!}
        {{ Form::hidden('order_id', @$order_id,array('id'=>'order_id')) }}
        {{ Form::hidden('materials','',array('id'=>'materials')) }}
    {!! Form::close() !!}
</div>

<div class="modal fade invoiceModal" data-backdrop="static" data-keyboard="false" id="invoiceModal" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="invoiceModal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>
                        {{  @$orders->order_number }}  - {!!__('main.invoice') !!}
                    </b>
                </h5>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
            	 <div class="form-group row">
			    	{!! Form::label('invoice_number', __('main.invoice_number'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					<div class="col-md-6">
						{!! Form::text('invoice_number',@$orders->invoice_number,array('class'=>'form-control','id' => 'invoice_number')) !!}
					</div>
			    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="save_number" type="button">
                    {!! __('main.save') !!}
                </button>
                <button class="btn btn-danger" data-dismiss="modal" type="button">
                    {!! __('main.cancel') !!}
                </button>
            </div>
        </div>
    </div>
</div>


@stop
@section('page_js')
	<script type="text/javascript">
		var billing_data_store_msg = "{!! __('main.billing_data_store_msg') !!}";
		var select_atleat_one = '{!! __("main.select_atleat_one") !!}';
		var order_id = "{!! @$order_id !!}";
		var invoiced_hours = 0;
		var user_name = "{!! @$user_name !!}";
		var date = "{!! @$date !!}";
		var customer_name = "{!! @$customerName !!}";
		var order_number = "{!! @$orders->order_number !!}";
		var project = "{!! @$orders->project_number !!}";
		if (order_number) {
			var showText = order_number + " - " + customer_name.substring(0,10);
			if (project) {
				showText = showText  + " - " + project.substring(0,15)
			}
			$(".order_customer_label").text(showText);
		}
	</script>
	{!! Html::script('js/billingData.js') !!}
@endsection
