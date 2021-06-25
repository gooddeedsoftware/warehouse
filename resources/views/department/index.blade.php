@extends('layouts.layouts')
@section('title',__('main.departments'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.department') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.department') !!}</p>
<p>{!!__('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.department') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.department.search', $query_string), 'id' => 'department_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.department.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!__('main.add').' '.strtolower(__('main.department')) !!} </div>
	                    </a>

	                    <a class="btn btn-primary syncUNIData" href="{!! route('main.department.syncDepartment') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!! __('main.sync') !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('department_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>__('main.search').' '.strtolower(__('main.department')) )) !!}
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
								<a>@sortablelink('Name', __('main.name'))</a>
							</th>
							<th class="d-none d-sm-block">
								<a>@sortablelink('Nbr', __('main.nbr'))</a>
							</th>
							<th></th>
						</tr>
	                </thead>
	                <tbody>
	                	@if(count(@$departments) > 0)
		                    @foreach(@$departments as $department)
		                    <tr>
								<td>
									<a href="{{route('main.department.edit',array($department->id))}}">
										{{ htmlspecialchars(@$department->Name) }}
									</a>
								</td>
								<td class="d-none d-sm-block">{{ @$department->Nbr}}</td>			
								<td class="delete-td">
									@if(count($department->user) == 0)
										<a href="{{ route('main.department.destroy', array($department->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.department')) !!}?" data-csrf="{!! csrf_token() !!}">
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
			@include('common.pagination', array('paginator' => @$departments, 'formaction' => 'department_search_form'))
		</div>
	</div>
</div> 
@endsection
