@extends('layouts.layouts')
@section('title',__('main.logistraDetails'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.logistraDetails') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.logistraDetails') !!}</p>
<p>{!!__('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.logistraDetails') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.logistraDetails.search', $query_string), 'id' => 'logistraDetails_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.logistraDetails.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!__('main.add').' '.strtolower(__('main.logistraDetails')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('logistraDetails_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>__('main.search').' '.strtolower(__('main.logistraDetails')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="logistraDetails_search_btn"></i></button>
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
								<a>@sortablelink('cargonizer_key', __('main.cargonizer_key'))</a>
							</th>
							<th>
								<a>@sortablelink('cargonizer_sender', __('main.cargonizer_sender'))</a>
							</th>
							<th>
								<a>@sortablelink('status', __('main.status'))</a>
							</th>
							<th></th>
						</tr>
	                </thead>
	                <tbody>
	                	@if(count(@$logistraDetails) > 0)
		                    @foreach(@$logistraDetails as $logistraDetail)
		                    <tr>
								<td>
									<a href="{{route('main.logistraDetails.edit',array($logistraDetail->id))}}">
										{{ htmlspecialchars(@$logistraDetail->name) }}
									</a>
								</td>
								<td> 
									{{ @$logistraDetail->cargonizer_key }}
								</td>
								<td> 
									{{ @$logistraDetail->cargonizer_sender }}
								</td>
								<td> 
									@if (@$logistraDetail->status == 1)
										{{ __('main.active') }}
									@else
										{{ __('main.inactive') }}
									@endif
								</td>

								<td>
									<a href="{{ route('main.logistraDetails.destroy', array($logistraDetail->id)) }}"
									data-method="delete"
									data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.logistraDetails')) !!}?" data-csrf="{!! csrf_token() !!}">
										<i class="fas fa-trash-alt delete-icon"></i>
									</a>
								</td>
							</tr>
							@endforeach
						@endif
	                </tbody>
	            </table>
			</div>
			@include('common.pagination', array('paginator' => @$logistraDetails, 'formaction' => 'logistraDetails_search_form'))
		</div>
	</div>
</div> 
@endsection
