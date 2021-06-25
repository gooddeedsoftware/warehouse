<div class="card card-secondary">
	<div class="card-header">
		<b>{!!__('main.contact') !!}</b>
		<a class="btn btn-primary float-right openModal" href="javascript:;" data-id="" data-href="{!! route('main.contact.loadContactView') !!}" form-name="contactform">{!! __('main.add') !!}</a>
	</div>
	<div class="card-body">
		<div class="table-responsive">
       		<table class="table table-striped table-hover" id='customer_table'>
                <thead>
                    <tr>
                    	<th>{!!__('main.name') !!}</th>
                    	<th>{!!__('main.title') !!}</th>
						<th>{!!__('main.email') !!}</th>
						<th>{!!__('main.mobile') !!}</th>
						<th>{!!__('main.phone') !!}</th>
						<th></th>
					</tr>
                </thead>
                <tbody>
                	@if (count(@$customer->contact) > 0)
	                    @foreach($customer->contact as $contact)
	                    <tr>
	                        <td>
	                        	<a class="openModal" href="javascript:;" data-id="{{ @$contact->id }}" data-href="{!! route('main.contact.loadContactView') !!}" form-name="contactform">  {{ $contact->name }} </a>
	                        	
	                        </td>
	                         <td>
	                        	{{ @$contact->title }}
	                        </td>
	                        <td>
	                        	{{ $contact->email }}
	                        </td>
	                        <td>
	                        	{{ $contact->mobile }}
	                        </td>
	                        <td>
	                        	{{ $contact->phone }}
	                        </td>
	                        <td class="delete-td">
								<a href="{{ route('main.contact.delete', array($contact->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.contact')) !!}?" data-csrf="{!! csrf_token() !!}"> 
									<i class="fas fa-trash-alt"></i>
								</a>
	                       	</td>
	                    </tr>
	                    @endforeach
                    @endif
                </tbody>
            </table>
		</div>
	</div>
</div>
