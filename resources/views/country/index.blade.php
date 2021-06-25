@extends('layouts.layouts')
@section('title',__('main.country'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!__('main.country') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!__('main.country') !!}</p>
<p>{!!__('main.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.country') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.country.search', $query_string), 'id' => 'country_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.country.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.country')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('country_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.country')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="country_search_btn"></i></button>
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
						 		<a>@sortablelink('name', trans('main.name'))</a>
						 	</th>
	                     	<th> </th>
	                    </tr>
	                </thead>
					<tbody>
                    @foreach($countries as $country)
                    <tr>
                        <td><a href="{{route('main.country.edit',array($country->id))}}">{!! htmlspecialchars($country->name) !!}</a></td>
						<td class="delete-td">
							<a href="{{ route('main.country.destroy', array($country->id)) }}"
							data-method="delete"
							data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.country')) !!}?" data-csrf="{!! csrf_token() !!}">
								<i class="fas fa-trash-alt"></i>
							</a>
                       </td>
                    </tr>
                    @endforeach
					</tbody>
				</table>
			</div>
			@include('common.pagination',array('paginator'=>@$countries, 'formaction' => 'country_search_form'))
		</div>
	</div>
</div> 
@endsection
