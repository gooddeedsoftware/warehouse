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
	<br><br>
	<div class="container">
		<div class="row">
		 	<div class="col-6">
		 		<div class="row">
					<div class="col-5 offset-1">
						<div class="text-left">
							<img src="{!! URL::to('/') !!}/images/maskinstyring_report_logo.png" alt="" style="margin-top: -22px"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-5 offset-1">
						<h4>
							<b style="font-size: 150%">
					 			{!! trans('main.return_order') !!}
					 		</b>
					 	</h4>
					 </div>
				</div>
				<div class="clearfix my-4"></div>
				<div class="row">
					<div class="col-5 offset-4">
						<p>{!! @$customer_details->name !!}</p>
						<p>
						@if (@$customer_details->departmentCustomerAddress)
							<span class="breakWord">{!! @$customer_details->departmentCustomerAddress[0]->address1 !!}</span><br>
							<span class="breakWord">{!! @$customer_details->departmentCustomerAddress[0]->address2 !!}</span><br>
							<span class="breakWord">{!! @$customer_details->departmentCustomerAddress[0]->zip !!} {!! @$customer_details->departmentCustomerAddress[0]->city !!}</span>
						@endif
						</p>
					</div>
				</div>
				<div class="clearfix my-4"></div>
				<div class="row">
					<div class="col-3 offset-1">
						<strong>{!! trans('main.arbsted') !!}:</strong>
					</div>
					<div class="col-6">
					</div>
				</div>
				<div class="row">
					<div class="col-3 offset-1">
						<strong>{!! trans('main.contact') !!}:</strong>
					</div>
					<div class="col-6">
						{!! @$contact_persons !!}
					</div>
				</div>
				<div class="row">
					<div class="col-3 offset-1">
						<strong>{!! trans('main.delivery_to') !!}:</strong>
					</div>
					<div class="col-6">
						<span class="breakWord">{!! @$orders->deliveraddress1 !!}</span><br>
						<span class="breakWord">{!! @$orders->deliveraddress2 !!}</span><br>
						<span class="breakWord">{!! @$orders->deliveraddress_zip !!} {!! @$orders->deliveraddress_city !!}</span>
					</div>
				</div>
		 	</div>
		 	<div class="col-6">
		 		<div class="row">
		 			<div class="col-5">
                    <strong>{!! trans('main.return_order') !!}:</strong>
	                </div>
	                <div class="col-7">
	                    {!! @$warehouse_order_details['warehouse_details']->order_number !!}
	                </div>
				</div>

				<div class="row">
					<div class="col-5">
					<strong>{!! trans('main.order_date') !!}:</strong>
					</div>
					<div class="col-7">
						@if(@$warehouse_order_details['warehouse_details']->order_date)
							<?php echo date('d.m.Y',strtotime(@$warehouse_order_details['warehouse_details']->order_date)); ?>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="col-5">
                    <strong>{!! trans('main.order_number') !!}:</strong>
	                </div>
	                <div class="col-7">
	                    {!! @$orders->order_number !!}
	                </div>
				</div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.order_category') !!}:</strong>
					</div>
					<div class="col-7">
						{!! @$order_category[@$orders->order_category] !!}
					</div>
				</div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.customer_number')!!}:</strong>
					</div>
					<div class="col-7">
						{!! @$customer_details->customer !!}
					</div>
				</div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.delivery_date')!!}:</strong>
					</div>
					<div class="col-7">
						@if(@$orders->date_completed)
							<?php echo date('d.m.Y',strtotime($orders->date_completed)); ?>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.requisition') !!}:</strong>
					</div>
					<div class="col-7">
						{!! @$orders->project_number !!}
					</div>
				</div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.ordered_by')!!}:</strong>
					</div>
					<div class="col-7">
						{!! @$contacts[@$orders->ordered_by] !!}
					</div>
				</div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.equipment')!!}:</strong>
					</div>
					<div class="col-7">
						{!! @$equipments[@$orders->equipment_id] !!}
					</div>
				</div>
				<div class="clearfix my-4"></div>
				<div class="row">
					<div class="col-5">
						<strong>{!! trans('main.othercontact')!!}:</strong>
					</div>
					<div class="col-7">
						{!! @$orders->contact ? @$orders->contact."," : ''  !!}
						<br>
						{!! @$orders->phone ? @$orders->phone."," : ''  !!} {!! @$orders->email !!}
					</div>
				</div>
		 	</div>
	 	</div>
		<div class="row">
			<table class="table" id="warehouse_product_order_table">
                <thead>
                    <tr>
                        <th width="35%">{!! trans('main.product') !!}</th>
                        <th width="15">{!! trans('main.qty') !!}</th>
                        <th width="20%">{!! trans('main.warehouse') !!}</th>
                        <th width="20%">{!! trans('main.location') !!}</th>
                    </tr>
                </thead>
                <tbody id="warehouse_product_order_table_body">
                    @if(@return_order_product_details)
                        @foreach($return_order_product_details as $product)
                            <tr>
                                <td>
                                    {!! @$product_drop_downs[$product->product_id] !!}
                                </td>
                                <td>
                                    {!! number_format(@$product->return_qty,"0", ",", "") !!}
                                </td>
                                <td>
                                    {!! @$warehouses[$product->warehouse] !!}
                                </td>
                                <td>
                                    {!! @$all_locations[$product->location] !!}
                                </td>
                            <tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
		</div>
	</div>
</body>
</html>
