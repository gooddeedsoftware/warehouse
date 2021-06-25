<!-- Created by aravinth for order report 06.08.2016 -->
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


.margin {
	margin-right: 40px;
	margin-left: 40px;
}

#maskin {
	border-bottom-style: solid;
	border-bottom-color: #ff0000;
}

.table-header {
	background-color: #ddd;
	border-style: solid;
	border-color: #ddd;
}

.div_body_border {
	border-style: solid;
	border-color: #c7c7c7;
}

#equalheight {
	overflow: hidden;
}
p{
	margin-left: 3% !important;
}
.height_115px {
	height: 115px !important;
}
.height_60px {
	height: 60px !important;
}
.table-borderless tbody tr td, .table-borderless tbody tr th, .table-borderless thead tr th,.table-borderless tr td {
	    border: none !important;
}
.smalltext {
	font-size: 12px !important;
}
</style>

<meta name="viewport" content="text/html" charset="UTF-8">
<link rel="stylesheet"	href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script	src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script	src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

{{-- <link type="text/css" rel="stylesheet" href="{{ URL::to('/') }}/css/bootstrap.min.css">
<script type="text/javascript" src="{{ URL::to('/') }}/js/jquery.min.js"></script>
<script type="text/javascript" src="{{ URL::to('/') }}/js/bootstrap.min.js"></script> --}}
</head>
<body>
	@if(@$page_break == '1')
		<div class="container" style="page-break-before:always;">
	@else
		<div class="container">
	@endif
		<div class="row">
			<div class="col-xs-6">
				<!-- report logo -->
				<div class="text-left">
					Gantic AS
				</div>
				<div class="col-xs-12">
					<p>{!! trans('main.material.stitle') !!}</p>
					<br />
					<br />
					<p>{!! @$materialsDetails->customer->name !!}</p>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
