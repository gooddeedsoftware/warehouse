<div class="modal-content modal-md">
    <div class="modal-header">
        <h4>{!! trans('main.onorder') !!}</h4>
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
                                    {!! trans('main.onorder') !!}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(@$records && count(@$records) > 0)
                                @foreach(@$records as $record)
                                <tr>
                                    <td>
                                        <a target="_blank" href="{!! route('main.warehouseorder.editSupplierOrder', array(@$record['order_details']->id))!!}" class="edit_warehouse_order">{!! @$record['order_details']->order_number!!}</a>
                                    </td>
                                     <td>
                                        @if(@$record['order_details']->order_date != null)
                                            {!! date('d.m.Y',strtotime($record['order_details']->order_date)) !!}
                                        @endif
                                    </td>
                                    <td>
                                        {!! number_format((float)@$record['on_order'],2, ',', ' ') !!}
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
