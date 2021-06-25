<div class="modal fade stockInfoModal" id="stockInfoModal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="stockInfoModal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><b>{!!__('main.stock') !!}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="stockInfoModalContent">
            </div>
            <div class="modal-footer">
                <button type='button' data-dismiss="modal" class="btn btn-danger">{!! __('main.cancel') !!}</button>
            </div>
        </div>
    </div>
</div>

<div class="hide_class">
    {!! Form::open( array('route' => 'main.ordermaterial.approveOrderMaterials','class'=>'form','id'=>'approve_product_form') ) !!}
        {{ Form::hidden('approved_product_ids','',array('id'=>'hidden_approved_product_ids'))}}
        {{ Form::hidden('approved_product_invoice_quantity','',array('id'=>'hidden_approved_product_invoice_quantity'))}}
    {!! Form::close() !!}
</div>

<div class="hide_class">
    {!! Form::open( array('route' => 'main.ordermaterial.createReturnOrder','class'=>'form','id'=>'return_order_form') ) !!}
    {{ Form::hidden('selected_materials','',array('id'=>'selected_materials'))}}
    {{ Form::hidden('order_id',@$order_id,array('id'=>'order_id'))}}
    {!! Form::close() !!}
</div>

<div class="hide_class">
    {!! Form::open( array('route' => 'main.order.picklist','class'=>'picklist-form','id'=>'picklist-form') ) !!}
        {{ Form::hidden('order_id', @$order_id,array('id'=>'picklist_order_id')) }}
        {{ Form::hidden('warehouse','',array('id'=>'form_pick_list_warehouse')) }}
        {{ Form::hidden('location','',array('id'=>'form_pick_list_location')) }}
    {!! Form::close() !!}

    {!! Form::open( array('route' => 'main.order.packlist','class'=>'packlist-form','id'=>'packlist-form') ) !!}
        {{ Form::hidden('order_id', @$order_id,array('id'=>'packlist_order_id')) }}
    {!! Form::close() !!}
</div>

<div class="modal fade pickListModal" id="pickListModal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>{!! __('main.choose_warehouse_for_picklist') !!}</h3>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    {!! Form::label('warehouse', trans('main.warehouse'), array('class' => 'col-md-3 col-form-label text-md-right')) !!}
                    <div class="col-md-7">
                        {!! Form::select('pick_list_warehouse', @$warehouse_dropdown_array, '', array('class'=>'select2 form-control pick_list_warehouse','id' => 'pick_list_warehouse','onchange' => 'getLocationsByWarehouse(this, 2)', 'placeholder' => __('main.selected'))) !!}
                    </div>
                </div>

                {{-- <div class="form-group row">
                    {!! Form::label('email', trans('main.location'), array('class' => 'col-md-3 col-form-label text-md-right')) !!}
                    <div class="col-md-7">
                        {!! Form::select('pick_list_location', [], '', array('class'=>'select2 form-control pick_list_location','id' => 'pick_list_location', 'placeholder' => __('main.selected'))) !!}
                    </div>
                </div> --}}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" disabled="disabled" id="picklist_btn">{!! trans('main.create_pick_list') !!}</button> 
                <button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('main.cancel') !!}</button>  
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

