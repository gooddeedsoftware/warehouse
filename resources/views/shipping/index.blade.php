@extends('layouts.layouts')
 @section('header')
    <h3><i class="icon-message"></i>{!!__('main.orders') !!}</h3>
@stop
@section('help')
    <p class="lead">{!!__('main.orders') !!}</p>
    <p>{!!__('main.orders.help') !!}</p>
@stop
@section('content')

<div class="container" id="shippmentContainer" orderid="{{ @$order_id }}">
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
                    <a class="nav-link" href="{!! route('main.ordermaterial.listOrderMaterials', @$order_id) !!}">
                        <span class="d-none d-sm-block">{!! __('main.materials') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        <span class="d-none d-sm-block">{!! __('main.shipping') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.order.billingData', @$order_id) !!}">
                        <span class="d-none d-sm-block">{!! __('main.billing_data') !!}</span>
                        <i class="d-block d-sm-none fa fa-wrench"></i>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="d-none d-sm-block">Invoices</span>
                        <i class="d-block d-sm-none fa fa-book"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="custom_required">{{ __('main.height') }}</label>
                        <input type="text" class="form-control volume validateNumbers" id="height" value="{{ @$measures->height }}">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="custom_required">{{ __('main.length') }}</label>
                        <input type="text" class="form-control volume validateNumbers" id="length" value="{{ @$measures->length }}">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="custom_required">{{ __('main.width') }}</label>
                        <input type="text" class="form-control volume validateNumbers" id="width" value="{{ @$measures->width }}">
                    </div>
                </div>
                 <div class="col-md-1">
                    <div class="form-group">
                        <label class="custom_required">{{ __('main.volume') }}</label>
                        <input type="text" class="form-control" id="volume" readonly disabled value="{{ @$measures->volume }}">
                    </div>
                </div>


                <div class="col-md-1">
                    <div class="form-group">
                        <label class="custom_required">{{ __('main.weight') }}</label>
                        <input type="text" class="form-control validateNumbers" id="weight" value="{{ @$measures->weight }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="custom_required">{{ __('main.sender') }}</label>
                        {{ Form::select('sender', @$senders, '',array('class'=>'form-control sender', 'placeholder' => __('main.selected'), 'id' => 'sender')) }}
                    </div>
                </div>


                <div class="col-md-2 offset-md-1">
                    <div class="form-group">
                        <label style="visibility: hidden;">demo</label>
                        <button class="btn btn-primary form-control" id="getprices" disabled="disabled">{{ __('main.getprices') }}</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label style="visibility: hidden;">demo</label>
                        <button class="btn btn-primary form-control" id="pickup">{{ __('main.pickup') }}</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="shippingTable">
                    <thead>
                        <tr>
                            <th width="10%"><a href="#">{{ __('main.carrier') }}</a></th>
                            <th width="15%"><a href="#">{{ __('main.product') }}</a></th>
                            <th width="10%"><a href="#">{{ __('main.listprice') }}</a></th>
                            <th width="10%"><a href="#">{{ __('main.customerprice') }}</a></th>
                            <th style="text-align: center;" width="10%"><a href="#">{{ __('main.selected') }}</a></th>
                            <th width="10%"><a href="#">{{ __('main.sender') }}</a></th>
                            <th width="10%"><a href="#">{{ __('main.tracking_number') }}</a></th>
                            <th width="5%"><a href="#">{{ __('main.label') }}</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(@$products)
                            @foreach($products as $product)
                                <tr class="saved" shipment_id="{{ $product->id }}" shippment-status="{!! @$product->shipment_status ? 1 : 0 !!}">
                                    @if($product->product_name == "Hentes" && !$product->shipment_status)
                                        <td>{{ @$product->carrier_name }}</td>
                                        <td>{{ @$product->product_name }}</td>
                                        <td>
                                            <input type="text" class="numberWithSingleComma pickupPrice netprice form-control" name="customerprice" value="{{ replaceDotWithComma(@$product->netprice) }}">
                                        </td>
                                         <td>
                                            <input type="text" class="numberWithSingleComma pickupPrice customerpickprice form-control" name="customerprice" value="{{ replaceDotWithComma(@$product->netprice) }}">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @else  
                                        <td>{{ @$product->carrier_name }}</td>
                                        <td>{{ @$product->product_name }}</td>
                                        <td>{{ replaceDotWithComma(@$product->netprice) }}</td>
                                        <td>
                                            @if($product->shipment_status == 1)
                                                {{ replaceDotWithComma(@$product->customerprice) }}
                                            @else
                                                <input type="text" class="numberWithSingleComma customerprice form-control" name="customerprice" value="{{ replaceDotWithComma(@$product->customerprice) }}">
                                                </div>
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if($product->shipment_status == 1)
                                                -
                                            @else
                                                <div class="custom-control custom-checkbox mb-3">
                                                  <input type="checkbox" class="custom-control-input saveShipment" id="customCheck" readonly checked disabled>
                                                  <label class="custom-control-label" for="customCheck"></label>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ @$all_senders[$product->sender_id] }}</td>
                                        <td>
                                            @if($product->shipment_status == 1)
                                                {{ @$product->track_number }}
                                            @else
                                                {{ __('main.no') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(@$product->consignment_id && $product->product_name != "Hentes") 
                                                <a  href="{!! route('main.downloadShipmentLabel', array(@$product->consignment_id)) !!}" class="download_btn" value="{!! @$product->consignment_id !!}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
    <script type="text/javascript">
        var token = "{!! csrf_token() !!}";
        var order_id = "{{ @$order_id }}";
        var getPriceUrl = "{!! route('main.order.getPrices') !!}";
        var storeShippingUrl = "{!! route('main.order.storeShipping') !!}";
        var updateShippingUrl = "{!! route('main.order.updateShipping') !!}";
    </script>
    {!! Html::script(mix('js/shipping.js')) !!}
@endsection
