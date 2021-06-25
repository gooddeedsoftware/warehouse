@extends('layouts.layouts')
@section('title',__('main.location'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.location') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.location') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.location') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
		 	{!! Form::open(array('route' => array('main.location.search', $query_string), 'id' => 'location_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
			 		@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
					 	<div class="form-group">
		                    <a class="btn btn-primary" href="{!! route('main.location.create') !!}" >
		                    	<i class="d-block d-sm-none fa fa-plus"></i>
		                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.location')) !!} </div>
		                    </a>
		                </div>
	                @endif
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('location_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.location')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
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
								<a>@sortablelink('name', __('main.name'))</a>
							</th>
							<th>
								<a>@sortablelink('warehouse', __('main.warehouse'))</a>
							</th>
							<th>
								<a>@sortablelink('scrap_location', __('main.scrap_location'))</a>
							</th>
							<th>
								<a>@sortablelink('return_location', __('main.return_location'))</a>
							</th>
							@if(Session::get('usertype') == "Admin"  || Session::get('usertype') == "Administrative")
								<th></th>
							@endif
						</tr>
	                </thead>
	                <tbody>
		                @if(@$locations) 
		                    @foreach (@$locations as $location) 
		                    <tr>
								<td>
									@if(Session::get('usertype') == "Admin"  || Session::get('usertype') == "Administrative")
										<a href="{{route('main.location.edit',array($location->id))}}">{{ @$location->name}}</a>
									@else 
										{{ htmlspecialchars(@$location->name)}}
									@endif
								</td>
								<td >{{ htmlspecialchars(@$location->shortname) }}</td>
								<td >{{ @$yesorno_language_array[@$location->scrap_location] }}</td>
								<td >{{ @$yesorno_language_array[@$location->return_location] }}</td>
								<td class="delete-td">
									@if(count($location->whsInventory) == 0 && (Session::get('usertype') == "Admin"  || Session::get('usertype') == "Administrative"))
										<a href="{{ route('main.location.destroy', array($location->id)) }}"
										data-method="delete"
										data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.location')) !!}?" data-csrf="{!! csrf_token() !!}">
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
			@include('common.pagination', array('paginator' => @$locations, 'formaction' => 'location_search_form'))
		</div>
	</div>
</div> 
@endsection
