@extends('layouts.layouts')
@section('title',trans('permission_group.group.title'))
@section('header')
<h3><i class="icon-message"></i>{!!trans('permission_group.group.title') !!}</h3>
@stop

@section('help')
<p class="lead">{!!trans('permission_group.group.title') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop
@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!trans('permission_group.group.title') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.group.search', $query_string), 'id' => 'department_search_form')) !!}
			<div class="row">
			 	<div class="col-xs-12 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.group.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.trans('permission_group.group.title') !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('group_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(trans('permission_group.group.title')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="group_search_btn"></i></button>
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
								<a>@sortablelink('group', trans('permission_group.group.title'))</a>
							</th>
							<th>
								<a>@sortablelink('module', trans('permission_group.modules'))</a>
							</th>
							@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
								<th></th>
							@endif
						</tr>
	                </thead>
	                <tbody>
		                @if(@$groups)
		                    @foreach (@$groups as $group)
		                    <tr>
								<td>
									@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
										<a href="{{route('main.group.edit',array($group->id))}}">{{ htmlspecialchars(@$group->group)}}</a>
									@else
										{{ htmlspecialchars(@$group->group)}}
									@endif
								</td>

								<td>
									{{ htmlspecialchars(@$modules[@$group->module]) }}
								</td>
								@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
									<td class="delete-td">
										<a href="{{ route('main.group.destroy', array($group->id)) }}"
										data-method="delete"
										data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('permission_group.group.title')) !!}?" data-csrf="{!! csrf_token() !!}">
										<i class="fas fa-trash-alt"></i>
										</a>
									</td>
								@endif
							</tr>
							@endforeach
						@endif
	                </tbody>
	            </table>
			</div>
			@include('common.pagination',array('paginator'=>@$groups, 'formaction' => 'group_search_form'))
		</div>
	</div>
</div>
@endsection
