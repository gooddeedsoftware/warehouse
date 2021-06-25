@extends('layouts.layouts')
@section('title', __('main.productGroup'))
@section('header')
<h3>
    <i class="icon-message"></i> {!! __('main.productGroup') !!}
</h3>
@stop

@section('help')
<p class="lead">{!! __('main.productGroup') !!}</p>
<p>{!! __('main.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!! __('main.productGroup') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.productGroup.search', $query_string), 'id' => 'productGroup_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.productGroup.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower( __('main.productGroup')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('productGroup_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower( __('main.productGroup')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="productGroup_search_btn"></i></button>
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
						 		<a>@sortablelink('number',  __('main.number'))</a>
						 	</th>
						 	<th>
						 		<a>@sortablelink('name',  __('main.name'))</a>
						 	</th>
	                     	<th> </th>
	                    </tr>
	                </thead>
					<tbody>
					@if (@$productGroups)
	                    @foreach($productGroups as $productGroup)
	                    <tr>
	                        <td><a href="{{route('main.productGroup.edit',array($productGroup->id))}}">{!! htmlspecialchars($productGroup->number) !!}</a></td>
	                        <td>{!! htmlspecialchars($productGroup->name) !!}</td>
							<td class="delete-td">
								<a href="{{ route('main.productGroup.destroy', array($productGroup->id)) }}"
								data-method="delete"
								data-modal-text="{!!trans('main.deletemessage') !!} {!! strtolower( __('main.productGroup')) !!}?" data-csrf="{!! csrf_token() !!}">
									<i class="fas fa-trash-alt"></i>
								</a>
	                       </td>
	                    </tr>
	                    @endforeach
                    @endif
					</tbody>
				</table>
			</div>
			@include('common.pagination',array('paginator' => @$productGroups, 'formaction' => 'productGroup_search_form'))
		</div>
	</div>
</div> 
@endsection
