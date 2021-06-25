<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!__('main.warehouse') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                   <div class="form-group row">
                        {!! Form::label('shortname', trans('main.name'), array('class' => 'col-md-4 col-form-label text-md-right  custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('shortname',@$warehouse->shortname,array('class'=>'form-control', 'required','max-length'=>32)) !!}
                        </div>
                    </div>
                   <div class="form-group row">
                        {!! Form::label('main',__('main.main'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
                        <div class="col-md-6">
                            {!! Form::select('main',@$warehousemain_array,@$warehouse->main ? sprintf("%02d", $warehouse->main) : '' ,array('class'=>'form-control','placeholder' => trans('main.selected'))) !!}
                        </div>
                    </div>
                   <div class="form-group row">
                        {!! Form::label('responsible',__('main.responsible'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
                        <div class="col-md-6">
                            {!! Form::select('responsible[]', @$users, @$warehouse['responsible'], array('class'=>'form-control select2', 'multiple' => 'multiple')) !!}
                        </div>
                    </div>
                   <div class="form-group row">
                        {!! Form::label('notification_email',__('main.notification_email'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
                        <div class="col-md-6">
                            {!! Form::email('notification_email', @$warehouse->notification_email, array('class'=>'form-control ', 'max-length' => 100)) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
				<button type="submit" class="btn btn-primary" name="warehouse_submit_btn">{!! $btn !!}</button>
                <a href="{!!route('main.warehouse.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
			</div>
        </div>
    </div>
</div> 

@section('page_js')
<script type="text/javascript">
    $("#warehouseForm").validate();
</script>
@endsection
