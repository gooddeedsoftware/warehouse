@extends('layouts.layouts')
@section('title',  __('main.activities'))
@section('header')
<h3><i class="icon-message"></i>{!!  __('main.activity') !!}</h3>
@stop

@section('help')
<p class="lead">{!!  __('main.activity') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!  __('main.activities') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
		 	{!! Form::open(array('route' => array('main.activities.search', $query_string), 'id' => 'activities_search_form')) !!}
			<div class="row">
			 	<div class="col-2 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.activities.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(  __('main.activity')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-10 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('activities_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.  strtolower(__('main.activity')) )) !!}
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
								<a>@sortablelink('ltkode',   __('main.ltkode'))</a>
							</th>
							<th >
								<a>@sortablelink('department', __('main.department'))</a>
							</th>
							<th >
								<a>@sortablelink('account', __('main.account_no'))</a>
							</th>
							<th >
								<a>@sortablelink('unit',   __('main.unit'))</a>
							</th>
							<th>
								<a>@sortablelink('price',   __('main.price'))</a>
							</th>
							<th >
								<a>@sortablelink('description',   __('main.description'))</a>
							</th>
							<th >
								<a>@sortablelink('VAT',   __('main.vat')) </a>
							</th>
							<th></th>
						</tr>
	                </thead>

	                <tbody>
	                    @foreach(@$activities as $activity) 
	                    <tr>
							<td>
								<a href="{{route('main.activities.edit',array($activity->id))}}">{{ @$activity->ltkode}}</a>
							</td>
							<td >{{ htmlspecialchars(@$activity->department->Name) }}</td>
							<td >{{ htmlspecialchars(@$activity->acc_plan->AccountNo) }}</td>
							<td >{{ htmlspecialchars(@$activity->unit) }}</td>
							<td>
								{{ htmlspecialchars(@$activity->price)  }}
	                        </td>
							<td >
								<div >{{ htmlspecialchars(@$activity->description)  }}</div>
							</td>
							<td >{{ htmlspecialchars(@$activity->VAT) }}</td>
							<td class="delete-td">
								<a href="{{ route('main.activities.destroy', array($activity->id)) }}"
								data-method="delete"
								data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(  __('main.activity')) !!}?" data-csrf="{!! csrf_token() !!}">
								<i class="fas fa-trash-alt"></i>
								</a>
							</td>				
						</tr>
						@endforeach
	                </tbody>
	            </table>
			</div>
			@include('common.pagination',array('paginator'=>@$activities, 'formaction' => 'activities_search_form'))
		</div>
	</div>
</div> 
@endsection
