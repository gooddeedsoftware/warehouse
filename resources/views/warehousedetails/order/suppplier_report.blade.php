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
					{{ __('report.purchase_order') }}
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
						<div class="col-l">
							<nobr>{{ __('main.email')}}:</nobr> {{ @$company_information->company_email }}
						</div>
						<div class="col-l">
							Org.nr.{{ @$company_information->company_VAT }}
						</div>
					</div>
				</div>

				<div class="clearfix" style="margin-bottom: 1.00rem !important"></div>
				<div class="row" style="font-size: 13px;">
					<div class="col-6">
						<div class="row">
							<div class="col-12 offset-1">
								<b>{{ __('main.supplier') }}</b>
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-1">
								{{  @$supplier_details->name }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-1">
								{{  @$supplier_address->address1 }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-1">
								{{  @$supplier_address->address2}}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-1">
								{{  @$supplier_address->zip }} {{  @$supplier_address->city}}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-1">
								{{  @$countries[@$supplier_address->country] }}
							</div>
						</div>
					</div>
					<div class="col-6 float-right">
						<div class="row">
							<div class="col-12 offset-7">
								<b>{{ __('main.deliveraddress') }}</b>
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$warehouseorder->company }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$warehouseorder->post_address }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$warehouseorder->zip }} {{ @$warehouseorder->city }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$warehouseorder->country }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6" style="padding-left:20% !important">
				<div class="row">
			 		<div class="clearfix" style="margin-bottom: 1.00rem !important"></div>
			 	</div>
		 		<div class="row">
					<div class="col-6">
						{!! __('main.side') !!}
					</div>
					<div class="col-6">
						1
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						{!! __('main.purchase_order_no') !!}
					</div>
					<div class="col-6">
						{!! @$warehouseorder->order_number !!}
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.supplier_no') !!}
					</div>
					<div class="col-6">
						{!! @$supplier_details->customer !!}
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.delivery') !!}
					</div>
					<div class="col-6">
						{!! @$warehouseorder->delivery_method !!}
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.currency') !!}
					</div>
					<div class="col-6">
						{{ @$supplier_details->currency }}
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.order_date') !!}
					</div>
					<div class="col-6">
						@if(@$warehouseorder->order_date)
							<?php echo date('d.m.Y', strtotime($warehouseorder->order_date)); ?>
						@endif
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{{ __('main.delivery_date') }}
					</div>
					<div class="col-6">
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{{ __('main.pmt_terms') }}:
					</div>
					<div class="col-6">
						Netto {{ @$supplier_details->pmt_terms }} dager
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{{ __('report.var_ref') }}.
					</div>
					<div class="col-6">
						{{ @$users[@$warehouseorder->our_reference] }}
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						{{ __('report.deres_ref') }}.
					</div>
					<div class="col-6">
						{!! @$warehouseorder->supplier_ref !!}
					</div>
				</div>
			</div>
		</div>	
		<div class="row">
			<div class="clearfix my-3"></div>
			@if(@$product_details['product_details'] && count($product_details['product_details']) > 0)
		        <table class="table table-sm" style='font-size: 13px !important;'>
					<thead>
						<tr style="background-color: lightgrey !important; font-style:italic !important">
							<th scope="col" width="18%">{{ __('main.produktnr') }}.</th>
							<th scope="col" width="18%">{{ __('main.productDescription')  }}</th>
							<th scope="col" width="18%" class="text-right">{{ __('main.quantity') }}</th>
							<th scope="col" width="18%" class="text-right">{{ __('report.unit_price_excl_vat') }}</th>
							<th scope="col" width="18%" class="text-right">{{ __('report.total_ex_vat') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($product_details['product_details'] as $product)
							<tr>
								<td>{{ @$product->productDetails->nobb }}</td>
								<td>{{ @$product->productDetails->description }}</td>
								<td class="text-right">{!! number_format(@$product->qty,2, ',', ' ') !!}</td>
								<td class="text-right">{!! number_format(@$product->vendor_price,2, ',', ' ') !!}</td>
								<td class="text-right">{!! number_format(@$product->cal_vendor_price,2, ',', ' ') !!}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			@endif
		</div>

		<div class="clearfix my-4"></div>
	    <div class="row">
	    	<div class="col-3"><strong>{!! __('main.comments') !!} : </strong></div>
			<div class="col-9">
				@if ($warehouseorder->order_comment != null)
					{!! nl2br(@$warehouseorder->order_comment) !!}
				@endif
			</div>
	    </div>
	</div>
</body>
</html>
