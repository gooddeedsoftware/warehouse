<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!! __('main.logistraDetails') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
                        {!! Form::label('name', __('main.name'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('name',@$logistraDetails->name,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>

                    
                    <div class="form-group row">
                        {!! Form::label('cargonizer_key', __('main.cargonizer_key'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('cargonizer_key',@$logistraDetails->cargonizer_key,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('cargonizer_sender', __('main.cargonizer_sender'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('cargonizer_sender',@$logistraDetails->cargonizer_sender,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>

                  	<div class="form-group row">
				    	{!! Form::label('status', trans('main.status'), array('class' => ' col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!!Form::select('status',array('1' => __('main.active'),'0' => __('main.inactive')),@$logistraDetails->status,array('class'=>'form-control'))!!}
						</div>
				    </div>
	           </div>
	        </div>
			<div class="col-md-6 text-center">
				<button type='submit' class="btn btn-primary logistraDetails_submit_btn" name="logistraDetails_submit_btn">{{$btn}}</button>
				<a href="{{ route('main.logistraDetails.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
			</div>
		</div>
	</div>
</div> 
@section('page_js')
<script type="text/javascript">
	$("#logistraDetailsform").validate();
</script>
@endsection
