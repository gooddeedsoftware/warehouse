<div class="table-responsive">
    <table class="table order_material_Table" id="order_material_Table" style="" >
        <thead>
            <tr>
                <th width="1%"></th>
                <th width="1%"></th>
                <th width="10%">
                    <a>{!!__('main.product_number') !!}</a>
                </th>
                <th width="12%">
                    <a>{!!__('main.description') !!}</a>
                    
                </th>
                <th width="4%" >
                    <a>{!!__('main.order_quantity') !!}</a>
                </th>
                <th width="5%" >
                    <a>{!!__('main.unit') !!}</a>
                </th>
                <th width="4%" >
                    <a>{!!__('main.cost_price') !!}</a>
                </th>
                <th width="4%" >
                    <a>{!!__('main.price') !!}</a>
                </th>
                <th width="4%" >
                    <a>{!!__('main.discount') !!}</a>
                </th>
                <th width="5%" >
                    <a>{!!__('main.sum_ex_vat') !!}</a>
                </th>

                <th width="4%" >
                    <a>{!!__('main.dg') !!}</a>
                </th>

                <th width="4%" >
                    <a>{!!__('main.vat') !!}</a>
                </th>

                <th width="5%">
                    <a>{!! __('main.delivery_date') !!}</a>
                </th>
                <th width="5%" >
                    <a>{!!__('main.return') !!}</a>
                </th>
                <th width="7%">
                    <a>{!!__('main.warehouse') !!}</a>
                </th>
                <th width="7%">
                    <a>{!! __('main.location') !!}</a>
                </th>
                <th width="5%" >
                    <a>{!! __('main.picked_quantity') !!}</a>
                </th>

                <th width="3%">
                    <a>{!! __('main.invoice_quantity') !!}</a>
                </th>
                <th width="5%">
                     <a>{!! __('main.shipping_status') !!}</a>
                </th>
                <th width="1%"></th>
                <th width="1%"></th>
                <th width="1%"></th>
            </tr>
        </thead>
        <tbody>
            @if (@$order_materials)
                @foreach (@$order_materials as $product)
                    @if ($product->is_text == 1) 
                        <tr material_id="{{ $product->id }}">    
                            <td class='product_move'>
                                <i class="fa fa-arrows handle"></i>
                            </td>

                            <td>
                                @if( @$product->approved_product == '1')
                                    <i class="fa fa-check-square"></i>
                                @endif
                            </td>
                            <td colspan="15">
                                @if( @$product->approved_product == '1')
                                    {{ $product->product_text }}
                                @else
                                    <input type="text" class="product_text form-control" name="product_text" value=' {{ $product->product_text }} '>
                                @endif
                            </td>

                            <td></td>

                            <td colspan="2">
                                @if (@$product->shippment_id)
                                     {{ $product->shippment_id }}
                                @else 
                                    <i class="fa fa-times"></i>
                                @endif
                            </td>
                            <td>
                                @if (@$product->approved_product != '1')
                                    <a href="{{ route('main.ordermaterial.destroy', array($product->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" data-csrf="{!! csrf_token() !!}"><i class="delete-icon fa fa-trash"></i></a>
                                @endif
                            </td>
                            <td></td>

                            <td class='save_content_td' style='display:none;'>
                                @if (@$product->approved_product != '1')
                                    <button type='button' class='btn btn-primary form-control save_text' data-val='{{ $product->id }}' onclick='saveText(this);'>save</button>
                                @else 
                                    <button type='button' class='btn btn-primary form-control updateSortOrder' onclick='updateSortOrder(this, "{{ $product->id }}");'>sort</button>
                                @endif
                            </td>
                        </tr>
                    @elseif (@$product->is_logistra == 1)
                        @if (@$product->description != "Hentes")
                            <tr material_id="{{ $product->id }}" from-index="1">    
                                <td class='product_move'>
                                    <i class="fa fa-arrows handle"></i>
                                </td>
                                <td class="approve_product_td">
                                    @if( @$product->approved_product != '1')
                                        <input type="hidden" class="approve_product" name="approve_product" value="1">
                                    @else
                                        <i class="fa fa-check-square"></i>
                                    @endif
                                </td>
                                <td class="product_td">
                                    {!! @$product->product_number !!} 
                                </td>

                                <td>
                                    {!! @$product->description !!}
                                    @if (@$product->track_number)
                                        <br>
                                        {{ @$product->track_number }}
                                    @endif
                                </td>

                                <td class="order_quantity_td">
                                    <label class='labelorderQuantity'>{!! number_format(@$product->order_quantity, 2, ",", "") !!}</label>
                                </td>

                                <td>
                                    {{ @$units[@$product->unit] }}
                                </td>
                                <td>{!! number_format(@$product->cost_price, 2, ",", "") !!}</td>
                                <td>{!! number_format(@$product->offer_sale_price, 2, ",", "") !!}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    @if (@$product->shippment_id)
                                        {{ $product->shippment_id }}
                                    @else 
                                        <i class="fa fa-times"></i>
                                    @endif
                                </td>
                                <td>
                                    @if (@$product->approved_product != '1')
                                        <a href="{{ route('main.ordermaterial.destroy', array($product->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" data-csrf="{!! csrf_token() !!}"><i class="delete-icon fa fa-trash"></i></a>
                                    @endif
                                </td>
                                <td>
                                    @if (@$product->shippment_id && @$product->shippment_id != 1) 
                                        <a  href="{!! route('main.downloadShipmentLabel', array(@$product->shippment_id)) !!}" class="download_btn" value="{!! @$product->shippment_id !!}">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @elseif (@$product->is_package == 0)
                        <tr class="locationTrigger order_material_tr" material_id="{{ $product->id }}" from-index="1">
                            <input type="hidden" class="order_offer_product_id" name="order_offer_product_id" value="{!! @$product->order_offer_product_id !!}">
                            <input type="hidden" class="stockable" value="{{ @$product->stockable }}" />
                            <td class='product_move'>
                                <i class="fa fa-arrows handle"></i>
                            </td>
                            <td class="approve_product_td">
                                @if( @$product->approved_product != '1')
                                    <input type="hidden" class="approve_product" name="approve_product" value="1">
                                @else
                                    <i class="fa fa-check-square"></i>
                                @endif
                            </td>

                            <td class="product_td">
                                {!! @$product->prod_nbr != null && @$product->prod_nbr != '' ? @$product->prod_nbr  :  @$product->product_number !!}
                                <input type="hidden" class="product product_number produt_text" name="product_number" value="{!! @$product->product_id !!}">
                                <input type="hidden" class="prod_nbr" name="prod_nbr" value="{!! @$product->prod_nbr !!}">
                            </td>

                            <td>
                                
                                @if( @$product->approved_product == '1')
                                    {!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}
                                @else
                                    <input class='product_description form-control' value="{!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}" />
                                @endif

                            </td>

                            <td class="order_quantity_td">
                                @if (@$product->approved_product != '1' && (@$product->quantity == 0 || @$product->order_quantity != @$product->quantity))
                                    <input class="order_quantity order_quantity_single form-control numberWithSingleComma" onchange="showSaveButton(this)" data-val='{!! number_format(@$product->order_quantity, 2, ",", "") !!}' type="text" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'>
                                    <label class='labelorderQuantity hide_div'>test</label>
                                @else
                                    <input class="order_quantity order_quantity_single form-control hide-div" onchange="showSaveButton(this)" data-val='{!! number_format(@$product->order_quantity, 2, ",", "") !!}' type="text" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'>
                                    <label class='labelorderQuantity'>{!! number_format(@$product->order_quantity, 2, ",", "") !!}</label>
                                @endif
                            </td>

                            <td class="unit_td">
                                @if( @$product->approved_product == '1')
                                    {!!  @$units[@$product->unit]!!}
                                @else
                                    {!! Form::select('unit', @$units, @$product->unit, array('class'=>'form-control unit','placeholder' => __('main.selected'))) !!}
                                @endif
                            </td>

                            <td class="cost_price_td">
                                 @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->cost_price, 2, ",", "") !!}
                                @else
                                     <input class="cost_price form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->cost_price, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="price_td">
                                 @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->offer_sale_price, 2, ",", "") !!}
                                @else
                                     <input class="price form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->offer_sale_price, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="discount_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->discount, 2, ",", "") !!}
                                @else
                                      <input class="discount form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->discount, 2, ",", "") !!}' />
                                @endif
                            </td>


                            <td class="sum_ex_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}
                                @else
                                      <input class="sum_ex_vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="dg_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->dg, 2, ",", "") !!}
                                @else
                                      <input class="dg form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->dg, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="vat_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}
                                @else
                                      <input class="vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->vat, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="delivery_date_td">
                                @if (@$product->approved_product != '1')
                                    {!! Form::text('delivery_date', @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '', array('class' => 'form-control delivery_date position-relative ')) !!}
                                @else 
                                    {{  @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '' }}
                                @endif
                            </td>

                            <td>
                                {!! number_format(@$product->return_quantity, 2, ",", "") !!}
                            </td>

                            <td class="warehouse_td">
                                @if (@$product->stockable == 1)
                                    @if (@$product->warehouse != null && @$product->warehouse != 'Select' && @$product->warehouse != 'Velg')
                                        <input type="hidden" class="warehouse" name="warehouse" value="{!! @$product->warehouses->id !!}">
                                        {!! @$product->warehouses->shortname !!}
                                    @else
                                        {!! Form::select('warehouse', @$warehouse_dropdown_array, @$product->warehouse, array('class'=>'select2 form-control warehouse','onchange' => 'getProductDetailsForMaterials(this, 2);', 'placeholder' => __('main.selected'))) !!}
                                        <label class='labelWarehouse hide_div'></label>
                                    @endif
                                @endif
                            </td>

                            <td class="location_td">
                                @if (@$product->stockable == 1)
                                    <label class='labelLocation hide_div'></label>
                                    @if (@$product->location != null && @$product->location != 'Select' && @$product->location != 'Velg')
                                        <input type="hidden" class="location" name="warelocationhouse" value="{!! @$product->warehouseLocation->id !!}">
                                        {!! @$product->warehouseLocation->name !!}
                                    @else
                                        <select class='select2 form-control location' onchange='showPickedQuantity(this, 1);'></select>
                                    @endif
                                @endif
                            </td>

                            <td class="quantity_td">

                                @if (@$product->stockable == 1)
                                    @if (@$product->location != null && @$product->location != 'Select' && @$product->location != 'Velg')
                                        @if (@$product->approved_product != '1' && @$product->quantity == 0)
                                            <input type="text" class="quantity form-control" onchange="getSerialNumberForOrderMaterial(this, 1)"  name="quantity" value='{!! number_format(@$product->quantity, 2, ",", "") !!}'>
                                            <label class='labelQuantity hide_div'></label>
                                        @else
                                            <input type="hidden" class="quantity" name="quantity" value='{!! number_format(@$product->quantity, 2, ",", "") !!}'>
                                            {!! number_format(@$product->quantity, 2, ",", "") !!}
                                        @endif
                                    @else
                                        <input type="text" class="quantity form-control hide_div" onchange="getSerialNumberForOrderMaterial(this, 1)"  name="quantity">
                                        <label class='labelQuantity hide_div'></label>
                                    @endif
                                @else
                                     {!! number_format(@$product->quantity, 2, ",", "") !!}
                                @endif
                            </td>
                            
                            <td class='invoice_qty_td'>
                                @if (@$product->invoiced || @$product->approved_product)
                                    <p class="product_invoice_label">
                                        {!! number_format(@$product->invoice_quantity, 2, ",", "") !!}
                                    </p>
                                @elseif (@$product->order_quantity ==  @$product->quantity || @$product->order_quantity >= 1)
                                    <p class="product_invoice_label" >{!! number_format(@$product->invoice_quantity, 2, ",", "") !!}</p>
                                    <div class="product_invoice_div" style="display: none;">
                                        <input type="text" class="product_invoice_quantity_text form-control" value='{!! number_format(@$product->quantity, 2, ",", "") !!}'>
                                    </div>
                                @else
                                    <p class="product_invoice_label" ></p>
                                    <div class="product_invoice_div" style="display: none;">
                                        <input type="text" class="product_invoice_quantity_text form-control" value='{!! number_format(@$product->invoice_quantity, 2, ",", "") !!}'>
                                    </div>
                                @endif

                            </td>
                            
                            <td>
                                @if (@$product->shippment_id)
                                    {{ $product->shippment_id }}
                                @else 
                                    <i class="fa fa-times"></i>
                                @endif
                            </td>
                            <td class='info_td'>
                                <a class='stock_info_btn' type='button' onclick='showStockInfo(this);' unique_id="{{ App\Helpers\GanticHelper::gen_uuid() }}"><i class='fa fa-info-circle'></i>
                                </a>
                            </td>
                            <td>
                                @if (@$product->approved_product != '1')
                                    <a href="{{ route('main.ordermaterial.destroy', array($product->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" data-csrf="{!! csrf_token() !!}"><i class="delete-icon fa fa-trash"></i></a>
                                @endif
                            </td>

                            <td></td>

                            {{-- hidden td --}}
                            <td class="update_td" style="display: none;">
                                @if(@$product->approved_product != '1')
                                    <button type='button' data-val="{!! @$product->id !!}" class='btn btn-primary form-control update_product' save_val=1 style="display:none" onclick='updateOrderMaterialData(this);'>{!! __('main.update') !!}</button>
                                @else 
                                    <button type='button' class='btn btn-primary form-control updateSortOrder' onclick='updateSortOrder(this, "{{ $product->id }}");'>sort</button>
                                @endif
                            </td>
                        </tr>
                    <!-- For Pacakage Products -->
                    @else 
                        @php
                            $invoice_quantity_status = 1;
                        @endphp
                        @foreach($product->package_contents as $pacakge)
                            @php
                                if(@$pacakge->order_quantity != @$pacakge->quantity) {
                                    $invoice_quantity_status = 0;
                                    break;
                                }
                            @endphp
                        @endforeach
                        <tr  class='order_material_tr' data-val="{!!$product->id !!}" material_id="{{ $product->id }}" from-index="1">
                            <input type="hidden" class="order_offer_product_id" name="order_offer_product_id" value="{!! @$product->order_offer_product_id !!}">
                            <input type='hidden' id='is_package' value='1' />
                            <input type="hidden" class="stockable" value="1" />

                            <td class='product_move'>
                                <i class="fa fa-arrows"></i>
                            </td>

                            <td class="approve_product_td">
                                @if(@$product->approved_product == '1')
                                    <i class="fa fa-check-square"></i>
                                @endif
                            </td>


                            <td class="product_td">
                                {!! @$product->prod_nbr != null && @$product->prod_nbr != '' ? @$product->prod_nbr  :  @$product->product_number !!}
                                <input type="hidden" class="product product_number produt_text" name="product_number" value="{!! @$product->product_id !!}">
                                <input type="hidden" class="prod_nbr" name="prod_nbr" value="{!! @$product->prod_nbr !!}">
                            </td>

                            <td>
                                
                                @if( @$product->approved_product == '1')
                                    {!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}
                                @else
                                    <input class='product_description form-control' value="{!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}" />
                                @endif

                            </td>



                           

                            <td class="order_quantity_td">
                                @php
                                    $order_quantity_status = 0;
                                @endphp
                                @foreach($product->package_contents as $pacakge)
                                    @php
                                        if(@$pacakge->quantity > 0) {
                                            $order_quantity_status = 1;
                                        }
                                    @endphp
                                @endforeach
                                @if(@$product->approved_product == 1 || @$product->invoice_quantity > 0)
                                    {!! number_format(@$product->order_quantity, 2, ",", "") !!}
                                    <input type="hidden" name="order_quantity" class="order_quantity" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'>
                                @elseif ($order_quantity_status == 1)
                                    {!! number_format(@$product->order_quantity, 2, ",", "") !!}
                                    <input type="hidden" name="order_quantity" class="order_quantity" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'>
                                @else
                                    <input type='text'  class='form-control order_quantity' onchange='updatePackageProductsQuantity(this);' value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}'/><label class='labelorderQuantity hide_div'>test</label>
                                @endif
                            </td>


                            <td class="unit_td">
                                @if( @$product->approved_product == '1')
                                    {!!  @$units[@$product->unit]!!}
                                @else
                                    {!! Form::select('unit', @$units, @$product->unit, array('class'=>'form-control unit','placeholder' => __('main.selected'))) !!}
                                @endif
                            </td>

                            <td class="cost_price_td">
                                 @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->cost_price, 2, ",", "") !!}
                                @else
                                     <input class="cost_price form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->cost_price, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="price_td">
                                 @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->offer_sale_price, 2, ",", "") !!}
                                @else
                                     <input class="price form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->offer_sale_price, 2, ",", "") !!}' />
                                @endif
                            </td>
                            <td class="discount_td">

                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->discount, 2, ",", "") !!}
                                @else
                                      <input class="discount form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->discount, 2, ",", "") !!}' />
                                @endif
                            </td>
                            <td class="sum_ex_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}
                                @else
                                      <input class="sum_ex_vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}' />
                                @endif
                            </td>

                            <td class="dg_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->dg, 2, ",", "") !!}
                                @else
                                      <input class="dg form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->dg, 2, ",", "") !!}' />
                                @endif
                            </td>


                            <td class="vat_td">
                                @if( @$product->approved_product == '1')
                                    {!! number_format(@$product->sum_ex_vat, 2, ",", "") !!}
                                @else
                                      <input class="vat form-control text-align-right numberWithSingleComma" type="text" value='{!! number_format(@$product->vat, 2, ",", "") !!}' />
                                @endif
                            </td>


                            <td class="delivery_date_td">
                                @if (@$product->approved_product != '1')
                                    {!! Form::text('delivery_date', @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '', array('class' => 'form-control delivery_date position-relative ')) !!}
                                @else 
                                    {{  @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '' }}
                                @endif
                            </td>
                            <td>{!! number_format(@$product->return_quantity,"2", ",", "") !!}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class='invoice_qty_td'>
                                @if (@$product->approved_product == '1')
                                    {!! number_format(@$product->invoice_quantity, 2, ",", "") !!}
                                @elseif (@$product->quantity > 0)
                                    <div class='product_invoice_div' style='display: none;'><input type='text' onchange='updatePackageInvoiceQuantity(this);' onkeypress='return isNumber(event);'  class='product_invoice_quantity_text form-control' data-val='{!! number_format(@$product->quantity, 2, ",", "") !!}' value='{!! number_format(@$product->quantity, 2, ",", "") !!}'></div><label class='labelQuantity product_invoice_label hide_div'></label>
                                    <label class='labelQuantity product_invoice_label'>
                                        {!! number_format(@$product->quantity, 2, ",", "") !!}
                                    </label>
                                @else
                                    @if ($invoice_quantity_status == 0)
                                        <div class='product_invoice_div' style='display: none;'><input type='text' onchange='updatePackageInvoiceQuantity(this);' class='product_invoice_quantity_text form-control'></div><label class='labelQuantity package_invoice_label hide_div'></label>
                                    @else
                                        <div class='product_invoice_div'><input type='text' onchange='updatePackageInvoiceQuantity(this);' class='product_invoice_quantity_text form-control'></div><label class='labelQuantity package_invoice_label hide_div'></label>
                                    @endif
                                @endif
                            </td>

                            <td>
                                @if (@$product->shippment_id)
                                    {{ $product->shippment_id }}
                                @else 
                                    <i class="fa fa-times"></i>
                                @endif
                            </td>
                            <td></td>
                            <td>
                                @if (@$product->approved_product != '1')
                                    <a href="{{ route('main.ordermaterial.destroy', array($product->id)) }}" data-method="delete" data-modal-text="{!!__('main.deletemessage') !!} {!!strtolower(__('main.product')) !!}?" data-csrf="{!! csrf_token() !!}"><i class="delete-icon fa fa-trash"></i></a>
                                @endif
                            </td>
                            <td></td>

                            {{-- hidden td --}}
                            <td class="update_td hide_div">
                                @if (@$product->approved_product != '1')
                                    <button type='button' class='btn btn-primary form-control update_product' data-val="{!! @$product->id !!}" save_val=1 style="display:none" onclick='updatePackageOrderMaterialData(this);'>{!! __('main.update') !!}</button>
                                @else 
                                    <button type='button' class='btn btn-primary form-control updateSortOrder' onclick='updateSortOrder(this, "{{ $product->id }}");'>sort</button>
                                @endif
                            </td>
                        </tr>
                        @if(@$product->package_contents != '')
                            @php $i = 0; @endphp
                            @foreach(@$product->package_contents as $product)
                                <tr class='order_material_tr locationTrigger' data-val="{!! $product->reference_id !!}" material_id="{{ $product->id }}" from-index="1">
                                    <input type='hidden' id='is_content' value='1' />
                                    <input type='hidden' id='reference_id' value="{!! $product->reference_id  !!}" />
                                    <input type='hidden' id='package_quantity' value="{!! @$product->package_quantity !!}"/>
                                    <input type='hidden' id='sort_number' value="{!! @$product->sort_number !!}"/>
                                    <input type="hidden" class="stockable" value="1"/>
                                    <td></td>
                                    <td></td>

                                    <td class="product_td">
                                        {!! @$product->prod_nbr != null && @$product->prod_nbr != '' ? @$product->prod_nbr  :  @$product->product_number !!}
                                        <input type="hidden" class="product product_number produt_text" name="product_number" value="{!! @$product->product_id !!}">
                                        <input type="hidden" class="prod_nbr" name="prod_nbr" value="{!! @$product->prod_nbr !!}">
                                    </td>

                                    <td>
                                        
                                        @if( @$product->approved_product == '1')
                                            {!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}
                                        @else
                                            <input class='product_description form-control' value="{!! @$product->product_description != null && @$product->product_description != '' ? @$product->product_description  : @$product->description !!}" />
                                        @endif

                                    </td>

                                    <td class="order_quantity_td">
                                        @if (@$product->quantity > 0)
                                            <label class='labelorderQuantity'>{!! number_format(@$product->order_quantity, 2, ",", "") !!}</label>
                                            <input type="hidden" name="order_quantity" value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}' class="order_quantity">
                                        @else
                                            <input type='text' readonly='readonly' onchange='showSaveButton(this, 1);' data-val='{!! number_format(@$product->package_quantity, 2, ",", "") !!}' value='{!! number_format(@$product->order_quantity, 2, ",", "") !!}' class='form-control order_quantity' disabled="disabled" /><label class='labelorderQuantity hide_div'>test</label>
                                        @endif
                                    </td>
                                    <td class="unit_td">
                                    </td>
                                    <td class="cost_price_td">
                                    </td>
                                    <td class="price_td">
                                    </td>
                                    <td class="discount_td">
                                    </td>
                                    <td class="sum_ex_td">
                                    </td>
                                    <td class="dg_td">
                                    </td>
                                    <td class="vat_td">
                                    </td>
                                    <td class="delivery_date_td">
                                        @if (@$product->approved_product != '1')
                                            {!! Form::text('delivery_date', @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '', array('class' => 'form-control delivery_date position-relative ')) !!}
                                        @else 
                                            {{  @$product->delivery_date ? date('d.m.Y', strtotime($product->delivery_date)) : '' }}
                                        @endif
                                    </td>

                                    <td>
                                        {!! number_format(@$product->return_quantity, 2, ",", "") !!}
                                    </td>
                                    <td class="warehouse_td">
                                        @if (@$product->warehouse != null && @$product->warehouse != 'Select' && @$product->warehouse != 'Velg')
                                            <input type="hidden" class="warehouse" name="warehouse" value="{!! @$product->warehouses->id !!}">
                                            {!! @$product->warehouses->shortname !!}
                                        @else
                                            {!! Form::select('warehouse', @$warehouse_dropdown_array, @$product->warehouse, array('class'=>'select2 form-control warehouse','onchange' => 'getProductDetailsForMaterials(this, 2);', 'placeholder' => __('main.selected'))) !!}
                                            <label class='labelWarehouse hide_div'>test</label>
                                        @endif

                                    </td>
                                    <td class="location_td">
                                        <label class='labelLocation hide_div'>test</label>

                                        @if (@$product->location != null && @$product->location != 'Select' && @$product->location != 'Velg')
                                            <input type="hidden" class="location" name="warelocationhouse" value="{!! @$product->warehouseLocation->id !!}">
                                            {!! @$product->warehouseLocation->name !!}
                                        @else
                                            <select class='select2 form-control location ' onchange='showPickedQuantity(this, 1);'></select>
                                        @endif
                                    </td>
                                    
                                    <td class="quantity_td">
                                        @if (@$product->location != null && @$product->location != 'Select' && @$product->location != 'Velg')
                                            @if(@$product->quantity == 0)
                                                <input type='text' class='form-control quantity' data-val=0  value='{!! number_format(@$product->quantity, 2, ",", "") !!}'  onchange='getSerialNumberForOrderMaterial(this, 1, false, false,false,1)'/>
                                            @else
                                                <input type="hidden" class="quantity" name="quantity" data-val='{!! number_format(@$product->quantity, 2, ",", "") !!}'  value='{!! number_format(@$product->quantity, 2, ",", "") !!}'>
                                                <label class='labelQuantity'> {!! number_format(@$product->quantity, 2, ",", "") !!}</label>
                                               
                                            @endif
                                        @else
                                            <input type='text' class='form-control quantity hide_div' data-val=0    onchange='getSerialNumberForOrderMaterial(this, 1, false, false,false,1)'/>
                                            <label class='labelQuantity hide_div'>test</label>
                                        @endif
                                    </td>
                                    <td class='invoice_qty_td'>
                                        @if (@$product->quantity > 0)
                                            <label class='ContentlabelQuantity'>0</label>
                                        @else
                                            <label class='ContentlabelQuantity hide_div'>0</label>
                                        @endif
                                    </td>
                                    <td></td>
                                    <td class='info_td'>
                                        <a class='stock_info_btn' type='button' onclick='showStockInfo(this);' unique_id="{{ App\Helpers\GanticHelper::gen_uuid() }}"><i class='fa fa-info-circle'></i>
                                        </a>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td  class='update_content_td' style='display:none;'>
                                        @if ($product->approved_product != 1)
                                            <button type='button'  class='btn btn-primary form-control update_product' id="update_{!! $product->reference_id !!}_{!! $i !!}" data-val="{!! @$product->id !!}" save_val=1 is_content=1 style="display:none" onclick='updateOrderMaterialData(this);'>{!! __('main.update') !!}</button><button class='btnEdit btn btn-success hide'>Edit</button>
                                        @else 
                                            <button type='button' class='btn btn-primary form-control updateSortOrder' onclick='updateSortOrder(this, "{{ $product->id }}");'>sort</button>
                                        @endif
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        @endif
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>