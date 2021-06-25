@extends('layouts.layouts')
@section('title',__('main.product'))
@section('header')
    <h3><i class="icon-message"></i>{!!__('main.product') !!}</h3>
    {!!__('main.product') !!}
@stop

@section('help')
    <p class="lead">{!!__('main.product') !!}</p>
    <p>{!!__('main.help') !!}</p>
@stop
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header cutomerOrderContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link"  href="{!! route('main.order.edit', @$order_id) !!}">
                        <span class="d-none d-sm-block">{!! __('main.order') !!}</span>
                        <i class="d-block d-sm-none fa fa-file"></i>
                    </a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        <span class="d-none d-sm-block">{!! __('main.materials') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.order.billingData', @$order_id) !!}">
                        <span class="d-none d-sm-block">{!! __('main.billing_data') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @include('ordermaterial.partials.materialHeader')
            @include('ordermaterial.partials.materialTable')
        </div>
    </div>
</div>
 @include('ordermaterial.partials.materialSupport')

{{-- Shippment modal --}}
<div class="modal fade shippmentModal" data-backdrop="static" data-keyboard="false" id="shippmentModal" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="shippmentModal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>
                        {!!__('main.shippment') !!}
                    </b>
                </h5>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body" id="shippmentContent">
                <div class="table-responsive">
                    <table class="table borderless">
                        <thead>
                            <tr>
                                <th width="9%">
                                    <label class="custom_required">
                                        {{ __('main.height') }}
                                    </label>
                                </th>
                                <th width="9%">
                                    <label class="custom_required">
                                        {{ __('main.length') }}
                                    </label>
                                </th>
                                <th width="9%">
                                    <label class="custom_required">
                                        {{ __('main.width') }}
                                    </label>
                                </th>
                                <th width="9%">
                                    <label class="custom_required">
                                        {{ __('main.volume') }}
                                    </label>
                                </th>
                                <th width="9%">
                                    <label class="custom_required">
                                        {{ __('main.weight') }}
                                    </label>
                                </th>
                                <th width="10%">
                                    <label class="custom_required">
                                        {{ __('main.sender') }}
                                    </label>
                                </th>
                                <th width="11%">
                                    <label class="custom_required">
                                        {{ __('main.printer') }}
                                    </label>
                                </th>
                                <th width="7%">
                                </th>
                                <th width="7%">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <td>
                                <input class="form-control volume validateNumbers" id="height" type="text" value="{{ @$measures->height }}">
                                </input>
                            </td>
                            <td>
                                <input class="form-control volume validateNumbers" id="length" type="text" value="{{ @$measures->length }}">
                                </input>
                            </td>
                            <td>
                                <input class="form-control volume validateNumbers" id="width" type="text" value="{{ @$measures->width }}">
                                </input>
                            </td>
                            <td>
                                <input class="form-control" disabled="" id="volume" readonly="" type="text" value="{{ @$measures->volume }}">
                                </input>
                            </td>
                            <td>
                                <input class="form-control validateNumbers" id="weight" type="text" value="{{ @$measures->weight }}">
                                </input>
                            </td>
                            <td>
                                {{ Form::select('sender', @$senders, '',array('class'=>'form-control sender', 'id' => 'sender')) }}
                            </td>
                             <td>
                                {{ Form::select('printer', @$printers ? $printers : [], @$printers[5] ? 5 : '', array('class'=>'form-control printer', 'placeholder' => __('main.selected'), 'id' => 'printer')) }}
                            </td>
                            <td>
                                <button class="btn btn-primary form-control" id="getprices">
                                    {{ __('main.getprices') }}
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-primary form-control" id="pickup">
                                    {{ __('main.pickup') }}
                                </button>
                            </td>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="shippingTable">
                        <thead>
                            <tr>
                                <th width="10%">
                                    <a href="#">
                                        {{ __('main.carrier') }}
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="#">
                                        {{ __('main.product') }}
                                    </a>
                                </th>
                                <th width="10%">
                                    <a href="#">
                                        {{ __('main.listprice') }}
                                    </a>
                                </th>
                                <th width="10%">
                                    <a href="#">
                                        {{ __('main.customerprice') }}
                                    </a>
                                </th>
                                <th style="text-align: center;" width="10%">
                                    <a href="#">
                                        {{ __('main.selected') }}
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="add_shipment" type="button">
                    {!! __('main.add') !!}
                </button>
                <button class="btn btn-danger" data-dismiss="modal" type="button">
                    {!! __('main.cancel') !!}
                </button>
            </div>
        </div>
    </div>
</div>
@stop
@section('page_js')
    {!! Html::script('jquery_ui/jquery-ui.js') !!}
    <script type="text/javascript">
        var filetodownload = "{!! session::get('filetodownload') !!}";
        var order_id = "{!! @$order_id !!}";
        var token = "{!! csrf_token() !!}";
        var url = "{!! URL::to('/') !!}";
        var stockUrl = "{!! route('main.product.getOnstockDetails') !!}";
        var usertype = "{!! Session::get('usertype') !!}";
        var user_id = "{!! Session::get('currentUserID');  !!}";
        var select_product = '{!! __("main.select_product") !!}';
        var select_location = '{!! __("main.select_location") !!}';
        var select_warehouse = '{!! __("main.select_warehouse") !!}';
        var add_product_package = "{!!__('main.addnew') !!} {!!__('main.productpackage.title') !!}";
        var deletemessage = "{!! __('main.deletefile') !!}";
        var message = '{!! __("main.message") !!}';
        var notify_text = '{!! __("main.notify_text") !!}';
        var quantity_alert_messge = '{!! __("main.quantity_alert_messge") !!}';
        var quantity_not_in_stock = '{!! __("main.quantity_not_in_stock") !!}';
        var check_package_quantity = '{!! __("main.check_package_quantity") !!}';
        var check_picked_quantity = '{!! __("main.check_picked_quantity") !!}';
        var quantity_required = '{!! __("main.quantity_required") !!}';
        var number_validation_msg = '{!! __("main.number_validation_msg") !!}';
        var package_invoice_qty_validation_msg = '{!! __("main.package_invoice_qty_validation_msg") !!}';
        var order_qty_validation_msg = '{!! __("main.order_qty_validation_msg") !!}';
        var return_qty_alert_msg = '{!! __("main.return_qty_alert_msg") !!}';
        var return_order_create_confirm_msg = '{!! __("main.return_order_create_confirm_msg") !!}';
        var select_atleat_one = '{!! __("main.select_atleat_one") !!}';
        var return_qty_must_be_1 = '{!! __("main.return_qty_must_be_1") !!}';
        var fill_warhouse =  '{!! __("main.fill_warhouse") !!}';
        var fill_location =  '{!! __("main.fill_location") !!}';
        var save_text = '{!! __("main.save") !!}';
        var update_text = '{!! __("main.update") !!}';
        var qty_greater_than_0_text = '{!! __("main.qty_greater_than_0") !!}';
        var billing_data_state = window.localStorage.getItem('billing_data_state');
        if (billing_data_state == 1) {
            window.location.href = $('#billing_data_btn').attr('data-href');
        }
        var customer_name = "{!! @$customerName !!}";
        var order_number = "{!! @$orders->order_number !!}";
        var project = "{!! @$orders->project_number !!}";
        var storeShippingUrl = "{!! route('main.order.storeShipping') !!}";
        var getPriceUrl = "{!! route('main.order.getPrices') !!}";
        if (order_number) {
            var showText = order_number + " - " + customer_name.substring(0,10);
            if (project) {
                showText = showText  + " - " + project.substring(0,15)
            }
            $(".order_customer_label").text(showText);
        }
    </script>
    {!! Html::script('js/ordermaterial.v7.js') !!}
    {!! Html::script('js/returnmaterial.js') !!}
@endsection
