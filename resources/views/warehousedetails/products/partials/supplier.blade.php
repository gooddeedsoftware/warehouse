<div class="table-responsive">
	<table class="table table-striped table-hover" id='customer_table'>
        <thead>
            <tr>
                <th>{!!__('main.is_main') !!}</th>
            	<th>{!!__('main.supplier') !!}</th>
                <th>{!!__('main.articlenumber') !!}</th>
                <th>{!!__('main.articlename') !!}</th>
                <th>{!!__('main.currency') !!}</th>
                <th>{!!__('main.supplier_price') !!}</th>
                <th>{!!__('main.supplier_discount') !!}</th>
                <th>{!!__('main.discount') !!}</th>
                <th>{!!__('main.realcost') !!}</th>
            	<th>{!!__('main.realcost_nok') !!}</th>
				<th></th>
			</tr>
        </thead>
        <tbody>
            @if(@$productSuppliers) 
                @foreach (@$productSuppliers as $productSupplier)
                    <tr> 
                        <td>
                            @if( @$productSupplier->is_main == '1')
                                <i class="fa fa-check-square"></i>
                            @endif
                        </td>
                        <td>
                            <a class="openModal" href="javascript:;" data-id="{{ @$productSupplier->id }}" data-href="{!! route('main.productSupplier.loadview') !!}" form-name="productSupplierform">
                                {{  @$suppliers[@$productSupplier->supplier] }}
                            </a>
                        </td>
                        <td>
                            {{  @$productSupplier->articlenumber }}
                        </td>
                        <td>
                            {{  @$productSupplier->articlename }}
                        </td>
                        <td>
                            {{  @$productSupplier->curr_iso_name }}
                        </td>
                        <td>
                            {!! number_format(@$productSupplier->supplier_price, 2, ",", "") !!}
                        </td>
                        <td>
                            {!! number_format(@$productSupplier->supplier_discount, 2, ",", "") !!}
                        </td>
                        <td>
                            {!! number_format(@$productSupplier->discount, 2, ",", "") !!}
                        </td>

                        <td>
                            {!! number_format(@$productSupplier->realcost, 2, ",", "") !!}
                        </td>

                        <td>
                            {!! number_format(@$productSupplier->realcost_nok, 2, ",", "") !!}
                        </td>
                        <td class="delete-td">
                            <a href="{{ route('main.productSupplier.delete', array($productSupplier->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.supplier')) !!}?" data-csrf="{!! csrf_token() !!}"> 
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
