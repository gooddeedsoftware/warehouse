@extends('layouts.layouts')
@section('title',__('main.accplan'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.accplan') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.accplan') !!}</p>
<p>{!!trans('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.accplan') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
		 	{!! Form::open(array('route' => array('main.accplan.search', $query_string), 'id' => 'accplan_search_form')) !!}
				<div class="row">
				 	<div class="col-3 col-sm-6 col-md-8">
					 	<div class="form-group">
		                    <a class="btn btn-primary" href="{!! route('main.accplan.create') !!}" >
		                    	<i class="d-block d-sm-none fa fa-plus"></i>
		                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.accplan')) !!} </div>
		                    </a>

		                     <a class="btn btn-primary syncUNIData" href="{!! route('main.accplan.syncUNIAccounts') !!}" >
		                    	<i class="d-block d-sm-none fa fa-sync"></i>
		                    	<div class="d-none d-sm-block">{!! strtolower(__('main.sync')) !!} </div>
		                    </a>

		                </div>
					</div>
					<div class="col-9 col-sm-6 col-md-4">
						<div class="form-group input-group">
							{!! Form::text('search', @Session::get('accplan_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.accplan')) )) !!}
							<div class="input-group-append">
								<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="accplan_search_btn"></i></button>
							</div>
						</div>
					</div>
		        </div>
			{!! Form::close() !!}
			<div class='table-responsive'>
			  	<table class="table table-striped table-hover">
	                <thead>
	                    <tr>
							<th>
								<a>@sortablelink('AccountNo', __('main.account_no'))</a>
							</th>
							<th>
								<a>@sortablelink('Name', __('main.name'))</a>
							</th>
							<th class="d-none d-sm-block">
								<a>@sortablelink('AccountGroup', __('main.account_group'))</a>
							</th>
							<th>
								<a>@sortablelink('ResAccount', __('main.res_account'))</a>
							</th>
							<th>
								<a>@sortablelink('TaxCode', __('main.tax_code'))</a>
							</th>
							<th>
								<a>@sortablelink('DefAccount', __('main.def_account'))</a>
							</th>
							<th></th>
						</tr>
	                </thead>

	                <tbody>
	                    @foreach(@$accplans as $accplan) 
	                    <tr>
							<td><a href="{{route('main.accplan.edit',array($accplan->id))}}">{{ @$accplan->AccountNo}}</a></td>
							<td>{{ htmlspecialchars(@$accplan->Name) }}</td>
							<td class="d-none d-sm-block">{{ htmlspecialchars(@$accplan->AccountGroup) }}</td>
							<td>
								@if(@$accplan->ResAccount) 
								 	<i class="fa fa-check" aria-hidden="true"></i>
								@endif
	                        </td>
							<td>
								<div>{{ htmlspecialchars(@$accplan->TaxCode)  }}</div>
							</td>
							<td>
								@if(@$accplan->DefAccount) 
								 	<i class="fa fa-check" aria-hidden="true"></i>
								@endif
	                        </td>
							<td class="delete-td">
								@if (count($accplan->product) == 0)
									<a href="{{ route('main.accplan.destroy', array($accplan->id)) }}"
									data-method="delete"
									data-modal-text="{!!trans('main.deletemessage') !!} {!! strtolower(__('main.accplan')) !!}?" data-csrf="{!! csrf_token() !!}">
									<i class="fas fa-trash-alt"></i>
									</a>
								@endif
							</td>				
						</tr>
						@endforeach
	                </tbody>
	            </table>
			</div>
			@include('common.pagination',array('paginator'=>@$accplans, 'formaction' => 'accplan_search_form'))
		</div>
	</div>
</div> 
@endsection
