@extends('layouts.layouts')
@section('title',__('main.product'))
@section('header')
<h3>
    <i class="icon-message">
    </i>
    {!!__('main.product') !!}
</h3>
{!!__('main.product') !!}
@stop

@section('help')
<p class="lead">
    {!!__('main.product') !!}
</p>
<p>
    {!!__('main.help') !!}
</p>
@stop
@section('content')
    <style>
        tr {
        cursor: pointer !important;
    }
    </style>
    <style type="text/css">
        .offer_material_Table {
        table-layout: fixed !important; width: 100% !important
    }
    .offer_material_Table input {
        width: 100% !important;
    }
    table.offer_material_Table tbody tr td {
        padding: 6px !important;
        padding-left: 3px !important;
        padding-right: 3px !important;
    }
    </style>
    <div class="container">
        <div class="card">
            <div class="card-header cutomerOrderContainer-Header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{!! route('main.offer.edit', @$order_id) !!}">
                            <span class="d-none d-sm-block">
                                {!! __('main.offer') !!}
                            </span>
                            <i class="d-block d-sm-none fa fa-file">
                            </i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <span class="d-none d-sm-block">
                                {!! __('main.materials') !!}
                            </span>
                            <i class="d-block d-sm-none fa fa-wrench">
                            </i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body" id="offerCardBody">
                {!! Form::open(array('route' => array('main.ordermaterial.listOfferMaterials', @$order_id), 'class'=>'offer_product_search_form', 'id' =>'offer_product_search_form')) !!}
                <div class="row hide-btn">
                    <div class="col-md-4">
                        <div class="btn-group form-group">
                            <button class="btn btn-primary add_product_material hide-btn" id="add_product_material" type="button">
                                {!!__('main.addnew') !!}
                            </button>
                            <button class="btn btn-primary dropdown-toggle invoice_save_btn hide-btn" data-toggle="dropdown" type="button">
                                <span class="caret">
                                </span>
                                <!-- caret -->
                                <span class="sr-only">
                                    Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu hide-btn" id="invoice_status" role="menu">
                                <a class="dropdown-item add_product_material hide-btn" href="javascript:;">
                                    {!!__('main.addnewproduct') !!}
                                </a>
                                <a class="dropdown-item add_new_text hide-btn" href="javascript:;">
                                    {!!__('main.addtext') !!}
                                </a>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right">
                        <a class="btn btn-primary form-group hide-btn" href="javascript:;" id="shippment">
                            {!!__('main.shippment') !!}
                        </a>
                        <a class="btn btn-primary form-group hide-btn" href="javascript:;" id="save_all_materials">
                            {!!__('main.save') !!}
                        </a>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group input-group">
                            {!! Form::text('offer_product_search_form', @Session::get('offer_product_search_form')['offer_product_search_form'], array('id' => 'product_search_str', 'class' => 'form-control searchField', 'placeholder' => __('main.search').' '.strtolower(__('main.product')))) !!}
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-search" id="department_search_btn">
                                    </i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="table-responsive">
                    <table class="table" id="offer_material_Table">
                        <thead>
                            <tr>
                                <th width="2%">
                                </th>
                                <th width="10%">
                                    <a>
                                        {!!__('main.product_number') !!}
                                    </a>
                                </th>

                                <th width="10%">
                                    <a>{!!__('main.description') !!}</a>
                                </th>


                                <th width="5%">
                                    <a>
                                        {!!__('main.order_quantity') !!}
                                    </a>
                                </th>
                                <th width="8%">
                                    <a>
                                        {!!__('main.unit') !!}
                                    </a>
                                </th>
                                <th width="5%">
                                    <a>
                                        {!!__('main.price') !!}
                                    </a>
                                </th>
                                <th width="5%">
                                    <a>
                                        {!! __('main.discount') !!}
                                    </a>
                                </th>
                                <th width="8%">
                                    <a>
                                        {!! __('main.sum_ex_vat') !!}
                                    </a>
                                </th>
                                <th width="5%">
                                    <a>
                                        {!! __('main.vat') !!}
                                    </a>
                                </th>
                                <th width="8%">
                                    <a>
                                        {!! __('main.delivery_date') !!}
                                    </a>
                                </th>
                                <th width="1%">
                                </th>
                                <th width="1%">
                                </th>
                                <th width="1%">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (@$order_materials)
                                @foreach (@$order_materials as $product)
                                    @if ($product->is_text == 1)
                                        <tr material_id="{{ $product->id }}">
                                            <td class="product_move">
                                                @if (@$disable_value != 1)
                                                    <i class="fa fa-arrows handle">
                                                    </i>
                                                @endif
                                            </td>

                                            <td colspan="9">
                                                <input class="product_text form-control" name="product_text" type="text" value=" {{ $product->product_text }} ">
                                                </input>
                                            </td>


                                            <td>
                                                @if (@$disable_value != 1)
                                                    <a data-csrf="{!! csrf_token() !!}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" href="{{ route('main.ordermaterial.destroy', array($product->id)) }}">
                                                        <i class="delete-icon fa fa-trash">
                                                        </i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="save_content_td" style="display:none;">
                                                <button class="btn btn-primary form-control save_text" data-val="{{ $product->id }}" onclick="saveText(this);" type="button">
                                                    save
                                                </button>
                                            </td>
                                        </tr>
                                    @elseif ($product->is_logistra == 1)
                                        <tr material_id="{{ $product->id }}">
                                            <td class="product_move">
                                                @if (@$disable_value != 1)
                                                    <i class="fa fa-arrows handle">
                                                    </i>
                                                @endif
                                            </td>
                                            <td class="product_td">
                                                {!! @$product->product_number !!} - {!! @$product->description !!}
                                                <input type="hidden" class="product product_number" name="product_number" value="{!! @$product->product_id !!}">

                                                {{-- {!! Form::select('product', @$all_prodducts, @$product->product_id, array('class'=>'form-control product product_number select2', 'disabled','placeholder' => __('main.selected'))) !!} --}}
                                            </td>

                                             <td></td>

                                            <td class="order_quantity_td">
                                                <input class="order_quantity text-align-right form-control numberWithSingleComma" data-val='{!! number_format(@$product->order_quantity, 2, ",", "") !!}' onchange="showSaveButton(this)" type="text" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="unit_td">
                                                {!! Form::select('unit', @$units, @$product->unit, array('class'=>'form-control unit','placeholder' => __('main.selected'))) !!}
                                            </td>
                                            <td class="price_td">
                                                <input class="price form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->offer_sale_price, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="discount_td">
                                                <input class="discount form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->discount, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="sum_ex_td">
                                                <input class="sum_ex_vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="vat_td">
                                                <input class="vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->vat, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="delivery_date_td">
                                                {!! Form::text('delivery_date', @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '', array('class' => 'form-control delivery_date position-relative ')) !!}
                                            </td>
                                            <td class="info_td">
                                                @if (@$disable_value != 1)
                                                    <a class="stock_info_btn" onclick="showStockInformation(this);" type="button">
                                                        <i class="fa fa-info-circle">
                                                        </i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if (@$disable_value != 1)
                                                    <a data-csrf="{!! csrf_token() !!}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" href="{{ route('main.ordermaterial.destroy', array($product->id)) }}">
                                                        <i class="delete-icon fa fa-trash">
                                                        </i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="save_content_td" style="display:none;">
                                                <button class="btn btn-primary form-control save_product" data-val="{{ $product->id }}" onclick="saveOrderMaterial(this);" type="button">
                                                    save
                                                </button>
                                            </td>
                                        </tr>
                                    @else
                                        <tr material_id="{{ $product->id }}" is_package="{{ @$product->is_package }}">
                                            <td class="product_move">
                                                @if (@$disable_value != 1)
                                                    <i class="fa fa-arrows handle">
                                                    </i>
                                                @endif
                                            </td>
                                            <td class="product_td">
                                                {!! @$product->product_number !!}
                                                <input type="hidden" class="product product_number" name="product_number" value="{!! @$product->product_id !!}">
                                            </td>

                                             <td>
                                                <input class='product_description form-control' value="{!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}" />
                                             </td>


                                            <td class="order_quantity_td">
                                                <input class="order_quantity text-align-right form-control numberWithSingleComma" data-val='{!! number_format(@$product->order_quantity, 2, ",", "") !!}' onchange="showSaveButton(this)" type="text" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="unit_td">
                                                {!! Form::select('unit', @$units, @$product->unit, array('class'=>'form-control unit','placeholder' => __('main.selected'))) !!}
                                            </td>
                                            <td class="price_td">
                                                <input class="price form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->offer_sale_price, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="discount_td">
                                                <input class="discount form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->discount, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="sum_ex_td">
                                                <input class="sum_ex_vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="vat_td">
                                                <input class="vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->vat, 2, ",", "") !!}'>
                                                </input>
                                            </td>
                                            <td class="delivery_date_td">
                                                {!! Form::text('delivery_date', @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '', array('class' => 'form-control delivery_date position-relative ')) !!}
                                            </td>
                                            <td class="info_td">
                                                @if (@$disable_value != 1)
                                                    <a class="stock_info_btn" onclick="showStockInformation(this);" type="button">
                                                        <i class="fa fa-info-circle">
                                                        </i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if (@$disable_value != 1)
                                                    <a data-csrf="{!! csrf_token() !!}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" href="{{ route('main.ordermaterial.destroy', array($product->id)) }}">
                                                        <i class="delete-icon fa fa-trash">
                                                        </i>
                                                    </a>
                                                 @endif
                                            </td>
                                            <td class="save_content_td" style="display:none;">
                                                <button class="btn btn-primary form-control save_product" data-val="{{ $product->id }}" onclick="saveOrderMaterial(this);" type="button">
                                                    save
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade stockInfoModal" data-backdrop="static" data-keyboard="false" id="stockInfoModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="stockInfoModal">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        <b>
                            {!!__('main.stock') !!}
                        </b>
                    </h5>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body" id="stockInfoModalContent">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" type="button">
                        {!! __('main.cancel') !!}
                    </button>
                </div>
            </div>
        </div>
    </div>
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
{!! Form::textarea('hidden_warehouses', @$warehouse_dropdown_array, array('class'=>'form-control hide_div','id'=>'hidden_warehouses')) !!}
{!! Form::textarea('hidden_products', @$products, array('class'=>'form-control hide_div','id'=>'hidden_products')) !!}
{!! Form::textarea('hidden_product_packages', @$product_packages, array('class'=>'form-control hide_div','id'=>'hidden_product_packages')) !!}
{!! Form::hidden('logged_user_id', $user_id,array('class'=>'form-control','id'=>'logged_user_id')) !!}
{!! Form::hidden('logged_user_name',Auth::user()->first_name,array('class'=>'form-control','id'=>'logged_user_name')) !!}
{!! Form::hidden('order_id',@$order_id,array('class'=>'form-control','id'=>'product_order_id')) !!}
{!! Form::hidden('usertype',@$usertype,array('class'=>'form-control','id'=>'hidden_usertype')) !!}
{!! Form::hidden('user_warehouse_resposible',@$user_warehouse_resposible,array('class'=>'form-control','id'=>'hidden_user_warehouse_resposible')) !!}
{!! Form::hidden('user_warehouse_resposible_id',@$user_warehouse_resposible_id,array('class'=>'form-control','id'=>'hidden_user_warehouse_resposible_id')) !!}
{!! Form::textarea('hidden_units', @$offer_order_units, array('class'=>'form-control hide_div','id'=>'hidden_units')) !!}
@stop
@section('page_js')
    {!! Html::script('jquery_ui/jquery-ui.js') !!}
    <script type="text/javascript">
        var customer_name = "{!! @$customerName !!}";
        var stockUrl = "{!! route('main.product.getOnstockDetails') !!}";
        var order_number = "{!! @$orders->order_number !!}";
        var project = "{!! @$orders->project_number !!}";
        var disable_value = "{{ @$disable_value }}";
        var deletemessage = "{!! __('main.deletefile') !!}";
        if (order_number) {
            var showText = order_number + " - " + customer_name.substring(0,10);
            if (project) {
                showText = showText  + " - " + project.substring(0,15)
            }
            $(".order_customer_label").text(showText);
        }
        var order_id = "{{ @$order_id }}";
        var getPriceUrl = "{!! route('main.order.getPrices') !!}";
        var storeShippingUrl = "{!! route('main.order.storeShipping') !!}";
        var updateShippingUrl = "{!! route('main.order.updateShipping') !!}";
        var select_product = '{!! __("main.select_product") !!}';
        if (disable_value == 1) {
            $('input,textarea,select,a,.select2').attr('readonly', true);
            $('a,.select2,select, button, .btn, #shippment').attr('disabled', true);
            $('.hide-btn').hide();
        }
    </script>
    {!! Html::script('js/offermaterial.v6.js') !!}
@endsection
</link>