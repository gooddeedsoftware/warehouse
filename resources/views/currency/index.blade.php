@extends('layouts.layouts')
@section('title',__('main.currency'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!__('main.currency') !!}
</h3>
@stop
@section('help')
<p class="lead">{!!__('main.currency') !!}</p>
<p>{!!__('main.help') !!}</p>
@stop
@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.currency') !!}</b>
		</div>
		<div class="card-body">
			@php
			    $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
			@endphp
			{!! Form::open(array('route' => array('main.currency.search', $query_string), 'id' => 'currency_search_form')) !!}
			<div class="row">
			 	<div class="col-3 col-sm-3 col-md-4">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.currency.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(__('main.currency')) !!} </div>
	                    </a>
	                </div>
				</div>
				<div class="col-9 col-sm-3 col-md-4 text-right">
					<a href="javascript:;" data-url="recalculate_prices" data-method="get" data-modal-text="{{ __('main.confirm_recalculate_price') }}" class="btn btn-primary" data-csrf="{!! csrf_token() !!}"> 
						{{ __('main.recalculate_prices') }}
					</a>
				</div>
				<div class="col-12 col-sm-6 col-md-4">
					<div class="form-group input-group">
						{!! Form::text('search', @Session::get('currency_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.currency')) )) !!}
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="currency_search_btn"></i></button>
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
						 		<a>@sortablelink('curr_iso_name', __('main.currency'))</a>
						 	</th>
						 	<th>
						 		<a>@sortablelink('exch_rate', __('main.exchange_rate'))</a>

						 	</th>
						 	<th>
						 		<a>@sortablelink('valid_from', __('main.valid_from'))</a>
						 	</th>
	                     	<th></th>
	                    </tr>
	                </thead>
					<tbody>
					@if (count(@$currencies) > 0)
	                    @foreach($currencies as $currency)
	                    <tr>
	                        <td><a href="{{route('main.currency.edit',array($currency->id))}}">{{ @$currency_list[$currency->curr_iso_name] }}</a></td>
	                        <td>{!! number_format((float)@$currency->exch_rate,2, ',', ' ') !!}</td>
	                       	<td>
	                       		@if($currency->valid_from != null)
                                    {!! date('d.m.Y',strtotime($currency->valid_from)) !!}
                                @endif
	                       	</td>
							<td class="delete-td">
								<a href="{{ route('main.currency.destroy', array($currency->id)) }}" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.currency')) !!}?" data-csrf="{!! csrf_token() !!}"> 
									<i class="fas fa-trash-alt"></i>
								</a>
	                       	</td>
	                    </tr>
	                    @endforeach
                    @endif
					</tbody>
				</table>
			</div>
			@include('common.pagination',array('paginator'=>@$currencies, 'formaction' => 'currency_search_form'))
		</div>
	</div>
</div> 
@endsection
