@extends('layouts.layouts')
@section('title',trans('main.product'))
@section('header')
    <h3>
        <i class="icon-message"></i>{!!trans('main.product') !!}
    </h3>
@stop

@section('help')
    <p class="lead">{!!trans('main.product') !!}</p>
    <p>{!!trans('main.product') !!}</p>
@stop

@section('content')
	<div class='container'>
		<div class="card">
			<div class="card-header">
				@if (@$product->id)
					<b>{!! @$product['product_number'] !!} </b>
				@else
					<b>{!! trans('main.product') !!}</b>
				@endif
			</div>
			@if (@$product->id)
	  			{!! Form::open(array('route' => array('main.product.update',$product->id),'method'=>'PUT', 'id'=>'productform','name'=>'productform','class' => 'form-horizontal row-border')) !!}
	  		@else
    			{!! Form::open( array('route' => 'main.product.store','id'=>'productform','name'=>'productform','class'=>'form-horizontal', 'id'=>'productform') ) !!}
    		@endif
				<div class="card-body">
					<div class="row" id="product_div">
		                <div class="col-md-6">
						    <div class="form-group row">
					      		{!! Form::label('nobb', trans('main.nobb'), array('class' => 'col-md-4 col-form-label text-md-right ')) !!}
					      		<div class="col-md-6">
					      			{!! Form::text('nobb',@$product['nobb'],array('class'=>'form-control','id'=>'nobb', 'maxlength' => 20 )) !!}
					      		</div>
						    </div>
						    <div class="form-group row">
					      		{!! Form::label('description', trans('main.description'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
					      		<div class="col-md-6">
					      			{!! Form::text('description',@$product['description'],array('class'=>'form-control','id'=>'description', 'maxlength' => 255, 'required' )) !!}
					      		</div>
						    </div>

						    <div class="form-group row">
						    	{!! Form::label('group',trans('main.productGroup'),array('class'=>'col-md-4 col-form-label text-md-right  custom_required')) !!}
					      		<div class="col-md-6">
					      			{!! Form::select('product_group',@$groups, @$product->product_group,array('class'=>'form-control', 'id' => 'group', 'placeholder'=>trans('main.selected'),'required')) !!}
					      		</div>
						    </div>

						    <div class="form-group row">
						    	{!! Form::label('fk_AccountNo',trans('main.account_no'),array('class'=>'col-md-4 col-form-label text-md-right  custom_required')) !!}
					      		<div class="col-md-6">
					      			{!! Form::select('acc_plan_id',@$accplans, @$product->acc_plan_id,array('class'=>'form-control','placeholder'=>trans('main.selected'),'required')) !!}
					      		</div>
						    </div>


						    <div class="form-group row">
						    	{!! Form::label('unit', trans('main.unit'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
					      		<div class="col-md-6">
					      			{!! Form::select('unit',@$units,@$product->unit,array('class'=>'form-control','placeholder'=>trans('main.selected'),'id'=>'unit','width'=>'100%', 'required')) !!}
					      		</div>
						    </div>

					      	@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
							    <div class="form-group row">
							    	{!! Form::label('vendor_price', trans('main.vendor_price'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
									<div class="col-6 col-sm-6 col-md-3">
										{!! Form::text('vendor_price',@$product['vendor_price'],array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'vendor_price')) !!}
									</div>
									<div class="col-6 col-sm-6 col-md-5">
										{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
									</div>
								</div>
								
						      	<div class="form-group row">
							    	{!! Form::label('cost', trans('main.costs'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						      		<div class="col-6 col-sm-6 col-md-3">
						      			{!! Form::text('costs',@$product->costs,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'cost')) !!}
						      		</div>
						      		<div class="col-6 col-sm-6 col-md-5">
						      			{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
						      		</div>
							    </div>

							     <div class="form-group row">
							    	{!! Form::label('cost', trans('main.cost_factor'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						      		<div class="col-6 col-sm-6 col-md-3">
						      			{!! Form::text('cost_factor',@$product->cost_factor,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'cost_factor')) !!}
						      		</div>
						      		<div class="col-6 col-sm-6 col-md-5">
						      			{!! Form::label('%', '%', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
						      		</div>
							    </div>
							@else 
								<div class="form-group row">
							    	{!! Form::label('cost_price', trans('main.cost_price'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						      		<div class="col-6 col-sm-6 col-md-3">
						      			{!! Form::text('cost_price', @$product->cost_price,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'cost_price')) !!}
						      		</div>
						      		<div class="col-6 col-sm-6 col-md-5">
						      			{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
						      		</div>
							    </div>

						      	<div class="form-group row">
							    	{!! Form::label('profit_percent', trans('main.profit_percent'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						      		<div class="col-6 col-sm-6 col-md-3">
						      			{!! Form::text('profit_percentage',@$product->profit_percentage,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'profit_percent')) !!}
						      		</div>
						      		<div class="col-6 col-sm-6 col-md-5">
						      			{!! Form::label('%', '%', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
						      		</div>
							    </div>
						    @endif
						</div>
						<div class="col-md-6">
						   	@if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
						     	<div class="form-group row">
							    	{!! Form::label('cost_price', trans('main.cost_price'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						      		<div class="col-6 col-sm-6 col-md-3">
						      			{!! Form::text('cost_price', @$product->cost_price,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'cost_price')) !!}
						      		</div>
						      		<div class="col-6 col-sm-6 col-md-5">
						      			{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
						      		</div>
							    </div>

						      	<div class="form-group row">
							    	{!! Form::label('profit_percent', trans('main.profit_percent'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						      		<div class="col-6 col-sm-6 col-md-3">
						      			{!! Form::text('profit_percentage',@$product->profit_percentage,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'profit_percent')) !!}
						      		</div>
						      		<div class="col-6 col-sm-6 col-md-5">
						      			{!! Form::label('%', '%', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
						      		</div>
							    </div>
						    @endif

						    <div class="form-group row">
						    	{!! Form::label('profit', trans('main.profit'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					      		<div class="col-6 col-sm-6 col-md-3">
					      			{!! Form::text('profit', @$product->profit,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'profit')) !!}
					      		</div>
					      		<div class="col-6 col-sm-6 col-md-5">
					      			{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
					      		</div>
						    </div>

						    
						    <div class="form-group row">
						    	{!! Form::label('sale_price', trans('main.sale_price'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					      		<div class="col-6 col-sm-6 col-md-3">
					      			{!! Form::text('sale_price',@$product->sale_price,array('class'=>'form-control numberWithSingleComma setDecimal','maxlength'=>'19','id'=>'sale_price')) !!}
					      		</div>
					      		<div class="col-6 col-sm-6 col-md-5">
					      			{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
					      		</div>
						    </div>

						    <div class="form-group row">
						    	{!! Form::label('tax', trans('main.vat'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					      		<div class="col-6 col-sm-6 col-md-3">
					      			{!! Form::text('tax', @$product->tax ? $product->tax: 25,array('class'=>'form-control','maxlength'=>'7','id'=>'tax')) !!}
					      		</div>
					      		<div class="col-6 col-sm-6 col-md-5">
					      			{!! Form::label('%', '%', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
					      		</div>
						    </div>


						     <div class="form-group row">
						    	{!! Form::label('sale_price_inc_vat', trans('main.sale_price_inc_vat'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					      		<div class="col-6 col-sm-6 col-md-3">
					      			{!! Form::text('sale_price_with_vat_disabled',@$product->sale_price_with_vat,array('class'=>'form-control numberWithSingleComma setDecimal sale_price_inc_vat', 'disabled', 'maxlength'=>'19','id'=>'sale_price_inc_vat')) !!}
					      			<input type='hidden' class="sale_price_inc_vat setDecimal" name="sale_price_with_vat" value="{{ @$product->sale_price_with_vat }}">
					      		</div>
					      		<div class="col-6 col-sm-6 col-md-5">
					      			{!! Form::label('NOK', 'NOK', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
					      		</div>
						    </div>

						      <div class="form-group row">
						    	{!! Form::label('dg', trans('main.dg'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					      		<div class="col-6 col-sm-6 col-md-3">
					      			{!! Form::text('dg_disabled',@$product->dg,array('class'=>'form-control numberWithSingleComma setDecimal dg', 'disabled','maxlength'=>'19','id'=>'dg')) !!}
					      			<input type='hidden' class="dg setDecimal" name="dg" value="{{ @$product->dg }}">
					      		</div>
					      		<div class="col-6 col-sm-6 col-md-5">
					      			{!! Form::label('%', '%', array('class' => 'col-md-3 col-6 col-sm-6 col-form-label')) !!}
					      		</div>
						    </div>

						      <div class="form-group row">
						     	{!! Form::label('stockable', __('main.stockable'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						     	<div class="col-md-6">
						      		@if(@$stockable && $stockable == 1)
										{!! Form::checkbox('stockable', '1', @$accplan->stockable, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "stockable", "checked" => "checked")) !!}
									@else
										{!! Form::checkbox('stockable', '1', @$accplan->stockable, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "stockable")) !!}
									@endif
								</div>
						    </div>


						</div>
					</div>
	                <div class="col-l text-sm-center">
	                	<button type="submit" class="btn btn-primary" name="submit_btn" id="product_submit_btn">{!! $btn !!}</button>
	                	<a href="{!!route('main.product.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
	                	@if (@$product->id && (Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" ))
		                	@if (count(@$hide_delete))
							@else
								<a class="btn btn-danger text-sm-float-right"  href="{{ route('main.product.destroy', array($product->id)) }}" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('main.product')) !!}?" data-csrf="{!! csrf_token() !!}">
		                            <i class="fas fa-trash-alt"></i>
		                        </a>
							@endif
                        @endif
	                </div>

	                 @if (@$product->id)
						<div class="accordion mt-2" id="locationCard">
					        <div class="card">
					            <div class="card-header" id="headingOne">
					                <button type="button" class="btn btn-link locationCollapse" data-toggle="collapse" data-target="#locationCardCollapse">{!! trans('main.default_locations') !!}</button>
				                  	<a class="btn btn-primary openModal" href="javascript:;" data-id="" data-href="{!! route('main.product.addLocation') !!}" form-name="productLocationForm"><i class="fas fa-plus"></i></a>
					            </div>
					            <div id="locationCardCollapse" class="collapse" aria-labelledby="headingOne" data-parent="#locationCard">
					                <div class="card-body" id="locationView">
					                	@include('warehousedetails/products/partials/productLocation')
					                </div>
					            </div>
					        </div>
					    </div>
				    @endif

					@if (@$product->id && @$product->stockable == 1)
						<div class="accordion mt-2" id="accordionExample">
					        <div class="card">
					            <div class="card-header" id="headingOne">
					                <button type="button" class="btn btn-link stockCollapse" data-toggle="collapse" data-target="#collapseOne">{!! trans('main.stock') !!}</button>
					                <button type="button" class="btn btn-link float-right" data-toggle="collapse" data-target="#collapseOne"><i class="fa fa-plus"></i></button>
					            </div>
					            <div id="collapseOne" class="collapse {!! @$disable_div_val !!}" aria-labelledby="headingOne" data-parent="#accordionExample">
					                <div class="card-body" id="stockView">
					                </div>
					            </div>
					        </div>
					    </div>
				    @endif

				    @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
					    @if (@$product->id)
							<div class="accordion mt-2" id="supplirCard">
						        <div class="card">
						            <div class="card-header" id="headingOne">
						                <button type="button" class="btn btn-link supplierCollapse" data-toggle="collapse" data-target="#supplirCardCollapse">{!! trans('main.suppliers') !!}</button>
						                <a class="btn btn-primary float-right openModal" href="javascript:;" data-id="" data-href="{!! route('main.productSupplier.loadview') !!}" form-name="productSupplierform">{!! __('main.add') !!}</a>
						            </div>
						            <div id="supplirCardCollapse" class="collapse" aria-labelledby="headingOne" data-parent="#supplirCard">
						                <div class="card-body" id="supplierView">
						                	@include('warehousedetails/products/partials/supplier')
						                </div>
						            </div>
						        </div>
						    </div>
					    @endif
				    @endif
				</div>
				<input type="hidden" name="vendor_price_nok" id="hidden_nok_price" value="{{ @$product->vendor_price_nok }}">
			{!! form::close() !!}
		</div>
	</div>
@stop
@section('page_js')
	<script type="text/javascript">
		$("#productform").validate();
		var supplier_count = "{{ @$productSuppliers ? count(@$productSuppliers) : 0 }}";
		var location_count = "{{ @$productLocations ? count(@$productLocations) : 0 }}";
		var product_id = "{{ @$product->id }}";
		var stockUrl = "{!! route('main.warehousedetails.getOnstockDetails') !!}";
		var id = "{!! @$product['product_number'] !!}";
		var product_id = "{!! @$product['id'] !!}";
		var getCurrencyDetailUrl = "{!! route('main.currencyController.getCurrencyDetails') !!}"
		$(".openModal").click(function (event) {
		  $(".subPanelModelLG").modal("show");
		  var form_name = $(this).attr('form-name');
		  $('#subPanelContentLG').load($(this).attr('data-href') + "?product_id=" + product_id + "&id=" + $(this).attr('data-id'), function () {
		    $("#" + form_name).validate();
		  });
		});
		if (location_count > 0) {
			setTimeout(function() {
				$('#locationCardCollapse').addClass('show');
			}, 100)
		}
		if (supplier_count > 0) {
			$('.supplierCollapse').trigger('click');
		}
		var user_type = "{{ Session::get('usertype') }}";
		if (user_type == "User") {
			$('#product_div').find('.form-control').attr('disabled', 'disabled');
			$('#product_submit_btn').attr('disabled', 'disabled');
		}
	</script>
 	{!! Html::script(mix('js/product.js')) !!}
@endsection
