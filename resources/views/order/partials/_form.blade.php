@section('addnew_contact')
	{!! Form::open( array('route' => array('main.contact.contact_inline_store'), 'id'=>'contact-form', 'class'=>'form','data-toggle'=>"validator") ) !!}
        @include('order/partials/contact', ['submit_text' => __('main.create').' '.__('main.contact'), 'btn'=>__('main.create')])
    {!! Form::close() !!}
@stop


@section('title', $is_offer == 0 ? __('main.order') : __('main.offer'))
<div class="row">
	<div class="col-md-6">
		<div class="form-group row">
			{!! Form::hidden('is_offer', $is_offer) !!}
			@if ($is_offer == 0)
				{!! Form::label('order_category', __('main.order_category'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
				<div class="col-md-6">
					@if (@$orders->id)
						{!! Form::label('',@$order_category[@$orders->order_category],array('class'=>'col-form-label col-sm-5 col-md-6 col-lg-7')) !!}
					@else
						{!! Form::select('order_category',@$order_category,@$orders['order_category'], array('class' => 'form-control','placeholder'=>__('main.selected'), 'required')) !!}
					@endif
				</div>
			@else
				{!! Form::hidden('order_category', "03") !!}
			@endif

	    </div>

	    <div class="form-group row">
	    	{!! Form::label('customer_id', __('main.customer'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			<div class="col-md-6">
				@if (@$orders->uni_status)
					{!! Form::select('customer_id_disabled',@$customers,@$orders['customer_id'],array('class'=>'form-control', 'readonly', 'disabled', 'placeholder'=>__('main.selected'))) !!}
					<input type="hidden" name="customer_id" value="{{ @$orders['customer_id'] }}" />
				@else 
					{!! Form::select('customer_id',@$customers,@$orders['customer_id'],array('class'=>'form-control select2', 'placeholder'=>__('main.selected'),'required')) !!}
				@endif
			</div>
	    </div>

	    <div class="form-group row">
	    	{!! Form::label('project_number', __('main.requisition'), array('class' => 'col-md-4 col-form-label text-md-right ')) !!}
			<div class="col-md-6">
				{!! Form::text('project_number',@$orders->project_number,array('class'=>'form-control')) !!}
			</div>
	    </div>

	    <div class="form-group row">
	    	{!! Form::label('equipment_id', __('main.equipment'), array('class' => 'col-md-4 col-form-label text-md-right ')) !!}
			<div class="col-md-6">
				{!! Form::select('equipment_id', @$equipments ? $equipments : [], @$orders['equipment_id'],array('class' => 'form-control 
				select2','id'=>'equipment_id','placeholder'=>__('main.selected'))) !!}
			</div>
	    </div>

	    <div class="form-group row">
	    	{!! Form::label('invoice_customer', __('main.invoice_customer'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			<div class="col-md-6">
				@if (@$orders->uni_status)
					{!! Form::select('invoice_customer_disabled',@$customers,@$orders['invoice_customer'],array('class'=>'form-control', 'readonly', 'disabled', 'placeholder'=>__('main.selected'))) !!}
					<input type="hidden" name="invoice_customer" value="{{ @$orders['customer_id'] }}" />
				@else 
					{!! Form::select('invoice_customer',@$customers,@$orders['invoice_customer'],array('class'=>'form-control select2','placeholder'=>__('main.selected'), 'required')) !!}
				@endif
			</div>
	    </div>

	    <div class="form-group row">
	    	{!! Form::label('contact_person', __('main.contact_person'), array('class' => 'col-md-4 col-form-label text-md-right ')) !!}
			<div class="col-md-5">
				{!! Form::select('contact_person_id[]',@$contacts_with_mobile ? @$contacts_with_mobile : [], @$selected_contact_persons,array('multiple'=>'true','class' => 'form-control select2','id'=>'contact_person')) !!}
			</div>
			<div class="col-md-1">
				<a id="order_contact_person" data-toggle="modal" class="btn btn-link addnew_contact_btn contact_quick_create" data-target="#addnew_contact" style="display:none" href="#" value=1><i class="fas fa-plus"></i></a>
			</div>
	    </div>

		<div class="form-group row">
			{!! Form::label('visitingAddress', __('main.visiting_address'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('visitingAddress', @$customer_address ? $customer_address : [], @$orders->visitingAddress, array('class' => 'form-control visitingAddress ','placeholder'=>__('main.selected'))) !!}
			</div>
		</div>

		<div class="form-group row">
			{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::text('visitingAddress1', @$orders->visitingAddress1, array('class' => 'form-control visitingAddress1')) !!}
			</div>
		</div>

		<div class="form-group row">
			{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::text('visitingAddress2', @$orders->visitingAddress2, array('class' => 'form-control visitingAddress2')) !!}
			</div>
		</div>

		<div class="form-group row">
			{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-sm-4 col-md-2 form-group">
				{!! Form::text('visitingAddressZip', @$orders->visitingAddressZip, array('class' => 'form-control visitingAddressZip')) !!}
			</div>
			<div class="col-sm-8 col-md-4 form-group">
				{!! Form::text('visitingAddressCity', @$orders->visitingAddressCity, array('class' => 'form-control visitingAddressCity')) !!}
			</div>
		</div>


		 <div class="form-group row">
           	{!! Form::label('ordered_by', __('main.ordered_by'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
            <div class="col-md-5">
                {!! Form::select('ordered_by',@$contacts ? @$contacts : [], @$orders->ordered_by,array('class'=>'form-control','placeholder'=>__('main.selected'))) !!}
            </div>
            <div class="col-md-1">
            	<a id="order_customer_order_by" data-toggle="modal" class="btn btn-link addnew_contact_btn order_by_quick_create" data-target="#addnew_contact" style="display:none" href="#" value=2><i class="fas fa-plus"></i></a>
            </div>

        </div>

		<div class="form-group row">
			{!! Form::label('pmtterms', __('main.pmt_terms'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('pmt_term',  @$pmt_terms, @$orders->pmt_term, array('class' => 'form-control pmt_term ','placeholder'=>__('main.selected'))) !!}
			</div>
		</div>
		<div class="form-group row">
			{!! Form::label('contact', __('main.contact'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::text('contact', @$orders->contact, array('class' => 'form-control contact')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group row">
			{!! Form::label('priority', __('main.priority'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('priority',@$priority,@$orders['priority'], array('class' => 'form-control','placeholder'=>__('main.selected'))) !!}
			</div>
		</div>
		<div class="form-group row">
			@if ($is_offer == 0)
				{!! Form::label('order_date', __('main.order_date'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			@else
				{!! Form::label('order_date', __('main.offer_date'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			@endif
			<div class="col-md-6">
				{!! Form::text('order_date', @$orders['order_date'], array('class'=>'form-control','required')) !!}
			</div>
		</div>
		<div class="form-group row">
			{!! Form::label('department', __('main.department'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('department_id', @$departments, @$orders['department_id'], array('class' => 'form-control','id' => 'department_id', 'placeholder' =>__('main.selected'))) !!}
			</div>
		</div>
		<div class="form-group row">
			{!! Form::label('order_user_id', __('main.assigned'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('order_user_id[]', @$users ? $users : [], @$selected_users,['multiple'=>'true','class'=>'form-control select2','id'=>'order_users'] ) !!}
			</div>
		</div>
		<div class="form-group row">
			{!! Form::label('ordered_created_by', __('main.ordered_created_by'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('ordered_created_by',@$department_chiefs, @$orders->id ? @$orders->order_user : Session::get('currentUserID'),array('class'=>'form-control', 'placeholder' => __('main.selected'))) !!}
			</div>
		</div>
		<div class="form-group row">
			{!! Form::label('status', __('main.status'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('status', @$is_offer == 0 ? @$order_status : $offer_status, @$orders->status,array('class'=>'form-control order_status')) !!}
			</div>
		</div>


		<div class="form-group row">
			{!! Form::label('date_completed', __('main.date_completed'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::text('date_completed', @$orders->date_completed, array('class'=>'form-control', 'id'=>'date_completed')) !!}
			</div>
		</div>

		@if ($is_offer == 1)
			<div class="form-group row">
				{!! Form::label('offer_due_date', __('main.offer_due_date'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
				<div class="col-md-6">
					{!! Form::text('offer_due_date', @$orders->offer_due_date, array('class'=>'form-control', 'id'=>'offer_due_date')) !!}
				</div>
			</div>
		@endif

		<div class="form-group row">
			{!! Form::label('deliveraddress', __('main.deliveraddress'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::select('deliveraddress', @$customer_address ? $customer_address : [], @$orders->deliveraddress, array('class' => 'form-control deliveraddress ','placeholder'=>__('main.selected'))) !!}
			</div>
		</div>



		<div class="form-group row">
			{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::text('deliveraddress1', @$orders->deliveraddress1, array('class' => 'form-control deliveraddress1')) !!}
			</div>
		</div>

		<div class="form-group row">
			{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-md-6">
				{!! Form::text('deliveraddress2', @$orders->deliveraddress2, array('class' => 'form-control deliveraddress2')) !!}
			</div>
		</div>

		<div class="form-group row">
			{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
			<div class="col-sm-4 col-md-2 form-group">
				{!! Form::text('deliveraddress_zip', @$orders->deliveraddress_zip, array('class' => 'form-control deliveraddress_zip')) !!}
			</div>
			<div class="col-sm-8 col-md-4 form-group">
				{!! Form::text('deliveraddress_city', @$orders->deliveraddress_city, array('class' => 'form-control deliveraddress_city')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-12">
        <div class="form-group row">
            {!! Form::label('comments', __('main.comments'), array('class' => 'col-md-2 col-form-label text-md-right')) !!}
            <div class="col-md-9">
                {!! Form::textarea('comments',@$orders->comments, array('class' => 'form-control','rows' => 3, 'maxlength' => 500)) !!}
            </div>
        </div>
        <div class="form-group row">
            {!! Form::label('order_invoice_comments',  (@$is_offer == 1) ? __('main.standard_offer_text') : __('main.order_invoice_comments'), array('class' => 'col-md-2 col-form-label text-md-right')) !!}
            <div class="col-md-9">
                {!! Form::textarea('order_invoice_comments', @$orders->id ? @$orders->order_invoice_comments : @$invoice_comments->data, array('class' => 'form-control','rows' => 5, 'maxlength' => 500)) !!}
            </div>
        </div>
        @if (@$orders->id && @$is_offer == 0)
	        <div class="form-group row">
	            <div class="offset-md-2 col-md-9">
					<a id="customer_sign_btn" class='btn btn-primary' data-toggle="modal" data-target='#customer-signature-pad' href="#">{!! __('main.customer_sign') !!}</a>
					{!! Form::hidden('customer_sign',@$orders->customer_sign,array('class'=>'form-control customer_signature', 'id'=>'customer_signature')) !!}
	            </div>
	        </div>
	        <div class="form-group row">
	            <div class="offset-md-2 col-md-9">
					@if (@$orders->customer_sign)
						<img src='{!!@$orders->customer_sign !!}' id='customerSignature'  width="250" height="100"/>
					@endif
	            </div>
	        </div>
        @endif
    </div>
</div>

@if (@$orders && @$orders->id && count(@$mailHistory) > 0)
	<div class="form-group">
	    <div class="accordion" id="historyAccordion">
	        <div class="card">
	            <div class="card-header" id="headingOne">
	                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseHistory">{!! __('main.history') !!}</button>	
	                <button type="button" class="btn btn-link float-right offer_product_collapse_btn" data-toggle="collapse" data-target="#collapseHistory" id="offer_product_collapse_btn"><i class="fa fa-plus"></i></button>
	            </div>
	            <div id="collapseHistory" class="collapse {!! @$disable_div_val !!}" aria-labelledby="headingOne" data-parent="#historyAccordion">
	                <div class="card-body">
	            		<table class='table table-striped table-hover' width="100%">
	            			<thead>
	            				<tr>
	            					<th width="20%">{!! __('main.historycreated') !!}</th>
	            					<th width="20%">{!! __('main.historycreatedby') !!}</th>
	            					<th width="20%">{!! __('main.status') !!}</th>
	            					<th width="40%">{!! __('main.comments') !!}</th>
	            				</tr>
	            			</thead>
	            			<tbody>
	            				@if (@$mailHistory)
	            					@foreach($mailHistory as $history)
	            					<tr>
		            					<td>
		            						@if(@$history->created_at)
												<?php echo date('d.m.Y H:s', strtotime($history->created_at)); ?>
											@endif
		            					</td>
		            					<td>{{ @$allUsers[$history->user_id] }}</td>
		            					<td>
		            						@if($is_offer == 0)
		            							{{ @$allstatus[$history->order_status] }}
		            						@else
		            							{{ @$allofferstatus[$history->order_status] }}
		            						@endif
		            					</td>
		            					<td>
		            						@if ($is_offer == 1) 
		            							{{ __('main.offer') }} {{ __('main.send') }} {{ __('main.to') }} {{ $history->email }}
		            						@else 
		            							{{ __('main.order') }} {{ __('main.send') }} {{ __('main.to') }} {{ $history->email }}
		            						@endif
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
@endif


<div class="col-l text-sm-center">
    @if (@$orders->id)
		<button name="update" value="update" type="submit" class="btn btn-primary order_update_btn btn-action">{!! $btn !!}</button>
		<button name="save_and_close"  type="submit" class="btn btn-primary btn-action">{!! __("main.update_and_close") !!}</button>
	@else
		<button type="submit" class="btn btn-primary order_submit_btn" name="order_submit_btn">{!! $btn !!}</button>
		<button type="submit" class="btn btn-primary order_submit_btn" name="order_submit_btn" value="close">{!! $btn_create_and_close !!}</button>
	@endif
	<a href="{!! @$is_offer == 0 ? route('main.order.index') : route('main.offer.index')!!}" class="btn btn-danger">{!!__('main.cancel') !!}</a>

	@if ($orders->id && $material_count == 0 && $is_offer == 0)
		<a class="float-right" href="{{ route('main.order.destroy', array($orders->id)) }}"
			data-method="delete"
		    data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.order')) !!}?" data-csrf="{!! csrf_token() !!}">
			<i class="fa fa-trash delete-icon"></i>
		</a>
	@endif
	{!! Form::hidden('order_submit_btn','',array('class'=>'form-control', 'id'=>'order_submit_btn_hidden')) !!}
</div>
	

<!-- Customer Signature -->
<div class="modal fade" id="customer-signature-pad" role="dialog" aria-labelledby="signatureLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal header-->
			<div class="modal-header">
				<h3 id="signatureLabel"><i class="icon-external-link"></i>{!! __('main.signature') !!}</h3>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
			</div>
			<!-- Modal body-->
			<div class="modal-body">
				<div class="m-signature-pad--body">
					<canvas></canvas>
				</div>
			</div>
			<!-- Modal footer-->
			<div class="m-signature-pad--footer">
				<div class="description">{!! __('main.signabove') !!}</div>
				<button type="button" class="button clear btn btn-danger " data-action="clear" id='custsign_clear'>{!! __('main.clear') !!}</button>
				<button type="button" class="button save btn btn-success" data-action="save" id='custsign_save'>{!! __('main.save') !!}</button>
			</div>
		</div>
	</div>
</div>

<!-- Customer Note -->
<a id="customer_note_modal_btn"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#customer_note" data-toggle="modal" style="display:none;">Test</a>
<div class="modal fade" id="customer_note" role="dialog" aria-labelledby="addNewModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            	{!! __('main.customer.customer_note') !!}
            </div>
            <div class="modal-body" id="customer_note_modal_content">
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-danger pull-left"  data-dismiss="modal">{!! __('main.ok') !!}</button>
            </div>
        </div>
    </div>
</div>


{!! Form::textarea('hidden_products', @$offer_order_products, array('class'=>'form-control hide_div','id'=>'hidden_products')) !!}
{!! Form::textarea('hidden_units', @$offer_order_units, array('class'=>'form-control hide_div','id'=>'hidden_units')) !!}
{!! Form::text('product_discount', @$product_discount ? number_format(@$product_discount,2, ',', '') : '', array('class'=>'form-control hide_div','id'=>'product_discount')) !!}
{!! Form::text('update_mail_btn_val', 0, array('class'=>'form-control hide_div','id'=>'update_mail_btn_val')) !!}
@if (@$orders->id)
	<a id="mail_update_button"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#mail_update_model" data-toggle="modal" style="visibility:hidden;"></a>

	<div class="modal fade" id="mail_update_model" role="dialog" aria-labelledby="addNewModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h2>{!! __('main.update_record_warning') !!}</h2>
                    <br><br>
                    <button type="button" class="btn btn-primary" id="mail_update_yes_btn" name="mail_send_yes_btn">{!! __('main.yes') !!}</button>
                    <button type="button" class="btn btn-danger" id="mail_update_no_btn" name="mail_send_no_btn" dataval="{!! @$orders->id !!}">{!! __('main.no') !!}</button>
                </div>
            </div>
        </div>
    </div>
@endif

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

<style type="text/css">
	.bootstrap-timepicker-widget {
    z-index: 100000 !important;
}

</style>
@section('page_js')
<script type='text/javascript'>
	var confirm_delete = "{!! __('main.deletefile') !!}";
	var token = "{!! csrf_token() !!}";
	var url = "{!! URL::to('/') !!}";
	var product = "{!! __('main.product') !!}";
	var qty = "{!! __('main.qty') !!}";
	var unit = "{!! __('main.unit') !!}";
	var price = "{!! __('main.price') !!}";
	var discount_text = "{!! __('main.discount') !!}";
	var sum_ex_vat = "{!! __('main.sum_ex_vat') !!}";
	var vat = "{!! __('main.vat') !!}";
	var usertype = "{!! Session::get('usertype') !!}";
	var user_id = "{!! Session::get('currentUserID') !!}";
	var ordered_created_by = "{!! @$orders->order_user !!}"
	var order_id = "{!! @$orders->id !!}";
	var offer_product_count = $('#offer_product_count').val();
	var delivery_date =  "{!! __('main.delivery_date') !!}";
	var disable_value = "{{ @$disable_value }}";
	if (offer_product_count == 1) {
		$('.offer_product_collapse_btn').trigger('click');
	}
	if (order_id) {
		$('.addnew_contact_btn').show();
		if (usertype == "Admin" || usertype == "Administrative" || usertype == "Department Chief" || user_id == ordered_created_by) {
		} else {
			$('#orderform input,select,textarea,.select2').attr('readonly', 'readonly');
			$('#orderform select,textarea,.select2,#customer_sign_btn').attr('disabled', 'disabled');
			$('#orderform').find("#comments,#status").attr('disabled', false);
			$('#orderform').find("#comments,#status").attr('readonly', false);
		}
	}
	var contact_person_id = '{{ @$orders->ordered_by }}';
	var user_department_route = "{!! route('main.orders.getUsersByDepartment') !!}";
	var order_contact_person_route = "{!! route('main.orders.getContactPersonsAndUsers') !!}";
	var customer_id = "{{ @$orders->customer_id }}";
	var customer_name = "{!! @$customerName !!}";
	var order_number = "{!! @$orders->order_number ? @$orders->order_number : @$orders->offer_number  !!}";
	var project = "{!! @$orders->project_number !!}";
	if (order_number) {
		var showText = order_number + " - " + customer_name.substring(0,10);
		if (project) {
			showText = showText  + " - " + project.substring(0,15)
		}
		$(".order_customer_label").text(showText);
	}

	$(".collapse.show").each(function(){
		$(this).prev(".card-header").find(".fa").addClass("fa-minus").removeClass("fa-plus");
	});
	$(".collapse").on('show.bs.collapse', function(){
		$(this).prev(".card-header").find(".fa").removeClass("fa-plus").addClass("fa-minus");
	}).on('hide.bs.collapse', function(){
		$(this).prev(".card-header").find(".fa").removeClass("fa-minus").addClass("fa-plus");
	});
	 $('.delivery_date').datetimepicker({
        format: 'DD.MM.YYYY',
        locale: 'en-gb'
    });

	$(".send_order_mail_btn, .download_order_report_btn").click(function() {
        $("#report_order_id").val($(this).attr('value'));
        $("#type").val($(this).attr('type'));
        $("#order_mail_report_form").submit();
    });
	var stockUrl = "{!! route('main.product.getOnstockDetails') !!}";
	if (disable_value == 1) {
	  	$('#orderform input,textarea,select,a,.select2').attr('readonly', true);
        $('#orderform a,.select2,select').attr('disabled', true);
        $('#orderform .btn-action').attr('disabled', true);
        $('#orderform .addnew_contact_btn ').hide();
	}
</script>
	{!! Html::script('js/order.v2.js') !!}
	{!! Html::script('js/offer_order.v1.js') !!}
@endsection