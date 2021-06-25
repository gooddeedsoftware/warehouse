<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!__('main.currency') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('curr_iso_name', __('main.currency'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('curr_iso_name', @$currency_list, @$currency->curr_iso_name, array('class'=>'form-control select2','required')) !!}    
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('exch_rate', __('main.exchange_rate'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('exch_rate', @$currency->exch_rate, array('class'=>'form-control validateNumbersWithComma','required')) !!}    
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('valid_from', __('main.valid_from'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('valid_from', @$currency->valid_from, array('class'=>'form-control','required')) !!}    
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <button type='submit' class="btn btn-primary currency_submit_btn settingsCrud" name="currency_submit_btn" form-name="currencyform">{{$btn}}</button>
                <a href="{{ route('main.currency.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script type="text/javascript">
     $('#valid_from').datetimepicker({
        format: 'DD.MM.YYYY',
        locale: 'en-gb'
    }).on("dp.change", function (e) {
    });
    $("#currencyform").validate();
</script>
@endsection
