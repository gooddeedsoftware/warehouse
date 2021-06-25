@extends('layouts.layouts')
@section('title',__('main.order'))
@section('header')
	<h3><i class="icon-message"></i>{!!__('main.order') !!}</h3>
	{!!__('main.order') !!}
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
                    <a class="nav-link active" href="#">{!! __('main.order') !!}</a>
                </li>
                
                <li class="nav-item">
                	<a class="nav-link" href="{{route('main.order.orderindex', array(2, @$customer_id, @$equipment_id))}}">{!! __('main.archived') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
        	@php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
			@include('order.order_filter')
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
							<th >
								<a>@sortablelink('order_project_number', __('main.requisition_short'))</a>
							</th>

							<th >
								<a>@sortablelink('title', __('main.equipment'))</a>
							</th>

							<th >
								<a>@sortablelink('order_date', __('main.ordered'))</a>
							</th>

							<th >
								<a>@sortablelink('date_completed', __('main.delivery'))</a>
							</th>

							<th >
								<a>@sortablelink('ordercategorysort', __('main.category'))</a>
							</th>

							<th>
								<a>@sortablelink('status', __('main.status'))</a>
							</th>

							<th>
								<a>@sortablelink('is_rest_order', __('main.is_rest_order'))</a>
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

								<td>
									{{htmlspecialchars(@$order_detail->project_number) }}
								</td>

								<td>
									{!! \App\Helpers\GanticHelper::characterLimiter(htmlspecialchars(@$order_detail->equipment_name)) !!}
								</td>

								<td>
									@if($order_detail->order_date != null)
										{!! date('d.m.Y',strtotime($order_detail->order_date)) !!}
									@endif
								</td>

								<td>
									@if($order_detail->date_completed != null)
										{!! date('d.m.Y',strtotime($order_detail->date_completed)) !!}
									@endif
								</td>

								<td>
									{!! @$order_categories[$order_detail->order_category] !!}
								</td>

								<td>{{ @$order_status[@$order_detail->status] }}</td>

								<td>
									@if(@$order_detail->is_res_order == 1) 
								 		<i class="fa fa-check" aria-hidden="true"></i>
									@endif
								</td>
							</tr>
						@endforeach
					@endif
					</tbody>
				</table>
        	</div>
        	@if (@$orders)
				@include('common.pagination',array('paginator'=>@$orders, 'formaction' => 'order_search_form'))
			@endif
        </div>
    </div>
</div>

@endsection
@section('page_js')
	<script type="text/javascript">
        $("#search_by_order_users, #search_status, .search_by_department").on("change", function () {
            $(".order_search_form").submit();
        });

        if (!$("#accordionExample").is(":visible")) {
        	$("#accordionExample").remove();
        } else {
        	$('.filterOnsm').remove();
        }
	</script>
@endsection
