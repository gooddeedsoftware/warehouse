{!! Form::open( array('route' => array('main.productSupplier.createOrUpdate'), 'id'=>'productSupplierform', 'name'=>'productSupplierform', 'class'=>'form-horizontal') ) !!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle"><b>{!!__('main.supplier') !!}</b></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	 	<span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
	<div class="row">
        <div class="col-md-6">
        	<div class="form-group row">
                {!! Form::label('supplier',__('main.supplier'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                <div class="col-md-8">
                    {{ Form::select('supplier', @$suppliers, @$productSupplier->supplier, array('class' => 'form-control currency', 'placeholder'=>trans('main.selected'),'required')) }}
                </div>
            </div>

            <div class="form-group row">
			    {!! Form::label('articlenumber', __('main.articlenumber'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			    <div class="col-md-8">
			        {!! Form::text('articlenumber',@$productSupplier->articlenumber,array('class'=>'form-control', 'id'=>'articlenumber', 'required')) !!}
			    </div>
			</div>

		 	<div class="form-group row">
			    {!! Form::label('articlename', __('main.articlename'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			    <div class="col-md-8">
			        {!! Form::text('articlename',@$productSupplier->articlename,array('class'=>'form-control', 'id'=>'articlename', 'required')) !!}
			    </div>
			</div>

			<div class="form-group row">
                {!! Form::label('currency',__('main.currency'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                <div class="col-md-8">
                    {{ Form::select('curr_iso_name', @$currency_list, @$productSupplier->curr_iso_name ? $productSupplier->curr_iso_name : 'NOK', array('id' => 'supplier_currency', 'class' => 'form-control currency', 'required')) }}
                </div>
            </div>


			<div class="form-group row">
			    {!! Form::label('supplier_price', __('main.supplier_price'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('supplier_price',@$productSupplier->supplier_price ? number_format($productSupplier->supplier_price, 2, ',', ''): '',array('id' => 'supplier_price', 'class'=>'form-control numberWithSingleComma ', 'id'=>'supplier_price')) !!}
			    </div>
			</div>

			<div class="form-group row">
			    {!! Form::label('supplier_discount', __('main.supplier_discount'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('supplier_discount',@$productSupplier->supplier_discount ? number_format($productSupplier->supplier_discount, 2, ',', ''): '0,00',array('id' => 'supplier_discount', 'class'=>'form-control numberWithSingleComma ', 'id'=>'supplier_discount')) !!}
			    </div>
			</div>
		</div>
		<div class="col-md-6">
			
			<div class="form-group row">
			    {!! Form::label('discounted', __('main.discounted'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('discount',@$productSupplier->discount ? number_format($productSupplier->discount, 2, ',', ''): '',array('class'=>'form-control numberWithSingleComma ', 'id'=>'discounted')) !!}
			    </div>
			</div>

			<div class="form-group row">
			    {!! Form::label('other', __('main.other'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('other',@$productSupplier->other ? number_format($productSupplier->other, 2, ',', ''): '0,00',array('class'=>'form-control numberWithSingleComma ', 'id'=>'other')) !!}
			    </div>
			</div>

			
			<div class="form-group row">
			    {!! Form::label('addon', __('main.addon'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('addon',@$productSupplier->addon ? number_format($productSupplier->addon, 2, ',', ''): '0,00',array('class'=>'form-control numberWithSingleComma ', 'id'=>'addon')) !!}
			    </div>
			</div>


			<div class="form-group row">
			    {!! Form::label('realcost', __('main.realcost'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('realcost',@$productSupplier->realcost ? number_format($productSupplier->realcost, 2, ',', ''): '',array('class'=>'form-control numberWithSingleComma ', 'id'=>'realcost')) !!}
			    </div>
			</div>

			<div class="form-group row">
			    {!! Form::label('realcost_nok', __('main.realcost_nok'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			    <div class="col-md-8">
			        {!! Form::text('realcost_nok',@$productSupplier->realcost_nok ? number_format($productSupplier->realcost_nok, 2, ',', ''): '',array('class'=>'form-control numberWithSingleComma ', 'id'=>'realcost_nok')) !!}
			    </div>
			</div>

			<div class="form-group row">
		     	{!! Form::label('is_main', __('main.is_main'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
		     	<div class="col-md-8">
		      		@if(@$productSupplier && $productSupplier->is_main == 1)
						{!! Form::checkbox('is_main', '1', @$productSupplier->is_main, array("class" => "mt-12", "id" => "is_main", "checked" => "checked")) !!}
					@else
						{!! Form::checkbox('is_main', '1', @$productSupplier->is_main, array("class" => "mt-12", "id" => "is_main")) !!}
					@endif
				</div>
			</div>
			{!! Form::hidden('product_id', @$product_id,array('class'=>'form-control', 'id'=>'product_id')) !!}
			{!! Form::hidden('id', @$id, array('class'=>'form-control')) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">{!! @$contact->id ? trans('main.update') : trans('main.save') !!}</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('main.cancel') !!}</button>
</div>
{!! Form::close() !!}