@extends('layouts.layouts')
@section('title',__('main.orders'))
@section('header')
	<h3><i class="icon-message"></i>{!!__('main.orders') !!}</h3>
	{!!__('main.orders') !!}
@stop

@section('help')
	<p class="lead">{!!__('main.order') !!}</p>
	<p>{!!__('main.help') !!}</p>
@stop

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header cutomerOrderContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
            	<li class="nav-item">
                	<a class="nav-link" href="{{route('main.offer.index')}}">{!! __('main.offer') !!}</a>
                </li>
            	<li class="nav-item">
            		<a class="nav-link" href="{{route('main.order.orderindex', array(0, @$customer_id, @$equipment_id))}}">{!! __('main.order') !!}</a>
            	</li>
                <li class="nav-item">
                	<a class="nav-link active" href="#">{!! __('main.archived') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
        	@php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            @include('order.archived_filter')
        	<div class="table-responsive">
        		<table class="table table-striped table-hover" id='order_table'>
					<thead class="thead-a-color">
						<tr>
							<th>
								<a >
									<div class="">
										<a>@sortablelink('order_number', __('main.number'))</a>
									</div>

								</a>
							</th>
							<th>
								<a>@sortablelink('customer', __('main.customer'))</a>
							</th>
							<th class=''>
								<a>@sortablelink('order_project_number', __('main.requisition_short'))</a>
							</th>

							<th class=''>
								<a>@sortablelink('title', __('main.equipment'))</a>
							</th>

							<th class=''>
								<a>@sortablelink('order_date', __('main.ordered'))</a>
							</th>

							<th class=''>
								<a>@sortablelink('date_completed', __('main.delivery'))</a>
							</th>

							<th class=''>
								<a>@sortablelink('ordercategorysort', __('main.category'))</a>
							</th>
						</tr>
					</thead>
					<tbody>
					@if (@$orders)
						@foreach($orders as $order_detail)
							<tr>
								<td>
									<a href="{{route('main.order.edit', array($order_detail->id)) }}">{{ @$order_detail->order_number}}</a>
								</td>

								<td><a href="{{route('main.customer.edit',array($order_detail->customer_id))}}">{{ @$order_detail->customer_name }}</a></td>

								<td class=''>
									{{htmlspecialchars(@$order_detail->project_number) }}
								</td>

								<td class=''>
									{!! \App\Helpers\GanticHelper::characterLimiter(htmlspecialchars(@$order_detail->equipment_name)) !!}
								</td>

								<td class=''>
									@if($order_detail->order_date != null)
										{!! date('d.m.Y',strtotime($order_detail->order_date)) !!}
									@endif
								</td>

								<td class=''>
									@if($order_detail->date_completed != null)
										{!! date('d.m.Y',strtotime($order_detail->date_completed)) !!}
									@endif
								</td>

								<td class=''>
									{!! @$order_categories[$order_detail->order_category] !!}
								</td>
							</tr>
						@endforeach
					@endif
					</tbody>
				</table>
        	</div>
        	@if (@$orders)
				@include('common.pagination',array('paginator'=>@$orders, 'formaction' => 'archived_order_search_form'))
			@endif
        </div>
    </div>
</div>
@endsection
@section('page_js')
	<script type="text/javascript">
        // dropdown to submit the form and search
        $("#search_by_department, #archived_search_by_order_users").on("change", function () {
            $(".archived_order_search_form").submit();
        });

        $("#search_btn").click(function () {
            if ($("#search_id").val() == '' && $("#archived_search_by_order_users").val()  == '' && $("#search_by_department").val() == '') {
                $("#cleared").val(1);
            } else {
                $("#cleared").val(0);
            }
        });
        if (!$("#accordionExample").is(":visible")) {
        	$("#accordionExample").remove();
        } else {
        	$('.filterOnsm').remove();
        }
	</script>
@endsection
