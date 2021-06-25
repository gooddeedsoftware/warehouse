<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.location') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
                <div class="col-md-6">
					<div class="form-group row">
			      		{!! Form::label('name', __('main.name'), array('class' => 'col-md-4 col-form-label text-md-right  custom_required')) !!}
			      		<div class="col-md-6">
			      			{!! Form::text('name',@$location->name,array('class'=>'form-control','max-length'=>32, 'required')) !!}
			      		</div>
				    </div>
				    <div class="form-group row">
				     	{!! Form::label('warehouse',__('main.warehouse'),array('class'=>'col-md-4 col-form-label text-md-right  custom_required')) !!}
				     	<div class="col-md-6">
			      			{!! Form::select('warehouse_id',@$warehouses,@$location->warehouse_id,array('class'=>'form-control', 'required','placeholder' => trans('main.selected'))) !!}
			      		</div>
				    </div>
				    <div class="form-group row">
				     	{!! Form::label('scrap_location',__('main.scrap_location'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
				     	<div class="col-md-6">
			      			{!! Form::select('scrap_location',@$yesorno_language_array,@$location->scrap_location ? sprintf("%02d", $location->scrap_location) : '02' ,array('class'=>'form-control','placeholder' => trans('main.selected'))) !!}
			      		</div>
				    </div>
				    <div class="form-group row">
				     	{!! Form::label('return_location',__('main.return_location'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
				     	<div class="col-md-6">
			      			{!! Form::select('return_location',@$yesorno_language_array,@$location->return_location ? sprintf("%02d", $location->return_location) : '02' ,array('class'=>'form-control','placeholder' => trans('main.selected'))) !!}
			      		</div>
				    </div>
				</div>
			</div>
			<div class="col-md-6 text-center">
				<button type="submit" class="btn btn-primary" name="location_submit_btn">{!! $btn !!}</button>
                <a href="{!!route('main.location.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
			</div>
		</div>
	</div>
</div> 
@section('page_js')
<script type="text/javascript">
	$("#locationForm").validate();
</script>
@endsection
