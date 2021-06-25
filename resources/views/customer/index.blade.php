@extends('layouts.layouts')
@section('title',__('main.customers'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.customer') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.customer') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.customer') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.customer.search', $query_string), 'id' => 'customer_search_form')) !!}
			<div class="row">
			 	<div class="col-2 col-sm-4 col-md-6">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.customer.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.customer')) !!} </div>
	                    </a>

                      	<a class="btn btn-primary syncUNIData" href="{!! route('main.customer.syncCustomers') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!! __('main.sync') !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-10 col-sm-8 col-md-2">
		 			{!! Form::select('status',array('0' =>__('main.active'), '1' => __('main.inactive')), @Session::get('customer_search')['status'], array('class'=>'form-control','id'=>'status','placeholder'=>trans('main.selected')))!!}
		 		</div>
				<div class="col-12 col-sm-12 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('customer_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.customer')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="customer_search_btn"></i></button>
						</div>
					</div>
				</div>
	        </div>
			{!! Form::close() !!}
			<div class="table-responsive">
             	<table class="table table-striped table-hover">
	                <thead class="thead-a-color">
	                    <tr>
	                    	<th><a>@sortablelink('name', __('main.name'))</a></th>

							<th><a>@sortablelink('customer', __('main.customer_no'))</a></th>

							<th><a> @sortablelink('email', __('main.email'))</a></th>

							<th><a>@sortablelink('departmentAddress', __('main.address'))</a></th>

							<th><a>@sortablelink('zip', __('main.zip'))</a></th>

							<th><a>@sortablelink('zip', __('main.city'))</a></th>

							<th><a>@sortablelink('phone', __('main.phone_short'))</a></th>
							<th>
								<a id='bb'>@sortablelink('bb', __('main.pmt_terms_short'))</a>
							</th>
						</tr>
	                </thead>

	                <tbody>
	                    @foreach($customers as $customer)
	                    <tr>
							<td>
								<a href="{{route('main.customer.edit',array($customer->id))}}"> {{ $customer->name}} </a>
							</td>
							<td>{{ $customer->customer}}</td>
							<td>
								<a href="mailto:{{htmlspecialchars(@$customer->email)  }}">
									@if ($customer->email != '')
										<i class="fa fa-envelope-o d-none d-sm-none"></i>
									@endif
									<div class="d-none d-sm-block">{{ htmlspecialchars(@$customer->email)  }}</div>
	                        	</a>
							</td>
							<td>{!! htmlspecialchars(@$customer->department_address) !!}</td>
							<td>{!!htmlspecialchars( @$customer->department_zip)  !!}</td>
							<td >{!! htmlspecialchars(@$customer->department_city)  !!}</td>
							<td>
								<a class="mobilesOnly" href="tel:{{ htmlspecialchars(@$customer->phone)  }}">
									@if ($customer->phone != '')
										<i class="fa fa-phone d-none d-sm-none"></i>
									@endif
									<div class="d-none d-sm-block">{{ htmlspecialchars($customer->phone) }}</div>
								</a>
							</td>
							<td>{{ htmlspecialchars(@$customer->pmt_terms)  }}</td>
						</tr>
						@endforeach
	                </tbody>
	            </table>
			</div>
			@include('common.pagination', array('paginator' => @$customers, 'formaction' => 'customer_search_form'))
		</div>
	</div>
</div> 
@endsection
@section('page_js')
	<script type="text/javascript">
		$(document).ready(function () {
			$("#status").on("change", function () {
				$("#customer_search_form").submit();
			});
		});
		$("#bb").next('a').attr('title', "{!! trans('main.invoice.pmt_terms') !!}");
	</script>
@endsection
