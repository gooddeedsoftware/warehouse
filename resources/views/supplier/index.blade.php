@extends('layouts.layouts')
@section('title',__('main.suppliers'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.suppliers') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.suppliers') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.suppliers') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.supplier.search', $query_string), 'id' => 'supplier_search_form')) !!}
			<div class="row">
			 	<div class="col-2 col-sm-4 col-md-6">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.supplier.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.supplier')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-10 col-sm-8 col-md-2">
		 			{!! Form::select('status',array('0' =>__('main.active'), '1' => __('main.inactive')), @Session::get('supplier_search')['status'], array('class'=>'form-control','id'=>'status','placeholder'=>trans('main.selected')))!!}
		 		</div>
				<div class="col-12 col-sm-12 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('supplier_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.supplier')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="supplier_search_btn"></i></button>
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

							<th><a>@sortablelink('customer', __('main.supplier_no'))</a></th>

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
	                    @foreach($suppliers as $supplier)
	                    <tr>
							<td>
								<a href="{{route('main.supplier.edit',array($supplier->id))}}"> {{ $supplier->name}} </a>
							</td>
							<td>{{ $supplier->customer}}</td>
							<td>
								<a href="mailto:{{htmlspecialchars(@$supplier->email)  }}">
									@if ($supplier->email != '')
										<i class="fa fa-envelope-o d-none d-sm-none"></i>
									@endif
									<div class="d-none d-sm-block">{{ htmlspecialchars(@$supplier->email)  }}</div>
	                        	</a>
							</td>
							<td>{!! htmlspecialchars(@$supplier->department_address) !!}</td>
							<td>{!!htmlspecialchars( @$supplier->department_zip)  !!}</td>
							<td >{!! htmlspecialchars(@$supplier->department_city)  !!}</td>
							<td>
								<a class="mobilesOnly" href="tel:{{ htmlspecialchars(@$supplier->phone)  }}">
									@if ($supplier->phone != '')
										<i class="fa fa-phone d-none d-sm-none"></i>
									@endif
									<div class="d-none d-sm-block">{{ htmlspecialchars($supplier->phone) }}</div>
								</a>
							</td>
							<td>{{ htmlspecialchars(@$supplier->pmt_terms)  }}</td>
						</tr>
						@endforeach
	                </tbody>
	            </table>
			</div>
			@include('common.pagination', array('paginator' => @$suppliers, 'formaction' => 'supplier_search_form'))
		</div>
	</div>
</div> 
@endsection
@section('page_js')
	<script type="text/javascript">
		$(document).ready(function () {
			$("#status").on("change", function () {
				$("#supplier_search_form").submit();
			});
		});
		$("#bb").next('a').attr('title', "{!! trans('main.invoice.pmt_terms') !!}");
	</script>
@endsection
