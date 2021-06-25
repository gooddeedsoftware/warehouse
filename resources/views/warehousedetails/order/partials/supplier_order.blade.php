@extends('layouts.layouts')
@section('title',trans('main.warehouse'))
@section('header')
<h3><i class="icon-message"></i>{!!trans('main.warehouse') !!}</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.warehouse') !!}</p>
@stop
@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<div class="row">
                <div class="col-8">
					@if (@$warehouseorder->id)
		                <b>{!! trans('main.order') !!} {!! @$warehouseorder->order_number !!}</b>
		            @else
		                <b>{!! trans('main.create_order') !!} </b>
		            @endif
		        </div>
		        <div class="col-4 text-right">
		        	@if (@$warehouseorder->id)
			        	<a href="{!! route('main.warehouseorder.downloadWarehouseReport', array(@$warehouseorder->id)) !!}" id="download_warehouse_report_btn" class="download_warehouse_report_btn btn btn-primary card-header-btn " value="{!! @$warehouseorder->id !!}"><i class="fa fa-download" aria-hidden="true"></i></a>
	                    <a href="{!! route('main.warehouseorder.sendSupplierOrderMail', array(@$warehouseorder->id)) !!}" class="send_order_mail_btn btn btn-primary card-header-btn"><i class="fa fa-envelope"></i></a>
                    @endif
                </div>
		    </div>
		</div>
		<div class="card-body">
			@if (@$warehouseorder->id)
                {!! Form::open(array('route' => array('main.warehouseSupplierOrder.update',$warehouseorder->id),'method'=>'PUT','id'=>'warehousesupplierorderform','name'=>'warehousesupplierorderform','class' => 'form-horizontal  row-border')) !!}
                {!! Form::hidden('order_id',@$warehouseorder->id,array('id'=>'rder_id_value')) !!}
            @else
                {!! Form::open(array('route' => 'main.warehouseSupplierOrder.store','id'=>'warehousesupplierorderform','name'=>'warehousesupplierorderform','class'=>'form-horizontal')) !!}
            @endif
				<div class="row">
					<div class="col-md-6">
						 {!! Form::hidden('order_type', 3,array('id'=>'order_type')) !!}
						<div class="form-group row">
	                        {!! Form::label('order_date', trans('main.order_date'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                            {!! Form::text('order_date',@$warehouseorder->order_date ? $warehouseorder->order_date : date('d.m.Y'),array('class'=>'form-control','id'=>'warehouse_order_date' )) !!}
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                        {!! Form::label('supplier_id', trans('main.supplier'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                             {!! Form::select('supplier_id',@$supplier,@$warehouseorder->supplier_id,array('class'=>'form-control','placeholder'=>trans('main.selected'),'id'=>'supplier','width'=>'100%')) !!}
	                        </div>
	                    </div>

                    	<div class="form-group row">
	                        {!! Form::label('supplier_ref', trans('main.supplier_ref'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                            {!! Form::text('supplier_ref',@$warehouseorder->supplier_ref,array('class'=>'form-control','id'=>'supplier_ref' )) !!}
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                        {!! Form::label('order_status', trans('main.order_status'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                            {!! Form::select('order_status', @$status,@$warehouseorder->order_status,array('class'=>'form-control','id'=>'order_status')) !!}
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                        {!! Form::label('priority', trans('main.priority'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                            {!! Form::select('priority',@$priorities,@$warehouseorder->priority ? $warehouseorder->priority : '02',array('class'=>'form-control','id'=>'priority')) !!}
	                        </div>
	                    </div>

	                     <div class="form-group row">
	                        {!! Form::label('our_reference', trans('main.our_reference'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                            {!! Form::select('our_reference', @$users, @$warehouseorder->our_reference ? $warehouseorder->our_reference : Auth::user()->id, array('class'=>'form-control','id'=>'our_reference')) !!}
	                        </div>
	                    </div>

					</div>
					<div class="col-md-6">

						<div class="form-group row">
							{!! Form::label('company', __('main.company'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
							<div class="col-md-6">
								{!! Form::text('company', @$warehouseorder->company ? $warehouseorder->company : @$company->name, array('class' => 'form-control company')) !!}
							</div>
						</div>

						<div class="form-group row">
							{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
							<div class="col-md-6">
								{!! Form::text('post_address', @$warehouseorder->post_address ? $warehouseorder->post_address : @$company->post_address, array('class' => 'form-control post_address')) !!}
							</div>
						</div>

						<div class="form-group row">
							{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
							<div class="col-sm-4 col-md-2 form-group">
								{!! Form::text('zip', @$warehouseorder->zip ? $warehouseorder->zip : @$company->zip, array('class' => 'form-control zip')) !!}
							</div>
							<div class="col-sm-8 col-md-4 form-group">
								{!! Form::text('city', @$warehouseorder->city ? $warehouseorder->city : @$company->city, array('class' => 'form-control city')) !!}
							</div>
						</div>

						<div class="form-group row">
							{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
							<div class="col-md-6">
								{!! Form::text('country', @$warehouseorder->country ? $warehouseorder->country : @$countries[@$company->country], array('class' => 'form-control country')) !!}
							</div>
						</div>

						<div class="form-group row">
	                        {!! Form::label('delivery_method', trans('main.delivery_method'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
	                        <div class="col-md-6">
	                            {!! Form::text('delivery_method',@$warehouseorder->delivery_method,array('class'=>'form-control','id'=>'delivery_method' )) !!}
	                        </div>
	                    </div>
					</div>
					<div class="col-md-12">
						<div class="form-group row">
		                    {!! Form::label('order_comment', trans('main.comments'), array('class' => 'col-md-2 col-form-label text-md-right')) !!}
		                    <div class="col-md-9">
		                        {!! Form::textarea('order_comment',@$warehouseorder->id ? @$warehouseorder->order_comment : $standard_text->data, array('class' => 'form-control','rows' => 4)) !!}
		                    </div>
		                </div>
					</div>

					<div class="form-group">
						<a class="btn btn-primary" href="#" id="cloneProductTableBtn" onclick="createNewProductTableRow();" style="display: none;">{!! trans('main.addnew') !!}</a>
						@if (count(@$product_packages) > 0) 
							<a class="btn btn-primary dropdown-toggle" href="#" role="button" id="product_packages_div" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  style="display: none;">
								{!!trans('main.add_package') !!}
							</a>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
								@if(@$product_packages)
								@foreach(@$product_packages as $key => $product)
								<a value="{!! $key !!}" class="dropdown-item product_package" href="#">{!! $product !!}</a>
								@endforeach
								@endif
							</div>
						@endif
					</div>

					<div class="col-md-12 table-responsive">
						
		                <table class="table" id="warehouse_product_order_table">
		                    <thead>
		                        <tr>
		                            <th width="20%">
		                            	{!! trans('main.product') !!}
		                            </th>
		                            <th width="8%">{!! trans('main.qty') !!}</th>
		                            <th width="16%" class='product_comment_td'>{!! trans('main.comment') !!}</th>
		                            <th width="9%" class='rec_qty_td' style="display: none;">{!! trans('main.rec_qty') !!}</th>
		                            <th width="12%" class='rec_warehouse_td' style="display: none;">{!! trans('main.warehouse') !!}</th>
		                            <th width="12%" class='rec_location_td' style="display: none;">{!! trans('main.location') !!}</th>
		                            <th width="11%" class='rec_date_td' style="display: none;">{!! trans('main.rec_date') !!}</th>
		                        </tr>
		                    </thead>
		                    <tbody id="warehouse_product_order_table_body">
		                    </tbody>
		                </table>
					</div>
				</div>
				{!! Form::hidden('submit_button_value', '', array('class'=>'form-control','id'=>'submit_button_value' )) !!}
				{!! Form::hidden('hidden_warehouse_table_row_count', '', array('class'=>'form-control','id'=>'hidden_warehouse_table_row_count' )) !!}
				{!! Form::textarea('product_details', @$warehouseorder['product_details'], array('class'=>'form-control hide_div','id'=>'product_details' )) !!}
				{!! Form::textarea('products_array', @$products, array('class'=>'form-control hide_div','id'=>'products_array' )) !!}
				{!! Form::textarea('warehouses_array', @$warehouses, array('class'=>'form-control hide_div','id'=>'warehouses_array' )) !!}
				{!! Form::textarea('all_locations_array', @$all_locations, array('class'=>'form-control hide_div','id'=>'all_locations_array' )) !!}
				{!! Form::hidden('supplier_warhouse_id', @$warehouseorder['destination_warehouse'], array('class'=>'form-control','id'=>'supplier_warhouse_id' )) !!}
			{!! form::close() !!}
            <div class="col-l text-sm-center">
				<button type="button" class="btn btn-primary warehouseorder_submit_btn" id="warehouseorder_submit_btn" value="0">{!! $btn !!}</button>
				@if (@$warehouseorder['id'])
					<button type="button" class="btn btn-primary warehouseorder_submit_btn" id="warehouseorder_submit_btn" value="1">{!! $btn. ' & '. trans('main.close') !!}</button>
				@endif
                <a type="button" class="btn btn-danger" href="{!! route('main.warehouseorder.index') !!}">{!!trans('main.cancel') !!}</a>

                @if (@$warehouseorder && $warehouseorder->order_status <= 3)
                	<a href="{{ route('main.warehouseorder.destroy', array($warehouseorder->id)) }}" class="btn btn-danger float-right" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('main.orders')) !!}?" data-csrf="{!! csrf_token() !!}"><i class="fas fa-trash-alt"></i></a>
                @endif
			</div>
		</div>
	</div>
</div>

<a id="delete_package_button"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_package_model" data-toggle="modal" style="visibility:hidden;">Test</a>
<div class="modal fade" id="delete_package_model" role="dialog" aria-labelledby="addNewModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>{!! trans('main.delete_package_warning') !!}</h4>
                <br>
                <button type="button" class="btn btn-primary" id="delte_package_yes_btn" name="delte_package_yes_btn">{!! trans('main.yes') !!}</button>
                <button type="button" class="btn btn-danger" id="delete_package_no_btn" name="delete_package_no_btn">{!! trans('main.no') !!}</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
    var url = "{!! URL::to('/') !!}";
    var token = "{!! csrf_token() !!}";
    var confirm_delete = "{!! trans('main.deletefile') !!}";
    var whs_order_id = "{!! @$warehouseorder['id'] !!}";
    var product_location_validation = "{!! trans('main.fill_production_location') !!}";
    var checkproduct_exist_url = "{!! route('main.warehouseDetailsController.getLocations') !!}";
    var arichive_confimation_message = "{!! trans('main.arichive_confimation_message') !!}";
    var please_select_atleast_one_product = "{!! trans('main.please_select_atleast_one_product') !!}";
    var destination_warehosue_is_required  = "{!! trans('main.destination_warehosue_is_required') !!}";
    var source_warehosue_is_required = "{!! trans('main.source_warehosue_is_required') !!}";
    var add_product_package = "{!!trans('main.add_package') !!}";
    var not_allowed_to_change_status = "{!!trans('jslang.not_allowed_to_change_status') !!}";
    var something_went_wrong = "{!!trans('main.something_went_wrong') !!}";
    var select_location = "{!! trans('jslang.select_location') !!}";
    var product_not_avaliable = "{!!trans('jslang.product_not_avaliable') !!}";
    var stock_not_availabl = "{!!trans('jslang.stock_not_available') !!}";
    var order_qty_not_less_rec_qty = "{!!trans('jslang.order_qty_not_less_rec_qty') !!}";
    var rect_qty_not_greater_order_qty = "{!!trans('jslang.rect_qty_not_greater_order_qty') !!}";
    var package_qty_same = "{!!trans('jslang.package_qty_same') !!}";
    $(document).ready(function () {
        $("#warehouse_order_date").datetimepicker({
            format: 'DD.MM.YYYY',
            locale: "en-gb"
        });
        var id = "{!! @$warehouseorder['id'] !!}";
        showHideOrderFields();
        editSupplierOrder('{!! @$warehouseorder->order_status !!}');
    });
    $('.product_package').on('click', function (e) {
        var selected_package = $(this).attr('value');
        getPackageProducts(selected_package);
    });
</script>
{!! Html::script('js/supplier.warehousev2.js') !!}
@endsection
  