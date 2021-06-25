<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!__('main.country') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('name', trans('main.name'), array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('name', @$country->name, array('class'=>'form-control','required' )) !!}   
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <button name='submit' class="btn btn-primary" type="submit">{{$btn}}</button>
                <a href="{{ route('main.country.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script name="text/javascript">
    $("#countryForm").validate();
</script>

@endsection
