<div class="modal-content modal-md">
    <div class="modal-header">
        <h4>{!! trans('main.customer_order') !!}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
    </div>
  <!-- Modal content-->
    <div class="modal-body">
        <div class="panel panel-default">
            <div class="panel-body" >
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                   {!! trans('main.order_number') !!}
                                </th>
                                <th>
                                    {!! trans('main.order_date') !!}
                                </th>
                                <th>
                                    {!! trans('main.quantity') !!}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count(@$customer_orders) > 0)
                                @foreach(@$customer_orders as $order)
                                <tr>
                                    <td>
                                        <a target="_blank" href="{{route('main.ordermaterial.listOrderMaterials', array($order->id)) }}">{{ @$order->order_number}}</a>
                                    </td>
                                    <td>
                                        @if($order->order_date != null)
                                            {!! date('d.m.Y',strtotime($order->order_date)) !!}
                                        @endif
                                    </td>
                                    <td>
                                        {!! number_format((float)@$order->quantity,2, ',', ' ') !!}
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
