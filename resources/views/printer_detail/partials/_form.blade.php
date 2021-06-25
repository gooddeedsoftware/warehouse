<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!! __('main.printer_detail') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
                        {!! Form::label('name', __('main.name'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('name',@$printer_detail->name,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('number', __('main.number'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('number',@$printer_detail->number,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>
	           </div>
	        </div>
			<div class="col-md-6 text-center">
				<button type='submit' class="btn btn-primary printer_detail_submit_btn" name="printer_detail_submit_btn">{{$btn}}</button>
				<a href="{{ route('main.printer_detail.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
			</div>
		</div>
	</div>
</div> 
@section('page_js')
<script type="text/javascript">
	$("#printer_detailform").validate();
</script>
@endsection
