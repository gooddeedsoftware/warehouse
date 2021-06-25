@extends('layouts.layouts')
@section('title',__('main.offer'))
@section('header')
	<h3><i class="icon-message"></i>{!!__('main.offer') !!}</h3>
	{!!__('main.offer') !!}
@stop

@section('help')
	<p class="lead">{!!__('main.offer') !!}</p>
	<p>{!!__('main.help') !!}</p>
@stop
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header cutomerOrderContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
            	<li class="nav-item">
                    <a class="nav-link active" href="#">{!! __('main.offer') !!}</a>
                </li>

                <li class="nav-item">
                	<a class="nav-link" href="{{route('main.order.orderindex', array(0, @$customer_id, @$equipment_id))}}">{!! __('main.order') !!}</a>
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
			@include('offer.offer_filter')
        	<div class="table-responsive">
        		<table class="table table-striped table-hover" id='order_table'>
					<thead class="thead-a-color">
						<tr>
							<th>
								<a >
									<div class="">
										<a>@sortablelink('offer_number', __('main.number'))</a>
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

							<th>
								<a>@sortablelink('status', __('main.status'))</a>
							</th>

							<th>
								<a>@sortablelink('status', __('main.order'))</a>
							</th>
							<th ></th>

						</tr>
					</thead>
					<tbody>
					@if (@$orders)
						@foreach($orders as $order_detail)
							<tr>
								<td>
									<a href="{{route('main.offer.edit', array($order_detail->id)) }}">{{ @$order_detail->offer_number}}</a>
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

								<td>{{ @$offer_status[@$order_detail->status] }}</td>

								<td>
									@if($order_detail->offer_order_id)
										<a href="{{route('main.order.edit', array($order_detail->offer_order_id)) }}">{{ @$order_detail->offer_order_number}}</a>
									@endif
								</td>

								<td>
									<a href="{{route('main.offer.edit', array($order_detail->id)) }}">
										<i class="fa fa-pencil"></i>
									</a>

									@if (@$order_detail->status == 1)
										<a href="{{ route('main.offer.destroy', array($order_detail->id)) }}"
										   data-method="delete"
										   data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.offer')) !!}?" data-csrf="{!! csrf_token() !!}">
											<i class="fa fa-trash delete-icon"></i>
										</a>
									@endif
								</td>
							</tr>
						@endforeach
					@endif
					</tbody>
				</table>
        	</div>
        	@if (@$orders)
				@include('common.pagination',array('paginator'=>@$orders, 'formaction' => 'offer_search_form'))
			@endif
        </div>
    </div>
</div>

@endsection
@section('page_js')
	<script type="text/javascript">
        $("#search_by_order_users, #search_status, .search_by_department").on("change", function () {
            $(".offer_search_form").submit();
        });
        if (!$("#accordionExample").is(":visible")) {
        	$("#accordionExample").remove();
        } else {
        	$('.filterOnsm').remove();
        }
	</script>
@endsection
