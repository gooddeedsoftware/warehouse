<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle"><b>{!!trans('main.contact') !!}</b></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	 	<span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
	<div class="form-group row">
	    {!! Form::label('name', trans('main.name'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
	    <div class="col-md-6">
	        {!! Form::text('name',@$contact->name,array('class'=>'form-control','maxlength'=>'255','required','id'=>'contact_name')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('email', trans('main.email'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	    <div class="col-md-6">
	        {!! Form::email('email',@$contact->email,array('class'=>'form-control','id'=>'contact_email')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('mobile', trans('main.mobile'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	    <div class="col-md-6">
	        {!! Form::text('mobile',@$contact->mobile,array('class'=>'form-control','maxlength'=>'15','id'=>'contact_mobile')) !!}
	    </div>
	</div>

	<div class="form-group row">
	    {!! Form::label('phone', trans('main.phone'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	    <div class="col-md-6">
	        {!! Form::text('phone',@$contact->phone,array('class'=>'form-control','maxlength'=>'15','id'=>'contact_phone')) !!}
	    </div>
	</div>
</div>
<div class="modal-footer">
    <button id="contact_submit" type="button" class="btn btn-primary">{!! trans('main.create') !!}</button>
	<button type='button' data-dismiss="modal" class="btn btn-danger">{!! trans('main.cancel') !!}</button>
</div>