
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
		<h4>{!! trans('main.product.title') !!}</h4>
	</div>
  <!-- Modal content-->
	<div class="modal-body">
		<div class="panel panel-default">
	
	   		<div class="panel-body" >
		  		@if (@$product->id)
		  			{!! Form::open(array('route' => array('main.ordermaterial.update',$product->id),'method'=>'PUT','id'=>'productform','name'=>'productform','class' => 'form-horizontal  row-border','data-toggle'=>"validator", 'files' => true)) !!}
		  		@else
	    			{!! Form::open( array('route' => 'main.ordermaterial.store','id'=>'productform','name'=>'productform','class'=>'form-horizontal order_material_from','data-toggle'=>"validator", 'files' => true) ) !!}
	    		@endif
	    			<br>
					
					{!! Form::hidden('material_id', @$product->id, array('class' => 'material_id', 'id' => 'material_id')) !!}

					<div class="form-group ">
						{!! Form::label('product_number', trans('main.product.product_number'), array('class' => 'control-label col-sm-12 col-md-3 custom_required')) !!}
						<div class="col-sm-12 col-md-6">
							{!! Form::select('product_number', @$products, @$product->product_number, array('class' => 'form-control select2',  'onchange' => 'productOnchnage(this, 2, "'.$usertype.'", "'.$user_warehouse_resposible.'", "'.$user_warehouse_resposible_id.'");', 'placeholder' =>trans('main.selected'), 'required', 'id' => 'product_number', 'width' => '100%')) !!}
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('order_quantity', trans('main.product.quantity'), array('class' => 'control-label col-sm-3 col-md-3 col-lg-3 custom_required')) !!}
						<div class="col-sm-12 col-md-6">
							{!! Form::number('order_quantity',@$product->order_quantity, array('class'=>'form-control order_quantity', 'required', 'id' => 'order_quantity')) !!}
						</div>
					</div>

					<div class="form-group " >
						{!! Form::label('warehouse', trans('main.product.warehouse'), array('class' => 'control-label col-sm-12 col-md-3')) !!}
						<div class="col-sm-12 col-md-6">

							@if ($usertype == "User" && !@$product->id) 
								{!! Form::select('warehouse',@$warehouse, $user_warehouse_resposible_id,array('class'=>'form-control select2','placeholder'=>trans('main.selected'),'required','id' => 'warehouse','onchange' => 'getProductDetailsForMaterials(this, 2, "'.$usertype.'", "'.$user_warehouse_resposible.'")')) !!}
								{!! Form::hidden('order_id',@$product['order_id'], array('class' => 'form-control', 'id' => 'product_order_id')) !!}
							@else 
								{!! Form::select('warehouse',@$warehouse, @$product->warehouse,array('class'=>'form-control select2','placeholder'=>trans('main.selected'),'required','id' => 'warehouse','onchange' => 'getProductDetailsForMaterials(this, 2, "'.$usertype.'", "'.$user_warehouse_resposible.'")')) !!}
								{!! Form::hidden('order_id',@$product['order_id'], array('class' => 'form-control', 'id' => 'product_order_id')) !!}
							@endif

						</div>
					</div>
				
					<div class="form-group">
						{!! Form::label('location', trans('main.location.title'), array('class' => 'control-label col-sm-12 col-md-3 ') ) !!}
						<div class="col-sm-12 col-md-6">
							{!! Form::select('location', @$locations, @$product->location, array('class' => 'form-control select2','id' => 'location' ,'placeholder' =>trans('main.selected'))) !!}
						</div>
						{!! Form::hidden('sn_required', @$product->sn_required ? $product->sn_required : 0 , array('class' => 'sn_required', 'id' => 'sn_required')) !!}
					</div>
					

					<div class="form-group">
						{!! Form::label('quantity', trans('main.product.picked_quantity'), array('class' => 'control-label col-sm-3 col-md-3 col-lg-3')) !!}
						<div class="col-sm-12 col-md-6">
							{!! Form::number('quantity', @$product->quantity, array('class'=>'form-control', 'required', 'onchange' => 'getSerialNumbers(this)')) !!}
						</div>
					</div>
								

					@if (Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative" || Session::get('usertype') == "Department Chief" )
						<div class="form-group">
		                	{!! Form::label('invoice_quantity', trans('main.product.invoice_quantity'), array('class' => 'control-label col-sm-12 col-md-3')) !!}
			                <div class="col-sm-12 col-md-6">
			                	{!! Form::number('invoice_quantity',@$product->invoice_quantity,array('class'=>'form-control','maxlength'=>'45','id'=>'invoice_quantity')) !!}
			                </div>
						</div> 
					@endif
									
					<div class="form-group ">
		                {!! Form::label('serial_number', trans('main.warehouseorder.serial_number'), array('class' => 'control-label col-sm-12 col-md-3')) !!}
		                <div class="col-sm-12 col-md-6" id="serial_number_div">
			                
		                </div>
					</div> 

					<div class="form-group" >
						{!! Form::label('user_id', trans('main.user.name'), array('class' => 'control-label col-sm-12 col-md-3')) !!}
						<div class="col-sm-12 col-md-6">
							{!! Form::hidden('signed_user_id', Session::get('currentUserID'); , array('class'=>'form-control', 'id' => 'signed_user_id')) !!}
							{!! Form::label('user', Auth::user()->first_name, array('class' => 'control-label col-sm-12 col-md-3')) !!}
						</div>
					</div>
		            
			
					<div class="form-group">
						<label class="col-xs-4 col-sm-2 col-md-2 col-lg-4 control-label"></label>
						<div class="col-xs-8 col-sm-4 col-md-4 col-lg-5">
							<button type="button" class="btn btn-primary" id="ordermaterial_save_btn" onclick="saveMaterial(this)">{!! @$btn !!}</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">{!!trans('main.cancel') !!}</button>
						</div>
					</div>
				{!! form::close() !!}
			</div>
		</div>
	</div>
</div>