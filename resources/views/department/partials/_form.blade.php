<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!! __('main.department') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
                        {!! Form::label('Nbr', __('main.nbr'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('Nbr',@$departments->Nbr,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>

					<div class="form-group row">
                        {!! Form::label('name',trans('main.name'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('Name',@$departments->Name,array('class'=>'form-control','required', 'maxlength' => 50)) !!}
                        </div>
                    </div>

					<div class="form-group row">
                        {!! Form::label('status',trans('main.status'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {{ Form::select('status', ['0' => trans('main.active'),'1' => trans('main.inactive')],@$departments->status,array('class'=>'form-control')) }}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('uni_department', __('main.uni_department'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::select('uni_department', @$uni_departments, @$departments->uni_department,array('class'=>'form-control select2','placeholder'=>trans('main.selected'))) !!}
                        </div>
                    </div>
	           </div>
	        </div>
			<div class="col-md-6 text-center">
				<button type='submit' class="btn btn-primary department_submit_btn" name="department_submit_btn">{{$btn}}</button>
				<a href="{{ route('main.department.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
			</div>
		</div>
	</div>
</div> 
@section('page_js')
<script type="text/javascript">
	$("#departmentform").validate();
</script>
@endsection
