{!! Form::open( array('route' => array('main.product.storeProductLocation'), 'id'=>'productLocationForm', 'name'=>'productLocationForm', 'class'=>'form-horizontal') ) !!}
	<div class="modal-content modal-md">
	    <div class="modal-header">
	        <h4>{!! trans('main.location') !!}</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
	    </div>
	    <!-- Modal content-->
	    <div class="modal-body">
	        <div class="panel panel-default">
	            <div class="panel-body">

					{{-- //warehouse --}}
					<div class="form-group row">
				     	{!! Form::label('warehouse', __('main.warehouse'),array('class'=>'col-md-3 col-form-label text-md-right custom_required')) !!}
				     	<div class="col-md-6">
			      			{!! Form::select('warehouse_id', @$warehouses, '',array('class'=>'form-control', 'id' => 'product_warehouse', 'required','placeholder' => trans('main.selected'))) !!}
			      		</div>
				    </div>

				    {{-- location --}}
					<div class="form-group row">
					    {!! Form::label('location', __('main.location'), array('class' => 'col-md-3 col-form-label text-md-right custom_required')) !!}
					    <div class="col-md-6">
					    	 {!! Form::select('location_id', [], '', array('class'=>'select2 form-control product_location', 'required', 'id' => 'product_location', 'placeholder' => __('main.selected'))) !!}
					    </div>
					</div>

					{{-- Hidden fields --}}
					{!! Form::hidden('product_id', $product_id) !!}
	            </div>
	        </div>
	    </div>
	    <div class="modal-footer">
		    <button type="submit" class="btn btn-primary">{!! __('main.save') !!}</button>
		    <button type="button" class="btn btn-danger" data-dismiss="modal">{!! __('main.cancel') !!}</button>
		</div>
	</div>
{!! Form::close() !!}