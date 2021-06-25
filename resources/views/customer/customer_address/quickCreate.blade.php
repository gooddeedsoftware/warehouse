{!! Form::open( array('route' => array('main.customerAddress.createOrUpdateCustomerAddress'), 'id'=>'customerAddressform', 'name'=>'customerAddressform', 'class'=>'form-horizontal') ) !!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle"><b>{!!__('main.address') !!}</b></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	 	<span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    
	<div class="form-group row">
	    {!! Form::label('name', __('main.type'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
	    <div class="col-md-6">
	        {!! Form::select('type',@$customer_address_types, @$customerAddress->type,array('class'=>'form-control','required','placeholder'=>trans('main.selected'))) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('address', __('main.address'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
	    <div class="col-md-6">
	        {!! Form::text('address1',@$customerAddress->address1,array('class'=>'form-control','maxlength'=>'255','required','id'=>'address')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	    <div class="col-md-6">
	        {!! Form::text('address2',@$customerAddress->address2,array('class'=>'form-control','maxlength'=>'255','id'=>'address2')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('zip', __('main.zip'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	    <div class="col-md-6">
	        {!! Form::text('zip',@$customerAddress->zip,array('class'=>'form-control', 'id'=>'zip')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('city', __('main.city'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	    <div class="col-md-6">
	        {!! Form::text('city',@$customerAddress->city,array('class'=>'form-control', 'id'=>'city')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('country', __('main.country'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
	    <div class="col-md-6">
	        {!! Form::select('country',@$countries, @$customerAddress->country,array('class'=>'form-control','required','placeholder'=>trans('main.selected'))) !!}
	    </div>
	</div>

	{!! Form::hidden('customer_id', @$customer_id,array('class'=>'form-control', 'id'=>'customer_id')) !!}
	{!! Form::hidden('id', @$customerAddress->id, array('class'=>'form-control', 'id'=>'customerAddress_id')) !!}
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">{!! @$customerAddress->id ? trans('main.update') : trans('main.save') !!}</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('main.cancel') !!}</button>
</div>
{!! Form::close() !!}