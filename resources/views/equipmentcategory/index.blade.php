@extends('layouts.layouts')
@section('title', __('main.equipmentcategory'))
@section('header')
<h3>
    <i class="icon-message"></i> {!! __('main.equipmentcategory') !!}
</h3>
@stop

@section('help')
<p class="lead">{!! __('main.equipmentcategory') !!}</p>
<p>{!! __('main.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!! __('main.equipmentcategory') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.equipmentcategory.search', $query_string), 'id' => 'equipmentcategory_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.equipmentcategory.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower( __('main.equipmentcategory')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('equipmentcategory_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower( __('main.equipmentcategory')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="equipmentcategory_search_btn"></i></button>
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
						 		<a>@sortablelink('type',  __('main.equipmentcategory'))</a>
						 	</th>
	                     	<th> </th>
	                    </tr>
	                </thead>
					<tbody>
					@if (count(@$equipmentcategories) > 0)
	                    @foreach($equipmentcategories as $equipmentcategory)
	                    <tr>
	                        <td><a href="{{route('main.equipmentcategory.edit',array($equipmentcategory->id))}}">{!! htmlspecialchars($equipmentcategory->type) !!}</a></td>
							<td class="delete-td">
								@if (count($equipmentcategory->equipment) == 0)
									<a href="{{ route('main.equipmentcategory.destroy', array($equipmentcategory->id)) }}"
									data-method="delete"
									data-modal-text="{!!trans('main.deletemessage') !!} {!! strtolower( __('main.equipmentcategory')) !!}?" data-csrf="{!! csrf_token() !!}">
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
			@include('common.pagination',array('paginator'=>@$equipmentcategories, 'formaction' => 'equipmentcategory_search_form'))
		</div>
	</div>
</div> 
@endsection
