@extends('layouts.layouts')
@section('title', __('main.grossMargin'))
@section('header')
<h3>
    <i class="icon-message"></i> {!! __('main.grossMargin') !!}
</h3>
@stop

@section('help')
<p class="lead">{!! __('main.grossMargin') !!}</p>
<p>{!! __('main.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!! __('main.grossMargin') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.grossMargin.search', $query_string), 'id' => 'grossMargin_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.grossMargin.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower( __('main.grossMargin')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('grossMargin_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower( __('main.grossMargin')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="grossMargin_search_btn"></i></button>
						</div>
					</div>
				</div>
	        </div>
			{!! Form::close() !!}
			<div class="table-responsive">
	            <table class="table table-striped table-hover ">
	                <thead>
	                    <tr>
	                    	<th>
						 		<a>@sortablelink('product_group',  __('main.productGroup'))</a>
						 	</th>
						 	<th>
						 		<a>@sortablelink('supplier',  __('main.supplier'))</a>
						 	</th>
						 	<th>
						 		<a>@sortablelink('gross_margin',  __('main.grossMargin'))</a>
						 	</th>
						 	<th></th>
	                    </tr>
	                </thead>
					<tbody>
						@if (@$grossMargins)
		                    @foreach($grossMargins as $grossMargin)
		                    <tr>
		                    	<td><a href="{{route('main.grossMargin.edit',array($grossMargin->id))}}">{!! htmlspecialchars($grossMargin->group_name) !!}</a></td>
		                    	<td> {{ $grossMargin->supplier_name }}</td>
		                        <td> {{ number_format($grossMargin->gross_margin, 2, ',', '') }}</td>
		                        <td class="delete-td">
									<a href="{{ route('main.grossMargin.destroy', array($grossMargin->id)) }}"
									data-method="delete"
									data-modal-text="{!!trans('main.deletemessage') !!} {!! strtolower( __('main.grossMargin')) !!}?" data-csrf="{!! csrf_token() !!}">
										<i class="fas fa-trash-alt"></i>
									</a>
		                       </td>
		                    </tr>
		                    @endforeach
	                    @endif
					</tbody>
				</table>
			</div>
			@include('common.pagination',array('paginator' => @$grossMargins, 'formaction' => 'grossMargin_search_form'))
		</div>
	</div>
</div> 
@endsection
