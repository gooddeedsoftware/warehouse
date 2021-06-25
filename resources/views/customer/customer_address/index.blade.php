<div class="card card-secondary">
	<div class="card-header">
		<b>{!!__('main.address') !!}</b>
		<a class="btn btn-primary float-right openModal" href="javascript:;" data-id="" data-href="{!! route('main.customerAddress.loadCustomerAddressView') !!}" form-name="customerAddressform">{!! trans('main.add') !!}</a>
	</div>
	<div class="card-body">
		<div class="table-responsive">
       		<table class="table table-striped table-hover" id='customer_table'>
                <thead>
					<tr>
						<th>{!! __('main.main_address') !!}</th>
						<th>{!!__('main.type') !!}</th>
						<th>{!!__('main.address') !!}</th>
						<th></th>
						<th>{!!__('main.zip') !!}</th>
						<th>{!!__('main.city') !!}</th>
						<th>{!!__('main.country') !!}</th>
						<th></th>
					</tr>
                </thead>
                <tbody>
                	@if (count(@$customer->address) > 0)
                		<?php $s = 0; ?>
	                    @foreach($customer->address as $address)
		                    <tr>
		                    	<td>
		                    		<input type="radio" name="main" class="main_radio" value="{!! @$address->id !!}" {!! @$address->main ? 'checked="checked"' : "" !!}>
		                    	</td>
		                        <td>
		                        	<a class="openModal" href="javascript:;" data-id="{{ @$address->id }}" data-href="{!! route('main.customerAddress.loadCustomerAddressView') !!}" form-name="customerAddressform">  {{ @$customer_address_types[$address->type] }} </a>
		                        </td>
		                        <td>
		                        	{{ $address->address1 }}
		                        </td>
		                        <td>
		                        	{{ $address->address2 }}
		                        </td>
		                        <td>
		                        	{{ $address->zip }}
		                        </td>
	                         	<td>
		                        	{{ $address->city }}
		                        </td>
	                         	<td>
		                        	{{ @$countries[$address->country] }}
		                        </td>
		                        <td class="delete-td">
		                        	@if (@$address->main == '1')
									@else
										@if (!@$address->main && @$s == 0)
										@else
											<a href="{{ route('main.customerAddress.delete', array($address->id)) }}" data-method="delete" data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.address')) !!}?" data-csrf="{!! csrf_token() !!}"> 
												<i class="fas fa-trash-alt"></i>
											</a>
										@endif
									@endif
									
		                       	</td>
		                    </tr>
	                       	<?php $s++; ?>
	                    @endforeach
                    @endif
                </tbody>
            </table>
		</div>
	</div>
</div>
