<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!__('main.equipment') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('customer_id',__('main.customer'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            @if (@$btn_value == 1)
								{!! Form::select('customer_id',@$customer, @$equipment->customer_id,array('class'=>'form-control customer_id select2','placeholder'=>trans('main.selected'),'required')) !!}
							@elseif(@$btn_value == 3)
								{!! Form::select('customer_id',@$customer, @$equipment->customer_id,array('class'=>'form-control','placeholder'=>trans('main.selected'),'required','disabled' => 'disabled', 'readonly' => 'readonly')) !!}
								<input type="hidden" name="customer_id" value="{!! @$equipment->customer_id !!}">
							@elseif (@$btn_value != 1 && @$btn_value != 3)
								{!! Form::select('customer_id',@$customer, @$customer_id,array('class'=>'form-control','placeholder'=>trans('main.selected'),'required', 'disabled' => 'disabled', 'readonly' => 'readonly')) !!}
								<input type="hidden" name="customer_id" value="{!! @$customer_id !!}">
							@endif
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('internalnbr', __('main.internalnbr'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::text('internalnbr',@$equipment->internalnbr,array('class'=>'form-control','max-length'=>255)) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('reginnid', __('main.reginnid'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::text('reginnid',@$equipment->reginnid,array('class'=>'form-control','max-length'=>255)) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('note', __('main.note'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::textArea('note',@$equipment->note,array('class'=>'form-control', 'rows' => 4)) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('install_date', __('main.install_date'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::text('install_date', @$equipment->install_date, array('class'=>'form-control', 'id' => 'install_date')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('name_model', __('main.name_model'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                        	{!! Form::text('description',@$equipment->description,array('class'=>'form-control', 'required')) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('equipment_category', __('main.category'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::select('equipment_category',@$equipment_category, @$equipment->equipment_category,array('class'=>'form-control select2','placeholder'=>trans('main.selected'))) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('child_equipment_id', __('main.relation'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	@if (@$btn_value == 1)
                             	{!! Form::select('child_equipment_id',@$equipments,@$selected_parent_equipment,array('class'=>'form-control child_equipment_id','placeholder'=>trans('main.selected'))) !!}
                             @elseif(@$btn_value == 3)
								{!! Form::select('child_equipment_id',@$equipments,@$selected_parent_equipment,array('class'=>'form-control child_equipment_id','placeholder'=>trans('main.selected'))) !!}
							@else
								{!! Form::select('child_equipment_id',@$equipments,@$parent_equipment,array('class'=>'form-control child_equipment_id','placeholder'=>trans('main.selected'),'disabled' => 'disabled', 'readonly' => 'readonly')) !!}
								<input type="hidden" name="child_equipment_id" value="{!! @$parent_equipment !!}">
							@endif
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="btn_value" value="{!! @$btn_value !!}">
            <div class="col-md-12 text-center">
            	<button type="submit" class="btn btn-primary equipment_submit_btn" name="equipment_submit_btn">{!! $btn !!}</button>
                @if (@$btn_value == 1)
					<a href="{!!route('main.equipment.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
				@elseif(@$btn_value == 3)
					<a href="{!!route('main.equipment.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
				@else
					<a href="{{ route('main.equipment.edit',array($btn_value)) }}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
				@endif
            </div>
        </div>
    </div>
    <div class="form-group"></div>
	@if (@$equipment->id)
	    <div class="card">
	        <div class="card-body">
	        	<div class="row">
	        		<div class="col-md-12 form-group">
	        		 	<a class="btn btn-primary" href="{!! route('main.equipment.create',['btn_value' => @$equipment->id]) !!}">
							{!! trans('main.add').' '.strtolower(__('main.equipment')) !!}
		                </a>
	                </div>
	        	</div>
	            <div class="table-responsive">
	            	<table class="table table-striped table-hover">
		                <thead>
		                    <tr>
		                    	<th><a >{!! __('main.internalnbr') !!}</a></th>
		                    	<th><a >{!! __('main.reginnid') !!}</a></th>
								<th><a >{!! __('main.customer') !!}</a></th>
								<th><a>{!! __('main.name_model') !!}</a></th>
								<th><a >{!! __('main.category') !!}</a></th>
								<th><a >{!! __('main.install_date') !!}</a></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
		                </thead>
		                <tbody>
		                    @foreach(@$child_equipments_details as $equipment)
		                    <tr>
								<td><a href="{{route('main.equipment.edit',array($equipment->id))}}">{{ @$equipment->internalnbr}}</td>
								<td>{{ @$equipment->reginnid}}</td>
								<td>{{ @$customers[@$equipment->customer_id] }}</td>
								<td>
									<div>{{ @$equipment->description }}</div>
								</td>
								<td>
									<div>{{ @$equipment_category[@$equipment->equipment_category] }}</div>
								</td>
								<td>
									 @if(@$equipment->install_date)
								 		{!! date('d.m.Y', strtotime(@$equipment->install_date) )!!}&nbsp;&nbsp;&nbsp;
									 @endif
		                        </td>
								<td></td>
								<td>
									<a href="{{route('main.equipment.edit',array($equipment->id))}}" class="fa fa-pencil"></a>
								</td>
								<td class="delete-td">
									@if(count(@$equipment->order) < 1)
										<a href="{{ route('main.equipment.destroy', array($equipment->id)) }}"
										data-method="delete"
										data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.title')) !!}?" data-csrf="{!! csrf_token() !!}">
										<i class="fas fa-trash-alt"></i>
										</a>
									@endif
								</td>
							</tr>
							@endforeach
		                </tbody>
		            </table>
	            </div>
	        </div>
	    </div>
	@endif
	<div class="form-group"></div>
	@if(@$orders && count(@$orders) > 0)
	 	<div class="card">
	        <div class="card-header">
	            <b>{!!trans('main.orders') !!}</b>
	        </div>
	        <div class="card-body">
	        	<div class="table-responsive">
	            	<table class="table table-striped table-hover">
	            		<thead>
							<th><a>{{ trans('main.number') }}</a></th>
							<th><a>{{ trans('main.customer') }}</a></th>
							<th><a>{{ trans('main.requisition_short') }}</a></th>
							<th><a>{{ trans('main.ordered') }}</a></th>
							<th><a>{{ trans('main.status') }}</a></th>
						</thead>
						<tbody>
							@foreach(@$orders as $order_detail)
								<tr>
									<td><a href="{{route('main.order.edit', array($order_detail->id)) }}">{{ @$order_detail->order_number}}</a></td>
									<td><a href="{{route('main.customer.edit',array($order_detail->customer_id))}}">{{ @$customers[$order_detail->customer_id] }}</a></td>
									<td>
										{{ htmlspecialchars(@$order_detail->project_number) }}
									</td>
									<td>
										@if($order_detail->order_date != null)
											{!! date('d.m.Y',strtotime($order_detail->order_date)) !!}
										@endif
									</td>
									<td>{{ @$order_status[@$order_detail->status] }}</td>
								</tr>
							@endforeach
						</tbody>
	            	</table>
	            </div>
			</div>
		</div>
	@endif
</div>

@section('page_js')
<script type="text/javascript">
    $("#equipmentform").validate();
	$(document).ready(function () {
		$('#install_date').datetimepicker({
		    format: 'DD.MM.YYYY',
		    locale: 'en-gb'
		});
	});

	/*
	* Loading the equipemnts based on customer
	*/
	$( ".customer_id" ).change(function() {
	   var token = "{!! csrf_token() !!}";
	   customer_id = $(this).val();
	   $.ajax({
			type : "get",
			url : "/equipment/getchildequipments/"+customer_id,
			data : {
				'_token' : token,
				'rest' : true
			},
			async : false,
			success : function (data) {
				if (data) {
					var parsed_data = $.parseJSON(data);
					if (parsed_data['status'] == 'success') {
						 $.each(parsed_data['values'], function (key, value) {
	                        $('.child_equipment_id').append('<option value="' + key + '">' + value + '</option>');
                    	});
					}
				}
			},
			error : function (data) {
				console.log(data);
			}

		});
	});
</script>
@endsection
