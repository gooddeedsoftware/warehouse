<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
<meta name="viewport" content="text/html" charset="UTF-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- <link type="text/css" rel="stylesheet" href="{{ URL::to('/') }}/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="{{ URL::to('/') }}/js/jquery-3.4.1.min.js'"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/bootstrap/js/bootstrap.min.js"></script>  -->
</head>
<style>
	body {
		font-family: Arial !important;
	    font-size: 11px !important;
	}
	.table-borderless tbody tr td, .table-borderless tbody tr td, .table-borderless tdead tr td,.table-borderless tr td {
	    border: none !important;
	}
	.breakWord {
		word-wrap:  break-word;
	}
	hr {
      border-top: 1px solid rgba(0, 0, 0, 0.88) !important;
    }
	.table td {
	 	border-top: none !important; 
	 	border-bottom: none  !important; 
	}
	.col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto {
     	padding-right: 0px !important; 
     	padding-left: 0px !important; 
	}
	.row {
	 	margin-right: 0px !important; 
	 	margin-left: 0px !important; 
	}

</style>
<body>
	<div class="container-fluid pl-0 pr-0">
	<div class="row">
		<div class="clearfix" style="margin-bottom: 2.5rem !important"></div>
	</div>
	<div class="row">
		<div class="col-8">
			<b style="font-size: 18px">GANTIC AS</b>
		</div>
		<div class="col-4">
			<b style="font-size: 18px" class="float-right">
				{{ __('report.packing_list') }}
	 		</b>
		</div>
	</div>	
	<div class="row">
		<div class="col-6" style="padding-right:6% !important">
			<div class="row">
				<div class="col-5">
					<div class="col-l">
						{{ @$company_information->post_address }}
					</div>
					<div class="col-l">
						{{ @$company_information->zip }} {{ @$company_information->city }}
					</div>
					<div class="row">
						<div class="col-4">
							{{ __('report.phone')}}
						</div>
						<div class="col-8">
							{{ @$company_information->phone }} 
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix" style="margin-bottom: 1.00rem !important"></div>
			<div class="row" style="font-size: 13px;">
				<div class="col-6">
					<div class="row">
						<div class="col-12 offset-1">
							{{ @$orders->visitingAddress1 }}
						</div>
					</div>
					<div class="row">
						<div class="col-12 offset-1">
							{{ @$orders->visitingAddress2 }}
						</div>
					</div>
					<div class="row">
						<div class="col-12 offset-1">
							{{ @$orders->visitingAddressZip }} {{ @$orders->visitingAddressCity }}
						</div>
					</div>
				</div>
				<div class="col-6 float-right">
					<div class="row">
						<div class="col-12 offset-7">
							<b>{{ __('report.deliveraddress') }}</b>
						</div>
					</div>
					<div class="row">
						<div class="col-12 offset-7">
							{{ @$orders->visitingAddress1 }}
						</div>
					</div>
					<div class="row">
						<div class="col-12 offset-7">
							{{ @$orders->visitingAddressZip }} {{ @$orders->visitingAddressCity }}
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix" style="margin-bottom: 1.25rem !important"></div>
			<div class="row">
				<div class="col-3">
					{{ __('report.var_ref') }}
				</div>
				<div class="col-9">
					{{ @$department_chiefs[@$orders->order_user] }}
				</div>
			</div>
			<div class="row">
				<div class="col-3">
					{{ __('report.deres_ref') }}
				</div>
				<div class="col-9">
					{{ @$contacts[@$orders->ordered_by] }}
				</div>
			</div>
			<div class="row">
				<div class="col-3">
					{{ __('report.requisition') }}
				</div>
				<div class="col-9">
					{{ $orders->project_number}}
				</div>
			</div>
		</div>
		<div class="col-6" style="padding-left:20% !important">
			<div class="row">
		 		<div class="clearfix" style="margin-bottom: 1.00rem !important"></div>
		 	</div>
	 		<div class="row">
				<div class="col-6">
					{!! __('report.side') !!}
				</div>
				<div class="col-7">
					1
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					{!! __('report.order_no') !!}
				</div>
				<div class="col-6">
					{!! @$orders->order_number !!}
				</div>
			</div>

			<div class="row">
				<div class="col-6">
					{!! __('report.customer_no') !!}
				</div>
				<div class="col-6">
					{!! @$customer_details->customer !!}
				</div>
			</div>

			<div class="row">
				<div class="col-6">
					{!! __('report.delivery') !!}
				</div>
				<div class="col-6">
					{{ @$logistra_product->product ? @$logistra_product->product->description : '' }}
				</div>
			</div>

			<div class="row">
				<div class="col-6">
					{!! __('report.shipment_terms') !!}
				</div>
				<div class="col-6">
					Mottaker betaler frakt
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					{{ __('report.delivery_date') }}
				</div>
				<div class="col-6">
					@if(@$orders->date_completed)
						<?php echo date('d.m.Y', strtotime($orders->date_completed)); ?>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					{{ __('report.pmt_terms') }}
				</div>
				<div class="col-6">
					Netto {{ @$orders->pmt_term }} dager
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					{!! __('report.order_date') !!}
				</div>
				<div class="col-6">
					@if(@$orders->order_date)
						<?php echo date('d.m.Y', strtotime($orders->order_date)); ?>
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="clearfix my-2"></div>
        <table class="table table-sm" style='font-size: 11px !important;'>
			<thead>
				<tr style="background-color: lightgrey !important; font-style:italic !important">
					<th scope="col" width="10%">{{ __('report.produktnr') }}.</th>
					<th scope="col" width="15%">{{ __('report.productDescription')  }}</th>
					<th scope="col" width="18%">{{ __('main.location')  }}</th>
					<th scope="col" width="13%">{{ __('report.delivery_date')  }}</th>
					<th scope="col" width="13%">{{ __('report.bestilt')  }}</th>
					<th scope="col" width="8%">{{ __('report.tidllevert')  }}</th>
					<th scope="col" width="8%">{{ __('report.rest')  }}</th>
					<th scope="col" width="7%">{{ __('report.leveres')  }}</th>
				</tr>
			</thead>
			<tbody>
				@if(@$order_materials)
					@foreach($order_materials as $product)
						@if ($product->is_text == 1)
							<tr>
								<td class="break-word" colspan="8">
									{{ @$product->product_text }}
								</td>
							</tr>
						@else
							<tr>
								<td class="break-word">
									{{ @$product->productDetails->product_number }}
								</td>
								<td align="left">	
									{{  @$product->product_description ? @$product->product_description  : @$product->productDetails->description  }}
								</td>
								<td>
									@if ($product->is_package != 1)
										@if (@$product->location_details) 
											{{ $product->location_details->name }}
										@else
											{{ __('report.undefined') }}
										@endif
									@else
										-
									@endif
								</td>
								<td>
									{{  @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '' }}
								</td>
								<td>
									{!! number_format(@$product->order_quantity, 2, ",", "") !!}
								</td>
								<td>
									{!! number_format(@$product->invoice_quantity, 2, ",", "") !!}
								</td>
								<td>
									0,00
								</td>
								<td> 
									@if(@$product->approved_product == 1)
										0,00
									@else
										{!! number_format(@$product->quantity, 2, ",", "") !!}
									@endif
								</td>
							</tr>
							@if(@$product->package_contents && $product->is_package == 1 && $product->quantity >= 1) 
								@foreach($product->package_contents as $package_product)
									<tr>
										<td class="break-word">
										</td>
										<td align="left" class="break-word">
											{{  @$package_product->product_description ? @$package_product->product_description  : @$package_product->productDetails->description  }}
										</td>
										<td>
											@if (@$package_product->location_details) 
												{{ $package_product->location_details->name }}
											@else
												{{ __('report.undefined') }}
											@endif
										</td>
										<td>
											{{  @$package_product->delivery_date ? date('d.m.Y', strtotime($package_product->delivery_date)) : '' }}
										</td>
										<td>
											{!! number_format(@$package_product->order_quantity, 2, ",", "") !!}
										</td>
										<td>
											@if(@$product->approved_product == 1)
												{!! number_format(@$package_product->invoice_quantity, 2, ",", "") !!}
											@else
												0,00
											@endif
										</td>
										<td>
											0,00
										</td>
										<td>
											@if(@$product->approved_product == 1)
												0,00
											@else
												{!! number_format(@$package_product->quantity, 2, ",", "") !!}
											@endif
										 </td>
									</tr>
								@endforeach
							@endif
						@endif
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>
</body>
</html>
