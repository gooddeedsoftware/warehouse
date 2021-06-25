<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tbody>
            <tr>
                <th>
                    {{  trans('main.warehouse')  }}
                </th>
                <th>
                    {{ trans('main.location') }}
                </th>
                <th>
                    {{  trans('main.onstock')  }}
                </th>
                <th>
                    {{  trans('main.customer_order') }}
                </th>
                <th>
                    {{ trans('main.available')  }}
                </th>
            </tr>
            @foreach($warehouse_product_details as $warehouse_product) 
            	<tr>
            		<td> 
            			@if (@$warehouseStatus == 1 && ($usertype == "Admin" || $usertype == "Administrative" || in_array($warehouse_product->warehouse_id, @$user_warehouse)))
            				<a href="javascript:;" class="setWarehouseandLocation" unique_id="{{ @$unique_id }}" location-id="{{ $warehouse_product->location_id }}" warehouse-id="{{ $warehouse_product->warehouse_id }}">{{ $warehouse_product->shortname }}</a>
            			@else
        					{{ $warehouse_product->shortname }}
            			@endif
            		</td>
            		<td> {{ $warehouse_product->location }}</td>
            		<td> {{ number_format($warehouse_product->balance, 2, ',', ' ') }}</td>
            		<td> {{ number_format($warehouse_product->customer_order, 2, ',', ' ') }}</td>
            		<td> {{ number_format($warehouse_product->available_qty, 2, ',', ' ') }}</td>
            	</tr>
            @endforeach
        </tbody>
    </table>
</div>
