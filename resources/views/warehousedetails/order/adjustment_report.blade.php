<!DOCTYPE html>
<html>
<head>
<title>Warehouse Order Rapport</title>
<style>
body, body div, .deftext {
	font-size: 14px !important;
	font-family: Helvetica !important;
}
h4 {
   font-size: 12px !important;
   font-weight: bold;
}
div {
   font-size: 14px !important;
}
td {
   font-size: 12px !important;
}
th {
   font-size: 10px !important;
}

table, tr, td, th, tbody, thead, tfoot {
    page-break-inside: avoid !important;
}
label {
	font-weight: bolder !important;
}

</style>

<meta name="viewport" content="text/html" charset="UTF-8">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- <link type="text/css" rel="stylesheet" href="{{ URL::to('/') }}/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="{{ URL::to('/') }}/js/jquery-3.4.1.min.js'"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/bootstrap/js/bootstrap.min.js"></script>  -->

</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-5">
					<img src="{!! URL::to('/') !!}/images/maskinstyring_report_logo.png" alt="" style="margin-top: -22px"/>
				</div>
				<div class="col-7">
					<b>{!! trans('main.order_type') !!}</b> : {!! @$warehouse_details->order_type!!}
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<label>{!! trans('main.order_number') !!}: </label> {!! @$warehouse_details->order_number !!}
				</div>
				<div class="col-1"></div>
				<div class="col-7">
					<label>{!! trans('main.source_whs') !!}: </label> 
					{!! @$warehouse_details->warehouses->shortname !!}
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<label>{!! trans('main.order_date') !!}: </label> @php echo (@$warehouse_details->order_date ? date('d.m.Y', strtotime(@$warehouse_details->order_date)) : '') @endphp
				</div>
				<div class="col-1"></div>
				<div class="col-7">
					<label>{!! trans('main.comments') !!}: </label> {!! @$warehouse_details->order_comment !!} 
				</div>
			</div>
			<div class="row">
				@if(@$product_details)
					<table class="table">
						<thead>
							<tr>
								<th width="40%">{!! trans('main.product') !!}</th>
								<th width="12%">{!! trans('main.qty') !!}</th>
								<th width="12%">{!! trans('main.received_qty') !!}</th>	
								<th width="18%">{!! trans('main.received_location') !!}</th>
								<th width="18%">{!! trans('main.rec_date') !!}</th>
							</tr>
						</thead>
						<tbody>
							@foreach (@$product_details as $product)
								@php $x = 0; @endphp
								@if (@$product->order_details) 
									@foreach (@$product->order_details as $order_product)
										@foreach (@$order_product->serial_number_products as $serial_number_product)
											<tr>
												<td  class="font-size-11">{!! @$product->product_text !!} </td>
												<td class="font-size-11"> {!! @$product->qty ? number_format( @$product->qty,2, ',', ' ') : '' !!} </td>
												<td class="font-size-11"> {!! @$order_product->received_quantity ? number_format( @$order_product->received_quantity,2, ',', ' ') : '' !!} </td>
												<td class="font-size-11"> {!! @$locations[@$serial_number_product->rec_location_id] !!} </td>
												<td class="font-size-11"> {!! @$order_product->received_date !!} </td>
											</tr>
										@endforeach
									@endforeach
								@else
									<tr>
										<td  class="font-size-11">{!! @$product->product_text !!} </td>
										<td class="font-size-11"> {!! @$product->qty ? number_format( @$product->qty,2, ',', ' ') : '' !!} </td>
										<td  class="font-size-11"></td>
										<td class="font-size-11"></td>
										<td class="font-size-11"></td>
										<td class="font-size-11"></td>
									</tr>
								@endif
							@endforeach
						</tbody>
					</table>
				@endif
			</div>
		</div>
	</body>
</html>


