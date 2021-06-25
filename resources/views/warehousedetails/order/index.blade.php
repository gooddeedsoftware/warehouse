@extends('layouts.layouts')
@section('title',trans('main.warehouse'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.warehouse') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.warehouseOrder') !!}</p>
<p>{!!trans('main.warehouse') !!}</p>
@stop
@section('content')
<div class="container warehouseContainer">
    <div class="card">
        <div class="card-header warehouseContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link " href="{!! route('main.warehousedetails.index') !!}">{!! trans('main.stock') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">{!! trans('main.whs_orders') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/ccsheet') }}">{!! trans('main.ccsheet') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('main.product.index') !!}">{!! trans('main.products') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{!! route('main.productpackage.index') !!}">{!! trans('main.productpackage') !!}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="{{ url('whs_history') }}">{!! trans('main.history') !!}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
             @php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            @include('warehousedetails/order/filter')
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th >
                                <a>@sortablelink('order_number', trans('main.order_number'), ['warehouse' => 'warehouse'])</a>
                            </th>
                            <th>
                                <a>@sortablelink('ordertype', trans('main.order_type'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('priority', trans('main.comments'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('fromwarehouse', trans('main.from_warehouse'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('towarehouse', trans('main.to_warehouse'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('order_date', trans('main.order_date'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('orderstatus', trans('main.order_status'))</a>
                            </th>
                            <th>
                                <a id='cb'>@sortablelink('cb', trans('main.cb'), ['title' => 'Test'])</a>
                            </th>
                            <th>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count(@$orders) > 0 )
                            @foreach (@$orders as $order)
                                <tr>
                                    <td>
                                    @if (@$order->order_type == 1)
                                        <a href="{!! route('main.warehouseorder.editTransferOrder', array(@$order->id))!!}" class="edit_warehouse_order">{!! @$order->order_number !!}</a>
                                    @elseif(@$order->order_type == 4)
                                        <a href="{!! route('main.warehouseorder.editReturnOrder', array(@$order->id))!!}" class="editReturnOrder">{!! @$order->order_number !!}</a>
                                    @elseif(@$order->order_type == 2)
                                    <a href="{!! route('main.warehouseorder.editAdjustmentOrder', array(@$order->id))!!}" class="edit_warehouse_order">{!! @$order->order_number !!}</a>
                                    @else
                                        <a href="{!! route('main.warehouseorder.editSupplierOrder', array(@$order->id))!!}" class="edit_warehouse_order">{!! @$order->order_number !!}</a>
                                    @endif
                                    </td>
                                    <td> {!! htmlspecialchars(@$warehouse_order_types[@$order->order_type]) !!} </td>
                                    <td>
                                        @php
                                            $limit = 20;
                                            if (strlen($order->order_comment) > $limit )
                                                {
                                                    echo substr(htmlspecialchars(@$order->order_comment), 0, $limit) . '.....';
                                                }
                                                else
                                                {
                                                    echo htmlspecialchars(@$order->order_comment);
                                                }
                                        @endphp

                                    </td>
                                    <td>
                                        @if (@$order->order_type == 4)
                                            <a href="{!! route('main.ordermaterial.listOrderMaterials', @$order->customer_order_id) !!}" >{{ @$order->customer_order_number}}</a>
                                        @else
                                            {{str_limit(htmlspecialchars(@$order->from_whs_concat),20)}}
                                        @endif
                                    </td>
                                    <td>
                                        {!! htmlspecialchars(@$order->to_whs_concat)!!}
                                    </td>
                                    <td> {!! @$order->order_date !!} </td>
                                    @php if (@$order->order_status ==  0){ @$order->order_status = 1; } @endphp
                                    <td> {!! htmlspecialchars(@$warehouse_order_status[@$order->order_status]) !!} </td>
                                    <td>
                                        <a title="{!! htmlspecialchars(@$order->first_name) !!} {!! htmlspecialchars(@$order->last_name) !!}">
                                            {{  htmlspecialchars(@$order->added) }}
                                        </a>
                                    </td>
                                    <td>
                                        <a  href="{!! route('main.warehouseorder.downloadWarehouseReport', array(@$order->id)) !!}" id="download_warehouse_report_btn" class="download_warehouse_report_btn" value="{!! @$order->id !!}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                        @if (@$order->order_status == 1 || (@$order->order_status <= 3 && @$order->order_type == 3))
                                            <a href="{{ route('main.warehouseorder.destroy', array($order->id)) }}" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('main.orders')) !!}?" data-csrf="{!! csrf_token() !!}"><i class="delete-icon fas fa-trash-alt"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody> 
                </table>
            </div>
            @if (@$orders)
                @include('common.pagination',array('paginator'=>@$orders, 'formaction' => 'order_search_form'))
            @endif
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
    var token = "{!! csrf_token() !!}";
    var url = "{!! URL::to('/') !!}";
    var confirm_delete = "{!! trans('main.deletefile') !!}";
    var product_location_validation = "{!! trans('main.warehouseorder.fill_production_location') !!}";
    $("#cb").next('a').attr('title', "{!! trans('main.offer.historycreatedby') !!}");
    window.localStorage.setItem('create_adjustment_order_id', '');
    $(document).ready(function () {
         $("#search_by_order_status, #search_by_order_type").on("change", function (e) {
            $("#order_search_form").submit();
        });
    });
    if (!$("#accordionExample").is(":visible")) {
        $("#accordionExample").remove();
    } else {
        $('.filterOnsm').remove();
    }
</script>
@endsection
