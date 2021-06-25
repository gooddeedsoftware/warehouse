<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
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
	   font-size: 11px !important;
	}
	th {
	   font-size: 10px !important;
	}

	.margin {
		margin-right: 40px;
		margin-left: 40px;
	}

	.div_body_border {
		border-style: solid;
		border-color: #c7c7c7; 
	}

	tr, td, th, tbody, thead, tfoot {
	    page-break-inside: avoid !important;
	}
	.page_break { 
		 page-break-before: always;  
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
	<div class="row">
		<div class="col-6 text-left">
			<img src="{!! URL::to('/') !!}/images/maskinstyring_report_logo.png" alt="" />
		</div>
		<div class="col-6 pull-right">
			<h3><b>{!! trans('main.ccsheet') !!}</b></h3>
		</div>
	</div>
	<div class="row">
		<div class="col-6">
			<label>{!! trans('main.warehouse') !!}: </label> {!! @$warehouse !!}
		</div>
		<div class="col-6">
			<label>{!! trans('main.completed_by') !!}: </label> {!! @$completed_by !!}
		</div>
	</div>

	<div class="row">
		<div class="col-6">
			<label>{!! trans('main.completed_at') !!}: </label> @php echo (@$completed_at ? date('d.m.Y', strtotime(@$completed_at)) : '') @endphp
		</div>
		<div class="col-6">
			<label>{!! trans('main.comments') !!}: </label> {!! @$comments !!} 
		</div>
	</div>
	<hr>
	<div class="clearfix"></div>
	<div>{!! @$currencies !!}</div>
	<div class="clearfix"></div>
	<br>
	@php $count = 0; @endphp 
	@foreach(@$locations as $key => $location_value)
	@if (@$count > 0)	
		<div class="page_break">
		<table class="table" style="page-break-inside: avoid !important;">
	@else
		<div>
		<table class="table">
	@endif
			<thead>
				<tr>
					<th>{!! trans('main.product') !!}</th>
					<th>{!! trans('main.location') !!}</th>
					<th>{!! trans('main.unit') !!}</th>
					<th>{!! trans('main.on_stock') !!}</th>
					<th>{!! trans('main.counted') !!}</th>
					<th>{!! trans('main.curr_iso') !!}</th>
					<th>{!! trans('main.vendor_price') !!}</th>
					<th>{!! trans('main.counted_at') !!}</th>
					<th>{!! trans('main.counted_by') !!}</th>
				</tr>
			</thead>
			<!-- thead end -->
			
			<!-- tbody start -->
			<tbody>
					@php $count++;
						for($i = 0; $i < count(@$location_value); $i++) { 
							$value = @$location_value[$i];
					@endphp
					<tr>
						<td>{!! @$value['product_number'] !!} {!! @$value['description'] !!}</td>
						<td>{!! @$value['location']['name'] !!}</td>
						<td>{!! @$value['unit'] !!}</td>
						<td>{!! Number_format(@$value['on_stock_qty'], 2, ',', '') !!}</td>
						<td>{!! Number_format(@$value['counted_qty'], 2, ',', '') !!}</td>
						<td>{!! @$value['curr_iso'] !!}</td>
						<td>{!! Number_format(@$value['vendor_price'], 2, ',', '') !!}</td>
						@if(@$value['recounted_at'])
							<td>{!! (@$value['recounted_at'] ? date('d.m.Y', strtotime(@$value['recounted_at'])) : '') !!}</td>
							<td>{!! @$value['recounted_user']['first_name'] !!} {!! @$value['recounted_user']['last_name'] !!}</td>
						@else
							<td>{!! (@$value['counted_at'] ? date('d.m.Y', strtotime(@$value['counted_at'])) : '') !!}</td>
							<td>{!! @$value['counted_user']['first_name'] !!} {!! @$value['counted_user']['last_name'] !!}</td>
						@endif
					</tr>
					@php 
						} 
					@endphp
			</tbody>
		</table>
	</div>
	@endforeach
</body>
</html>
