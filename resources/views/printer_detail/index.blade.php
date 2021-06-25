@extends('layouts.layouts')
@section('title',__('main.printer_detail'))
@section('header')
<h3><i class="icon-message"></i>{!!__('main.printer_detail') !!}</h3>
@stop

@section('help')
<p class="lead">{!!__('main.printer_detail') !!}</p>
<p>{!!__('main.area.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.printer_detail') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.printer_detail.search', $query_string), 'id' => 'printer_detail_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.printer_detail.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!__('main.add').' '.strtolower(__('main.printer_detail')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('printer_detail_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>__('main.search').' '.strtolower(__('main.printer_detail')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="printer_detail_search_btn"></i></button>
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
								<a>@sortablelink('number', __('main.number'))</a>
							</th>
							<th></th>
						</tr>
	                </thead>
	                <tbody>
	                	@if(count(@$printer_detail) > 0)
		                    @foreach(@$printer_detail as $logistraDetail)
		                    <tr>
								<td>
									<a href="{{route('main.printer_detail.edit',array($logistraDetail->id))}}">
										{{ htmlspecialchars(@$logistraDetail->name) }}
									</a>
								</td>
								<td> 
									{{ @$logistraDetail->number }}
								</td>
								<td>
									<a href="{{ route('main.printer_detail.destroy', array($logistraDetail->id)) }}"
									data-method="delete"
									data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.printer_detail')) !!}?" data-csrf="{!! csrf_token() !!}">
										<i class="fas fa-trash-alt delete-icon"></i>
									</a>
								</td>
							</tr>
							@endforeach
						@endif
	                </tbody>
	            </table>
			</div>
			@include('common.pagination', array('paginator' => @$printer_detail, 'formaction' => 'printer_detail_search_form'))
		</div>
	</div>
</div> 
@endsection
