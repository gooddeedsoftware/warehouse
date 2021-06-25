@extends('layouts.layouts')
@section('title',__('main.users'))
@section('header')
<h3><i class="icon-message"></i>{!! __('main.user') !!}</h3>
@stop
@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.users') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.user.search', $query_string ), 'id' => 'user_search_form')) !!}
			<div class="row">
			 	<div class="col-6 col-md-6">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.user.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.user')) !!} </div>
	                    </a>
	                    <a class="btn btn-primary syncUNIData" href="{!! route('main.user.syncSellers') !!}" >
	                    	<i class="d-block d-sm-none fa fa-sync"></i>
	                    	<div class="d-none d-sm-block">{!! strtolower(__('main.sync_sellers')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-6 col-md-2 text-align-right">
					@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
					 	{!! Form::checkbox('filter_by_active_user1', '0', '', array("data-toggle" => "toggle",  'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.inactive'), "data-off" => trans('main.all'), "id" => "filter_by_active_user1")) !!}
					 	{!! Form::hidden('filter_by_active_user', @Session::get('user_search')['filter_by_active_user'] ? @Session::get('user_search')['filter_by_active_user'] : '' , array('id'=>'filter_by_active_user')) !!}
					@endif
				</div>
				<div class="col-12 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('user_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.user')) )) !!}
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
								<a>@sortablelink('first_name', __('main.first_name'))</a>
							</th>
							<th>
								<a>@sortablelink('last_name', __('main.last_name'))</a>
							</th>
							<th>
								<a>@sortablelink('email', __('main.email'))</a>
							</th>
							
							<th>
								<a>@sortablelink('phone', __('main.phone'))</a>
							</th>
							<th>
								<a>@sortablelink('department', __('main.department'))</a>
							</th>
							<th>
								<a>@sortablelink('usertype', __('main.usertype'))</a>
							</th>
						</tr>
	                </thead>

	                <tbody>
	                    @foreach($users as $user)
	                    <tr>
							<td>
								@if(Auth::user()->department_id == $user->department_id && Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
									<a href="{{route('main.user.edit',array($user->id))}}">{{ $user->first_name}}</a></td>
								@else
									<label>{{ htmlspecialchars($user->first_name) }}</label>
								@endif
							</td>

							<td>{{ htmlspecialchars($user->last_name) }}</td>
							
							<td>
								<a href="mailto:{{ htmlspecialchars(@$user->email)  }}">
									<i class="fa fa-envelope-o d-block d-sm-none" aria-hidden="true"></i>
									<div class="d-none d-sm-block">{{ htmlspecialchars(@$user->email)  }}</div>
	                            </a>
							</td>
							<td>
								@if(@$user->phone)
									<a class="mobilesOnly" href="tel:{{ @$user->phonenumber }}">
										<i class="fa fa-phone d-block d-sm-none" aria-hidden="true"></i>
										<div class="d-none d-sm-block">{{ @$user->phone }}</div>
									</a>
								@endif
							</td>
							<td>{{ htmlspecialchars(@$user->department_name) }}</td>
							<td>{{ @$usertypes[$user->user_type_name]}}</td>						
						</tr>
						@endforeach
	                </tbody>
	            </table>
			</div>
			@include('common.pagination', array('paginator' => @$users, 'formaction' => 'user_search_form'))
		</div>
	</div>
</div> 
@endsection
@section('page_js')
	<script type="text/javascript">
		var filter_by_active_user = "{!! @Session::get('user_search')['filter_by_active_user'] !!}";
	</script>
	{!! Html::script(mix("js/user.js")) !!}
@endsection