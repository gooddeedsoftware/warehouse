{!! Form::open( array('route' => array('main.contact.createOrUpdateContact'), 'id'=>'contactform', 'name'=>'contactform', 'class'=>'form-horizontal') ) !!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle"><b>{!!__('main.contact') !!}</b></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	 	<span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    
		<div class="form-group row">
		    {!! Form::label('name', __('main.name'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
		    <div class="col-md-6">
		        {!! Form::text('name',@$contact->name,array('class'=>'form-control','maxlength'=>'255','required','id'=>'name')) !!}
		    </div>
		</div>

		<div class="form-group row">
		    {!! Form::label('title', trans('main.title'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
		    <div class="col-md-6">
		        {!! Form::text('title',@$contact->title,array('class'=>'form-control','maxlength'=>'255','required','id'=>'title')) !!}
		    </div>
		</div>

		<div class="form-group row">
		    {!! Form::label('email', __('main.email'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
		    <div class="col-md-6">
		        {!! Form::email('email',@$contact->email,array('class'=>'form-control','id'=>'email')) !!}
		    </div>
		</div>

		<div class="form-group row">
		    {!! Form::label('mobile', __('main.mobile'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
		    <div class="col-md-6">
		        {!! Form::text('mobile',@$contact->mobile,array('class'=>'form-control','maxlength'=>'15','id'=>'mobile')) !!}
		    </div>
		</div>

		<div class="form-group row">
		    {!! Form::label('phone', __('main.phone'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
		    <div class="col-md-6">
		        {!! Form::text('phone',@$contact->phone,array('class'=>'form-control','maxlength'=>'15','id'=>'phone')) !!}
		    </div>
		</div>
		{!! Form::hidden('customer_id', @$customer_id,array('class'=>'form-control', 'id'=>'customer_id')) !!}
		{!! Form::hidden('id', @$contact->id, array('class'=>'form-control', 'id'=>'contact_id')) !!}
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">{!! @$contact->id ? trans('main.update') : trans('main.save') !!}</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('main.cancel') !!}</button>
</div>
{!! Form::close() !!}