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
					@if ($type == 1)
		 				{!! __('main.offer') !!}
		 			@else
		 				{!! __('main.order') !!}
		 			@endif
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
								{{ __('main.phone')}}
							</div>
							<div class="col-8">
								{{ @$company_information->phone }} 
							</div>
						</div>
					</div>
					<div class="col-7">
						<div class="row">
							<div class="col-3 offset-3">
								{{ __('main.bank')}}
							</div>
							<div class="col-6">
								{{ @$company_information->account_number }} 
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix" style="margin-bottom: 1.10rem !important"></div>
				<div class="row">
					<div class="col-12">
						<nobr>{{ __('main.email')}}:</nobr> {{ @$company_information->company_email }} 
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						{{ @$company_information->web_page }} 
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						Org.nr.{{ @$company_information->company_VAT }}
					</div>
				</div>
				<div class="clearfix" style="margin-bottom: 1.00rem !important"></div>
				<div class="row" style="font-size: 13px;">
					<div class="col-6">
						<div class="row">
							<div class="col-12 offset-1">
								{{ @$customer_details->name }}
							</div>
						</div>
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
								<b>{{ __('main.deliveraddress') }}</b>
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$orders->deliveraddress1 }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$orders->deliveraddress2 }}
							</div>
						</div>
						<div class="row">
							<div class="col-12 offset-7">
								{{ @$orders->deliveraddress_zip }} {{ @$orders->deliveraddress_city }}
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix" style="margin-bottom: .50rem !important"></div>
				<div class="row">
					<div class="col-6">
						<div class="row">
							<div class="col-4">
								{{ __('main.phone') }}.
							</div>
							<div class="col-6 offset-2">
								{{ @$customer_details->phone }}
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
						@if ($type == 1)
							{!! __('main.offer_no') !!}
			 			@else
							{!! __('main.order_no') !!}
			 			@endif
					</div>
					<div class="col-6">
						@if ($type == 1)
							{!! @$orders->offer_number !!}
			 			@else
							{!! @$orders->order_number !!}
			 			@endif
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.customer_no') !!}
					</div>
					<div class="col-6">
						{!! @$customer_details->customer !!}
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.delivery') !!}
					</div>
					<div class="col-6">
						@if ($type != 1)
			 				{{ @$shipping_details ? $shipping_details->product_name : ''  }} 
			 			@endif
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.shipment_terms') !!}
					</div>
					<div class="col-6">
						Mottaker betaler frakt
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{!! __('main.currency') !!}
					</div>
					<div class="col-6">
						NOK
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						@if ($type == 1)
							{!! __('main.offer_date') !!}
			 			@else
							{!! __('main.order_date') !!}
			 			@endif
					</div>
					<div class="col-6">
						@if(@$orders->order_date)
							<?php echo date('d.m.Y', strtotime($orders->order_date)); ?>
						@endif
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						{{ __('main.delivery_date') }}
					</div>
					<div class="col-6">
						@if(@$orders->date_completed)
							<?php echo date('d.m.Y', strtotime($orders->date_completed)); ?>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						{{ __('main.pmt_terms') }}
					</div>
					<div class="col-6">
						Netto {{ @$orders->pmt_term }} dager
					</div>
				</div>
				@if ($type == 1)
					<div class="row">
						<div class="col-6">
							{{ __('main.valid_until') }}
						</div>
						<div class="col-6">
							@if(@$orders->offer_due_date)
								<?php echo date('d.m.Y', strtotime($orders->offer_due_date)); ?>
							@endif
						</div>
					</div>
				@endif
				<div class="row">
					<div class="col-6">
						{{ __('main.var_ref') }}.
					</div>
					<div class="col-6">
						{{ @$department_chiefs[@$orders->order_user] }}
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						{{ __('main.deres_ref') }}.
					</div>
					<div class="col-6">
						{{ @$contacts[@$orders->ordered_by] }}
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						{{ __('main.requisition') }}
					</div>
					<div class="col-6">
						{{ $orders->project_number}}
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix my-2"></div>
		@if(count(@$offerProducts) > 0)
	        <table class="table table-sm" style='font-size: 13px !important;'>
				<thead>
					<tr style="background-color: lightgrey !important; font-style:italic !important">
						<th scope="col" width="15%">{{ __('main.produktnr') }}.</th>
						<th scope="col" width="22%">{{ __('main.productDescription')  }}</th>
						<th scope="col" width="15%">{{ __('main.date_completed') }}</th>
						<th scope="col" width="10%" class="text-right">{{ __('main.leveres') }}</th>
						<th scope="col" width="9%" class="text-right">{{ __('main.enhetspris') }}</th>
						<th scope="col" width="10%" class="text-right">{{ __('main.rabatt') }}</th>
						<th scope="col" width="9%" class="text-right">{{ __('main.mva') }}.</th>
						<th scope="col" width="10%" class="text-right">{{ __('main.sum') }}.</th>
					</tr>
				</thead>
				<tbody>
					@php 
						$mvaArray = array();
					@endphp
					@if(@$offerProducts)
						@foreach($offerProducts as $product)
							@if ($product->is_text == 1)
								<tr>
									<td class="break-word" colspan="8">
										{{ @$product->product_text }}
									</td>
								</tr>
							@else
								@if (@$product->productDetails->description != "Hentes")
									<tr>
										@php 
											if (!array_key_exists(number_format(@$product->vat,0, '', ''), $mvaArray)) {
												$mvaArray[number_format(@$product->vat,0, '', '')]['price'] = $product->sum_ex_vat;
											} else {
												$mvaArray[number_format(@$product->vat,0, '', '')]['price'] = $product->sum_ex_vat + $mvaArray[number_format(@$product->vat,0, '', '')]['price'];
											}
										@endphp
										<td class="break-word">
											{{ @$product->productDetails->product_number }}
										</td>
										<td align="left">	

											{{  @$product->product_description ? @$product->product_description  : @$product->productDetails->description  }}

											@if($product->is_logistra == 1)
												<br>{{ @$product->track_number }}
											@endif
										</td>
										<td>
											@if(@$product->delivery_date)
												<?php echo date('d.m.Y', strtotime($product->delivery_date)); ?>
											@endif
										</td>
										<td align="right">
											{!! number_format(@$product->qty,2, ',', '')  !!}
										</td>
										<td align="right">{!! number_format(@$product->offer_sale_price,2, ',', '') !!}</td>
										<td align="right">{!! number_format(@$product->discount,2, ',', '') !!}</td>
										<td align="right">{!! number_format(@$product->vat,2, ',', '') !!}</td>
										<td align="right">{!! number_format(@$product->sum_ex_vat,2, ',', '') !!}</td>
									</tr>
									@if ($product->package == 1 && @$product->package_products)
										@foreach($product->package_products as $package_product)
											<tr>
												<td>
												</td>
												<td class="break-word">
													@if ($type == 1)
														{{ @$package_product->product_number }} - {{ @$package_product->description }}
													@else
														{{ @$package_product->productDetails->product_number }} - {{ @$package_product->productDetails->description }}
													@endif
												</td>
												<td>
													@if(@$package_product->delivery_date)
														<?php echo date('d.m.Y', strtotime($package_product->delivery_date)); ?>
													@endif
												</td>
												<td align="right">
													{!! number_format(@$package_product['qty'],2, ',', '')  !!}
												</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										@endforeach
									@endif
								@endif
							@endif
						@endforeach
					@endif
				</tbody>
			</table>
	    @endif
	    <div class="clearfix my-2"></div>
		 	<div class="row">
			<div class="col-1"><b>Mva. %</b></div>
			<div class="col-2" align="right"><b>Mva-grunnlag</b></div>
			<div class="col-2" align="right"><b>Mva.</b></div>
	    </div>
	    <div class="row">
	    	<div class="col-5"> <hr class="my-4"></div>
	    </div>
	    @if (@$mvaArray)
		    @foreach($mvaArray as $key => $value)
		    	<div class="row">
					<div class="col-1">{{ $key }}%</div>
					<div class="col-2" align="right">{!! @$value['price'] ? number_format(@$value['price'],2, ',', '') : ''; !!}</div>
					<div class="col-2" align="right">
						{{ number_format(findPercentage($value['price'], $key),2, ',', '') }}
					</div>
			    </div>
		    @endforeach
	    @endif
	    <div class="clearfix my-4"></div>
	    <div class="row">
	    	<div class="col-3"><strong>{!! __('main.comments') !!} : </strong></div>
			<div class="col-9">
				@if ($orders->comments != null)
					{!! nl2br(@$orders->comments) !!}
				@endif
			</div>
	    </div>
	    <div class="clearfix my-4"></div>
	    <div class="row">
	    	<div class="col-3"><strong>{!! $type == 1 ? __('main.standard_offer_text') : __('main.invoicecomments') !!} : </strong></div>
			<div class="col-9">
				@if ($orders->order_invoice_comments != null)
					{!! nl2br(@$orders->order_invoice_comments) !!}
				@endif
			</div>
	    </div>
	</div>
</body>
</html>
