<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><b>{!!trans('main.create_return_order') !!}</b></h5>
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		 	<span aria-hidden="true">&times;</span>
	    </button>
    </div>
    <!-- Modal content-->
    <div class="modal-body ">
    	<table class="table table-striped table-hover table-responsive" id="order_material_Table">
			<thead>
				<tr>
					<th width="5%">
						<input class="select_all_materials" type="checkbox" id='select_all_materials'>
					</th>
					<th width="20%">
						<a>{!!trans('main.product_number') !!}</a>
					</th>
					<th width="10%">
						<a>{!!trans('main.order_quantity') !!}</a>
					</th>
					<th width="15%">
						<a>{!!trans('main.return_qty') !!}</a>
					</th>
					<th width="25%">
						<a>{!!trans('main.warehouse') !!}</a>
					</th>
					<th width="25%">
						<a>{!! trans('main.location') !!}</a>
					</th>
				</tr>
			</thead>
			<tbody>
				@if(@$materials)
					@foreach(@$materials as $material)
		                <tr data-val="{!! @$material['id'] !!}" id="{!! @$material['id'] !!}">
		                	<td>
		                		<input sn_type="{!! @$material['sn_required'] !!}" inventory_id="{!! @$material['inventory_id'] !!}" material_id="{!! @$material['material_id'] !!}" class="select_material" type="checkbox" value='{!! @$material["id"] !!}' id='select_material_{!! @$material["id"] !!}' product_id="{!! @$material['product_details']->id !!}" serial_number="{!! $material['serial_number'] !!}">
		                	</td>
		                	<td>
		                		{!! @$material['product_details']->product_number !!} - {!! @$material['product_details']->description !!}
		                	</td>
		                	<td>
		                		@if (@$material['sn_required'] == 1)
		                			1
		                		@else
		                			{!! number_format(@$material['available_qty'], 2, ",", "") !!}
		                		@endif
		                	</td>
		                	<td>
		                		@if (@$material['sn_required'] == 1)
		                			1
		                		@else
		                			<input class='return_qty form-control numberWithSingleComma' avaliable_qty="{!! number_format(@$material['available_qty'], 2, ',', '.') !!}" type='text'>
		                		@endif
		                	</td>
		                	<td>
		                		{!! Form::select('warehouse', @$warehouses, @$material['warehouse'], array('class'=>'select2 form-control material_warehouse','onchange' => 'getLocationsByWarehouse(this, 1)', 'id'=>'material_warehouse_id_'.@$material['id'], 'placeholder' => trans('main.selected'))) !!}
		                	</td>
		                	<td>
		                		{!! Form::select('locations', @$material['locations_array'], @$material['location'], array('class'=>'select2 form-control material_location','id'=>'material_location_id_'.@$material['id'], 'placeholder' => trans('main.selected'))) !!}
		                	</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
    </div>
    <div class="modal-footer">
    	<button type="button" class="btn btn-primary" id="create_return_order_btn" name="create_return_order_btn">{!! trans('main.create') !!}</button>
   		<button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('main.cancel') !!}</button>
    </div>
</div>
<script type="text/javascript">
	setTimeout(function(){
 		$(".select2").select2();
 	},100);
</script>