<div class="table-responsive">
	<table class="table table-striped table-hover" id='product_location_table'>
        <thead>
            <tr>
                <th width="20%">{!!__('main.warehouse') !!}</th>
            	<th width="20%">{!!__('main.location') !!}</th>
                <th width="20%"></th>
                <th width="20%"></th>
                <th width="20%"></th>
			</tr>
        </thead>
        <tbody>
            @if(@$productLocations) 
                @foreach (@$productLocations as $location)
                    <tr> 
                        <td>
                            {{ @$warehouses[$location->warehouse_id] }}
                        </td>
                        <td>
                            {{ @$locations[$location->location_id] }}
                        </td>
                        <td class="delete-td">
                            <a href="{{ route('main.productLocation.destroy', array($location->id)) }}"
                            data-method="delete"
                            data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(__('main.location')) !!}?" data-csrf="{!! csrf_token() !!}">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>   
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
