<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!! __('main.grossMargin') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                   <div class="form-group row">
                        {!! Form::label('productGroup',trans('main.productGroup'),array('class'=>'col-md-4 col-form-label text-md-right  custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('product_group',@$groups, @$grossMargin->product_group,array('class'=>'form-control', 'id' => 'product_group', 'placeholder'=>trans('main.selected'),'required')) !!}
                        </div>
                    </div>
                   <div class="form-group row">
                        {!! Form::label('supplier',trans('main.supplier'),array('class'=>'col-md-4 col-form-label text-md-right  custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('supplier',@$suppliers, @$grossMargin->supplier,array('class'=>'form-control', 'id' => 'supplier', 'placeholder'=>trans('main.selected'),'required')) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('gross_margin',  __('main.grossMargin'), array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('gross_margin', @$grossMargin->gross_margin ? number_format($grossMargin->gross_margin, 2, ',', '')  : '', array('class'=>'form-control numberWithSingleMinusAndComma','required' )) !!}   
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <button type='submit' class="btn btn-primary grossMargin_submit_btn settingsCrud" name="grossMargin_submit_btn" form-name="grossMarginForm">{{$btn}}</button>
                <a href="{{ route('main.grossMargin.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script type="text/javascript">
    $("#grossMarginForm").validate();
</script>
@endsection
