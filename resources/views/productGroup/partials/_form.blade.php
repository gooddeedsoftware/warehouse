<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!! __('main.productGroup') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('number',  __('main.number'), array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('number', @$productGroup->number, array('class'=>'form-control','required' )) !!}   
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('name',  __('main.name'), array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('name', @$productGroup->name, array('class'=>'form-control','required' )) !!}   
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('status', trans('main.status'), array('class' => ' col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!!Form::select('status',array('0' => __('main.active'),'1' => __('main.inactive')), @$productGroup->status,array('class'=>'form-control'))!!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-6 text-center">
                <button type='submit' class="btn btn-primary productGroup_submit_btn settingsCrud" name="productGroup_submit_btn" form-name="productGroupForm">{{$btn}}</button>
                <a href="{{ route('main.productGroup.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script type="text/javascript">
    $("#productGroupForm").validate();
</script>
@endsection
