@extends('layouts.layouts')
@section('title',trans('main.ccsheet'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.ccsheet') !!}
</h3>
@stop
@section('help')
<p class="lead">{!!trans('main.ccsheet') !!}</p>
<p>{!!trans('main.ccsheet') !!}</p>
@stop

@section('content')
<div class="container warehouseContainer">
    <div class="card ccsheetCard">
        <div class="card-header warehouseContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link " href="{!! route('main.warehousedetails.index') !!}">{!! trans('main.stock') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.warehouseorder.index') !!}">{!! trans('main.whs_orders') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">{!! trans('main.ccsheet') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.product.index') !!}">{!! trans('main.products') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{!! route('main.productpackage.index') !!}">{!! trans('main.productpackage') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{{ url('whs_history') }}">{!! trans('main.history') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
    		@php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
			@include('ccsheet.filter')
        	<div class="table-responsive">
        		<table class="table table-striped table-hover" id='ccsheet_table'>
            	    <thead>
	                    <tr>
							<th>
								<a>@sortablelink('created_at', trans('main.date'))</a>
							</th>

							<th>
								<a>@sortablelink('warehouse', trans('main.warehouse'))</a>
							</th>

							<th>
								<a>@sortablelink('status', trans('main.status'))</a>
							</th>

							<th>
								<a>@sortablelink('total', trans('main.total'))</a>
							</th>

							<th>
								<a>@sortablelink('diff', trans('main.diff'))</a>
							</th>
							<th></th>
							<th></th>
							<th></th>
	                    </tr>
                	</thead>
                	<tbody>
                		@foreach($ccsheets as $ccsheet)
                			<tr>
		                		<td>
		                        	<a href="{{route('main.ccsheet.ccsheetDetails',array($ccsheet->id))}}">{!! @$ccsheet->created_date !!}</a>
		                    	</td>
	                    	 	<td>
		                        	{!! htmlspecialchars(@$warehouses[@$ccsheet->whs_id]) !!}
	                        	</td>

		                       	<td>
		                       		{!! htmlspecialchars(@$ccsheet_status[@$ccsheet->status]) !!}
		                       	</td>

		                       	<td>
		                       		{!! @$ccsheet->total !!}
		                       	</td>

		                       	<td>
		                       		{!! @$ccsheet->diff !!}
		                       	</td>
		                       	<td>
		                       		@if (@$ccsheet->mismatch < 1 && @$ccsheet->status == 5)
	                       				<i class="fas fa-circle" style="color: #1a9e1a"></i>
	                       			@else
	                       				<i class="fas fa-circle" style="color: red"></i>
	                       			@endif
		                       	</td>
		                       	<td>
	                       			<a href="{!! route('main.ccsheet.createCCSheetReport', array(@$ccsheet->id)) !!}"><i class="fa fa-download" aria-hidden="true"></i></a>
		                       	</td>
		                       	<td>
									@if (@$ccsheet->status == 1)
										<a href="{{ route('main.ccsheet.destroy', array($ccsheet->id)) }}" class="delete-icon" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('main.ccsheet')) !!}?" data-csrf="{!! csrf_token() !!}"> <i class="fas fa-trash-alt"></i></a>
									@elseif (@$ccsheet->show_recount && !@$ccsheet->recount_of)
										<a href="{!! route('main.ccsheet.recountCCSheetDetails', array($ccsheet->id)) !!}" > <i class="fas fa-plus"></i></a>
									@elseif (@$ccsheet->show_recount && @$ccsheet->status == 5)
			                       		<a href="{!! route('main.ccsheet.createAdjustmentOrder', array($ccsheet->id, @$ccsheet->whs_id)) !!}" data-id='{!! @$ccsheet->id !!}' class="create_adjustment_order" id='create_adjustment_order'>
			                       		<i class="fas fa-plus"></i>
			                       		</a>
			                       	@endif
		                       	</td>
	                    	</tr>
                    	@endforeach
					</tbody>
                </table>
        	</div>
        	@include('common.pagination',array('paginator'=>@$ccsheets, 'formaction' => 'ccsheet_search_form'))
        </div>
    </div>
</div>
@endsection

@section('page_js')
	<script type="text/javascript">
		window.localStorage.setItem('create_adjustment_order_id', '');
		$(document).ready(function () {
			// dropdown to submit the form and search
			$(".warehouse").on("change", function () {
				$('#warehouse_xs').val($(this).val());
				$("#ccsheet_search_form").submit();
			});

			$("#search_submit").click(function () {
				$('#start_date_xs').val($('#start_date').val());
				$('#end_date_xs').val($('#end_date').val());
				$('#search_id_xs').val($('#search_id').val());
				$("#ccsheet_search_form").submit();
			});
			// start and end daetimepicker
			$('#start_date, #end_date').datetimepicker({
			    format: 'DD.MM.YYYY',
			    locale: 'en-gb'
			});

			// show active tabs
			$('#ccsheet_tabs li a').on('click', function(e) {
				localStorage.setItem('warehouse_activeTab', $(this).attr('data-href'));
				window.location.href = "{{ url('/warehousedetails') }}";
			});

			// store ccsheet id in local storage.
			$(".create_adjustment_order").click(function () {
				window.localStorage.setItem('create_adjustment_order_id', $(this).attr('data-id'));
			});
			if (!$("#accordionExample").is(":visible")) {
	        	$("#accordionExample").remove();
	        } else {
	        	$('.filterOnsm').remove();
	        }
		});
	</script>
@endsection
