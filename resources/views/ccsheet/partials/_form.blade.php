<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!trans('main.ccsheet') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('warehouse',trans('main.warehouse'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('whs_id', @$warehouse, @$ccsheet->whs_id, array('class' => 'form-control', 'required' => 'required', 'placeholder' => trans('main.selected'))) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('comments',trans('main.comments'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::textarea('comments', @$ccsheet->comments, array('class' => 'form-control', 'rows' => '4', 'cols' => '3' )) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 float-right">
                    <div class='table-responsive'>    
                        <table class="table table-striped table-hover">
                            <thead>
                                <th>{!!trans('main.currency') !!}</th>
                                <th> {!!trans('main.exchange_rate') !!}</th>
                            </thead>
                            <tbody>
                            @foreach($currency_details as $currency)
                                <tr>
                                    <td>{{ @$currency_list[$currency->curr_iso_name] }}</td>
                                    <td>{!! @$currency->exch_rate !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 text-sm-center">
                    <button type="button" class="btn btn-primary formSaveBtn" id='ccsheet_btn' name="ccsheet_submit_btn" form-name="ccsheet_form">{!! $btn !!}</button>
                    <a href="{!!route('main.ccsheet.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@section('page_js')
    <script type="text/javascript">
        $('#ccsheet_form').validate();
    </script>
@endsection
