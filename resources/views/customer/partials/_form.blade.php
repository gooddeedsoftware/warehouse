<div class='container'>
    <div class="card">
        <div class="card-header">
            @if (@$createSupplier && $createSupplier == 1) 
                <b>{!! __('main.supplier') !!}</b>
            @else
                <b>{!!__('main.customer') !!}</b>
            @endif
            @if(@$customer->id)
                <div class="float-right"><b>{{ @$customer->customer }}</b></div>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('name',trans('main.name'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6 input-group">
                            {!! Form::text('name',@$customer->name,array('class'=>'form-control customer_name', 'id' => 'customer_name', 'maxlength' => 50)) !!}
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary form-control"  id="customer_search_btn"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('is_supplier',__('main.is_supplier'), array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            @if (@$customer->is_supplier || (@$createSupplier && $createSupplier == 1 && !@$customer->id))
                                {!! Form::checkbox('is_supplier', '1', @$customer->is_supplier, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "is_supplier", "checked" => "checked")) !!}
                            @else
                                {!! Form::checkbox('is_supplier', '1', @$customer->is_supplier, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "is_supplier")) !!}
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('shortname',__('main.shortname'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::text('shortname',@$customer->shortname,array('class'=>'form-control shortname', 'size' => 10, 'maxlength' => 10)) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                       @if (@$customer->is_supplier || (@$createSupplier && $createSupplier == 1))
                            {!! Form::label('VAT',__('main.org_number'),array('class'=>'col-md-4 col-form-label text-md-right vat_label')) !!}
                        @else
                            {!! Form::label('VAT',__('main.org_number'),array('class'=>'col-md-4 col-form-label text-md-right vat_label custom_required')) !!}
                        @endif
                        <div class="col-md-6">
                            @if (@$customer->is_supplier || (@$createSupplier && $createSupplier == 1))
                                {!! Form::text('VAT',@$customer->VAT,array('class'=>'form-control validateNumbers customer_vat', 'id' => 'customer_vat','maxlength' => 9, 'minlength' => 9)) !!}
                            @else
                               {!! Form::text('VAT',@$customer->VAT,array('class'=>'form-control validateNumbers customer_vat', 'id' => 'customer_vat', 'required','maxlength' => 9, 'minlength' => 9)) !!}
                            @endif
                        </div>
                    </div>

                     <div class="form-group row">
                        {!! Form::label('phone',__('main.phone'),array('class'=>'col-md-4 col-form-label text-md-right phone')) !!}
                        <div class="col-md-6">
                            {!! Form::text('phone',@$customer->phone,array('class'=>'form-control phone')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('email',__('main.email'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::email('email',@$customer->email,array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('customer_note',__('main.customer_note'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::textArea('customer_note',@$customer->customer_note,array('class'=>'form-control', 'rows' => 2)) !!}
                        </div>
                    </div>
                    
                </div>

                <div class="col-md-6">

                    <div class="form-group row">
                        {!! Form::label('currency',__('main.currency'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {{ Form::select('currency', @$currency_list, @$customer->currency ? $customer->currency : 'NOK', array('class' => 'form-control currency')) }}
                        </div>
                    </div>


                    <div class="form-group row">
                        {!! Form::label('language',__('main.language'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {{ Form::select('language', ['1' => __('main.english'),'2' => __('main.norwegian')],@$customer->language ? $customer->language : '2', array('class' => 'form-control language')) }}
                        </div>
                    </div>


                    <div class="form-group row">
                        {!! Form::label('pmt_terms', __('main.pmt_terms_short'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::select('pmt_terms',@$pmt_terms,@$customer->pmt_terms,array('class'=>'form-control pmt_terms')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('web',__('main.web'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::text('web',@$customer->web,array('class'=>'form-control')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('creditlimit',__('main.creditlimit'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {!! Form::text('creditlimit',@$customer->creditlimit,array('class'=>'form-control numberWithSingleComma')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('status',__('main.status'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {{ Form::select('status', ['0' => __('main.active'),'1' => __('main.inactive')],@$customer->status, array('class' => 'form-control status')) }}
                        </div>
                    </div>


                    <div class="form-group row">
                        {!! Form::label('uniCustomerNo',__('main.uniCustomerNo'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                            {{ Form::select('uni_id', @$customer->id ? @$uni_customers: [], @$customer->uni_id, array('class' => 'form-control uniCustomerNo select2', 'id' => 'uniCustomerNo', 'placeholder' => __('main.selected'))) }}
                        </div>
                    </div>
                    <input type="hidden" id="uni_id_hidden" name="uni_id_hidden" value="{{ @$customer->uni_id }}">


                </div>
                <input type="hidden" name="createSupplier" value="{{ @$createSupplier }}">
                <div class="col-md-6 text-md-right form-group">
                    <button type='submit' class="btn btn-primary customer_submit_btn" name='customer_submit_btn'>{{$btn}}</button>
                    <button type='submit' class="btn btn-primary customer_submit_btn" name='customer_submit_btn' value="close">{{@$create_or_update_close}}</button>

                    @if (@$createSupplier && $createSupplier == 1) 
                        <a href="{{ route('main.supplier.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
                    @else
                        <a href="{{ route('main.customer.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
                    @endif
                    {!! Form::hidden('customer_submit_btn','',array('class'=>'form-control', 'id'=>'customer_submit_btn_hidden')) !!}
                </div>
                 <div class="col-md-4 text-sm-right form-group">

                    @if (@$has_equipment)
                        <a class="btn btn-primary pull-right view_equipment" role="button" href="#" id="view_equipment">{!! __('main.view_equipment')!!}</a>
                    @endif

                    @if (@$has_orders)
                        <a class="btn btn-primary pull-right view_orders" role="button" href="#" id="view_orders">{!! __('main.view_orders')!!}</a>
                    @endif
                </div>
            </div>
            <div class="clearfix mb-2"></div>
            @if (@$customer->id)
                @include('customer/contact/index')
            @endif
            <div class="clearfix mb-2"></div>
            @if (@$customer->id)
                @include('customer/customer_address/index')
            @endif
        </div>
    </div>
</div>
@section('page_style')
    {!! Html::style('css/jquery.flexdatalist.min.css') !!}
@endsection
@section('page_js')
{!! Html::script('js/jquery.flexdatalist.min.js') !!}
<script type="text/javascript">
    var url = "{!! URL::to('/') !!}";
    var getCustomerUrl = "{!! route('main.customer.getCustomers') !!}";
    var token = "{!! csrf_token() !!}";
    var customer_id = "{{ @$customer->id }}"
    // submit form when slider is chnaged
    $("#is_supplier").change(function () {
        if ($('input:checkbox[name=is_supplier]').is(':checked')) {
            $('.customer_vat').removeAttr('required')
            $('.vat_label').removeClass('custom_required');
        } else {
            $('.customer_vat').attr('required', true)
            $('.vat_label').addClass('custom_required');
        }
    });

</script>
{!! Html::script(mix('js/customer.js')) !!}
@endsection
