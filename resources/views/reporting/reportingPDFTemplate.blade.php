<!-- Created by aravinth for reporting 15.06.2017 -->
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
<style>
/* body {
   font-family: Arial, 'sans-serif'  !important;
} */
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

table, tr, td, th, tbody, thead, tfoot {
    page-break-inside: avoid !important;
}
</style>

<meta name="viewport" content="text/html" charset="UTF-8">
<!-- <link rel="stylesheet"	href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script	src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script	src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->

<link type="text/css" rel="stylesheet" href="{{ URL::to('/') }}/css/bootstrap.min.css">
<script type="text/javascript" src="{{ URL::to('/') }}/js/jquery.min.js"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		
		
		<div class="clearfix"></div>

		<div class="row">
			<div class="col-xs-6">
		 		<div class="text-left">
					<!--<h1>Need to place image here</h1> -->
					<img src="{!! URL::to('/') !!}/images/maskinstyring_report_logo.png" alt="" />

				</div>
			</div>
			<div class="col-xs-6">
				<h1>{!! trans('main.reporting.report_uninvoiced_hours_to') !!} {!! @$date!!}</h1>
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="row">
			<div class="col-xs-6">
				{!! Form::label('department', trans('main.reporting.department'), ['class' => 'control-label']) !!} : {!! @$data[0]->avdeling !!}
				
			</div>

			<div class="col-xs-6">
				{!! Form::label('to_date', trans('main.reporting.to_date'), ['class' => 'control-label']) !!} : {!! @$to_date !!}
			</div>
		</div>


		<div class="clearfix"></div>
		<br><br>
		
		<table class="table table-striped">	
			<thead>
				<tr>
				@foreach (@$header_data['headers'] as $key => $value)
					<th>{!! $value!!}</th>
				@endforeach
				</tr>
			</thead>
			<tbody>
				@foreach (@$data as $key => $value)
					<tr>
						
						<td>{!! @$value->Kunde !!}</td>
						<td>{!! @$value->order_number !!}</td>
						<td>{!! @$value->project !!}</td>
						<td>{!! @$value->first_name !!}</td>
						<td>{!! @$value->last_name !!}</td>
						<td>{!! @$value->avdeling !!}</td>
						<td>{!! @$value->reg_type !!}</td>
						<td>{!! @$value->ltkode !!}</td>
						<td>{!! @$value->date !!}</td>
						<td>{!! @$value->hours !!}</td>
						<td>{!! @$value->adj_hours !!}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		
		<div class="clearfix"></div>
		
		<div>
			<hr>
		</div>
		<div>
			<div> 
				<label>{!! trans('main.reporting.total_hours') !!}</label>: {!! @$total_hours['total_hours']['hours'] !!}
			</div>
			<div>
				<label>{!! trans('main.reporting.total_adj_hours') !!}</label>: {!! @$total_hours['total_hours']['adj_hours'] !!}
			</div>
		<div>
			<hr>
		</div>
	</div>
</body>
</html>
