@extends('layouts.layouts')
@section('title',__('main.warehouse'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!__('main.warehouse') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!__('main.warehouse') !!}</p>
<p>{!!__('main.warehouse.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.warehouse') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
		 	{!! Form::open(array('route' => array('main.warehouse.search', $query_string), 'id' => 'warehouse_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
			 		@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
					 	<div class="form-group">
		                    <a class="btn btn-primary" href="{!! route('main.warehouse.create') !!}" >
		                    	<i class="d-block d-sm-none fa fa-plus"></i>
		                    	<div class="d-none d-sm-block">{!!__('main.add').' '.strtolower(__('main.warehouse')) !!} </div>
		                    </a>
		                </div>
	                @endif
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('warehouse_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>__('main.search').' '.strtolower(__('main.warehouse')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="warehouse_search_btn"></i></button>
						</div>
					</div>
				</div>
	        </div>
			{!! Form::close() !!}
			<div class="table-responsive">
             	<table class="table table-striped table-hover">
	                <thead>
	                    <tr>
							<th>
								<a>@sortablelink('shortname', __('main.name'))</a>
							</th>
							<th>
								<a>@sortablelink('main', __('main.main'))</a>
							</th>
							<th>
								<a>{!! __('main.responsible') !!}</a>
							</th>
							@if (Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
								<th></th>
							@endif
						</tr>
	                </thead>
	                <tbody>
		                @if (@$warehouses) 
		                    @foreach (@$warehouses as $warehouse) 
		                    <tr>
								<td>
									@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
										<a href="{{route('main.warehouse.edit',array($warehouse->id))}}">{{ @$warehouse->shortname}}</a>
									@else 
										{{ htmlspecialchars(@$warehouse->shortname) }}
									@endif
								</td>
								<td>{{ htmlspecialchars(@$warehousemain_array[@$warehouse->main]) }}</td>
								<td>
									@foreach (@$warehouse->warehouseResponsible as $responsible)
										{!! htmlspecialchars(@$users[@$responsible->user_id]) !!}<br>
									@endforeach
								</td>
								<td class="delete-td">
									@if(count($warehouse->whsInventory) == 0 && count($warehouse->location) == 0 && (Session::get('usertype') == "Admin"  || Session::get('usertype') == "Administrative"))
										<a href="{{ route('main.warehouse.destroy', array($warehouse->id)) }}"
										data-method="delete"
										data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.warehouse')) !!}?" data-csrf="{!! csrf_token() !!}">
											<i class="fas fa-trash-alt"></i>
										</a>
									@endif			
								</td>	
							</tr>
							@endforeach
						@endif
	                </tbody>
	            </table>
			</div>
			@include('common.pagination',array('paginator'=>@$warehouses, 'formaction' => 'warehouse_search_form'))
		</div>
	</div>
</div> 
@endsection
