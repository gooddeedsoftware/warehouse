
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!trans('main.productpackage') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
                <div class="col-md-6">
                	<div class="form-group row">
			      		{!! Form::label('product_number', trans('main.product_number'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
			      			{!! Form::text('product_number',@$product->product_number,array('class'=>'form-control','id'=>'product_number', 'max-length' => 100, 'required' )) !!}
			      		</div>
				    </div>

				    <div class="form-group row">
			      		{!! Form::label('description', trans('main.description'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
			      			{!! Form::text('description',@$product->description,array('class'=>'form-control','id'=>'description', 'maxlength' => 255, 'required' )) !!}
			      		</div>
				    </div>

				    <div class="form-group row">
			      		{!! Form::label('fk_AccountNo',trans('main.account_no'),array('class'=>'col-md-4 col-form-label text-md-right  custom_required')) !!}
			      		<div class="col-md-6">
			      			{!! Form::select('acc_plan_id',@$accplans, @$product->acc_plan_id,array('class'=>'form-control','placeholder'=>trans('main.selected'),'required')) !!}
			      		</div>
				    </div>
				</div>
				<div class="col-md-6">
				    <div class="form-group row">
			      		{!! Form::label('sale_price', trans('main.sale_price'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
			      			{!! Form::text('sale_price',@$product->sale_price,array('class'=>'form-control numberWithSingleComma','maxlength'=>'19','id'=>'sale_price', 'required' => 'required')) !!}
			      		</div>
				    </div>

				    <div class="form-group row">
			      		{!! Form::label('tax', trans('main.tax'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
			      			{!! Form::text('tax',@$product->tax,array('class'=>'form-control numberWithSingleComma','maxlength'=>'7','id'=>'tax', 'required' => 'required')) !!}
			      		</div>
				    </div>
                </div>
                <div class="col-md-12 table-responsive">
                	<a class="btn btn-primary form-group" id="cloneProductTableBtn" href="#">{!! trans('main.addnew') !!}</a>
					<table class="table" id="product_package_table" width="100%">
						<thead>
		                    <tr>
		                        <th width="40%">
		                        	
		                        	{!! Form::label('product_name', trans('main.product'), array('class' => 'control-label custom_required')) !!}
		                     	</th>
		                        <th width="50%">
		                            {!! Form::label('product_qty', trans('main.qty'), array('class' => 'control-label custom_required')) !!}
		                    	</th>
		                    	<th></th>
		                    </tr>
	                    </thead>
	                    <tbody id="warehouse_product_order_table_body">
	                    </tbody>
	                </table>
	        	</div>
        	</div>
        	<div class="col-l">
				<button type="submit" class="btn btn-primary group_product_submit_btn formSaveBtn" id="group_product_submit_btn" form-name="productPackage_form">{!! $btn !!}</button>
                <a type="button" class="btn btn-danger" href="{!! route('main.productpackage.index') !!}">{!!trans('main.cancel') !!}</a>
                @if (@$product->id)
	                <a class="btn btn-danger text-sm-float-right"  href="{{ route('main.productpackage.destroy', array($product->id)) }}" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('main.productpackage')) !!}?" data-csrf="{!! csrf_token() !!}"> 
	                    <i class="fas fa-trash-alt"></i>
	                </a>
				@endif
			</div>
    	</div>
	</div>
</div>
<textarea style="display: none" id="product_lists">{!! @$product_list !!}</textarea>
<textarea style="display: none" id="pacakage_product_list">{!! @$pacakage_product_list !!}</textarea>
{!! Form::hidden('hidden_product_package_table_row_count', @$product_package_count, array('class'=>'form-control hidden_product_package_table_row_count','id'=>'hidden_product_package_table_row_count')) !!}

@section('page_js')
	<script type="text/javascript">
		$('#productPackage_form').validate();
		var confirm_delete = "{!! trans('main.deletefile') !!}";
		var select_text = "{!! trans('main.selected') !!}";
		var url = "{!! URL::to('/') !!}";
		var token = "{!! csrf_token() !!}";
	</script>
	{!! Html::script(mix('js/productpackage.js')) !!}
@endsection

