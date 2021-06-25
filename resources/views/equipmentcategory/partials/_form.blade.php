<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!! __('main.equipmentcategory') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('type',  __('main.equipmentcategory'), array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('type', @$equipmentcategory->type, array('class'=>'form-control','required', 'placeholder' => trans('main.type') )) !!}   
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <button type='submit' class="btn btn-primary equipmentcategory_submit_btn settingsCrud" name="equipmentcategory_submit_btn" form-name="equipmentcategoryForm">{{$btn}}</button>
                <a href="{{ route('main.equipmentcategory.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script type="text/javascript">
    $("#equipmentcategoryForm").validate();
</script>

@endsection
