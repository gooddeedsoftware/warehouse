@extends('layouts.layouts')
@section('title',trans('main.ccsheet_details'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.ccsheet_details') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.ccsheet_details') !!}</p>
@stop

@section('content')

<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!trans('main.ccsheet_details') !!}</b>
        </div>
        <div class="card-body">
        	<div class="row">
        		<div class="col-md-3 form-group">
        			<input type="text" name="location" class="form-control" id="location" placeholder="{!! trans('main.location') !!}">
        		</div>
        		<div class="col-md-9 form-group">
        			<a class="btn btn-primary disabled" id="start_count" data-val=0 href="#">{!! trans('main.start_count') !!}</a>
        			<a class="btn btn-primary hide-div" id="next_location" href="#">{!! trans('main.next_location') !!}</a>
        		</div>
        	</div>
        	 <div class="hide-div table-responsive" id='product_div'>
        	 	<table class="table" id="product_table">
                    <thead>
                        <tr>
                            <th width="20%">{!! trans('main.product') !!}</th>
                            <th width="30%">{!! trans('main.description') !!}</th>
                            <th width="20%">{!! trans('main.location') !!}</th>
                            <th width="10%">{!! trans('main.qty') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@foreach($scanned_products as $product)
                        	<tr class="{!! @$product->locationDetail->name !!}">
                        		<td>{!! @$product->productDetail->product_number !!}</td>
                        		<td>{!! @$product->productDetail->description !!}</td>
                       			<td><label class="counted_location">{!! @$product->locationDetail->name !!}</label></td>
                       			<td>{!! number_format(@$product->qty, 2, ",", "");  !!}</td>
                        	</tr>
                    	@endforeach
                    </tbody>
                </table>
        	 </div>
        	 <div class="col-l">
    			<a href="{{route('main.ccsheet.ccsheetDetails',array($ccsheet->id))}}" class="btn btn-danger" id="close_scanner_view">{!! trans('main.close') !!}</a>
    			<a href="{{route('main.ccsheet.completeCounting',array($ccsheet->id))}}" class="btn btn-success hide-div" id="complete_counting">{!! trans('main.complete_counting') !!}</a>
	        </div>
        </div>
    </div>
</div>
<a id="open_modal_btn"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#recount" data-toggle="modal" style="visibility:hidden;">Test</a>
<div class="modal fade" id="recount" role="dialog" aria-labelledby="addNewModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header">
			    <h3>{!! trans('main.message') !!}</h3>
			</div>
			<div class="modal-body">
				<h4>{!! trans('main.location_counted_msg') !!}</h4>
				<br />
			    <button type="button" class="btn btn-primary" id="continue_counting" name="continue_counting">{!! trans('main.continue_counting') !!}</button> 
			    <button type="button" class="btn btn-success" id="clear_and_recount" name="clear_and_recount">{!! trans('main.clear_and_recount') !!}</button> 
			    <button type="button" class="btn btn-danger" id="back" name="back">{!! trans('main.back') !!}</button>  
			</div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

@endsection
@section('page_style')
	{!! Html::style('css/jquery.flexdatalist.min.css') !!}
@endsection

@section('page_js')
	{!! Html::script('js/jquery.flexdatalist.min.js') !!}
	<script type="text/javascript">
		var warhouse_id = "{!! @$ccsheet->whs_id !!}";
		var ccsheet_id = "{!! @$ccsheet->id !!}";
		var lcoation_not_found = "{!! trans('main.lcoation_not_found') !!}";
		var product_not_found = "{!! trans('main.product_not_found') !!}";
		var serial_number_required_msg = "{!! trans('main.serial_number_required_msg') !!}";
		var next_prodcut = "{!! trans('main.next_prodcut') !!}";
		var scanned_product_count = "{!! @$scanned_product_count !!}";
		if (scanned_product_count > 0)  {
			$('#product_div').removeClass('hide-div');
			$('#close_scanner_view').addClass('hide-div');
			$('#complete_counting').removeClass('hide-div');
		}
		var i = 0;
	</script>
	{!! Html::script(mix('js/ccsheet.js')) !!}
@endsection
