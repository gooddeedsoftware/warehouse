{!! Form::open(array('route' => array('main.ordermaterial.listOrderMaterials', @$order_id), 'class'=>'product_search_form', 'id' =>'product_search_form')) !!}
    @if (!$orders->uni_status)
        <div class="row">
            <div class="col-md-2">
                <div class="btn-group form-group">
                    <button type="button" class="btn btn-primary add_product_material" id="add_product_material">{!!__('main.addnew') !!}</button>
                    <button type="button" class="btn btn-primary dropdown-toggle invoice_save_btn" data-toggle="dropdown">
                    <span class="caret"></span> <!-- caret -->
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu" id="invoice_status">
                        <a class="dropdown-item add_product_material" href="javascript:;">{!!__('main.addnewproduct') !!}</a>
                        <a class="dropdown-item add_new_text" href="javascript:;">{!!__('main.addtext') !!}</a>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 text-md-left"> 
                @if(count(@$product_packages) > 0)
                    <a class="btn btn-primary dropdown-toggle form-group" href="#" role="button" id="product_packages_div" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {!!__('main.addnew') !!} {!! strtolower(__('main.productpackage')) !!}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        @foreach(@$product_packages as $key => $product)
                        <a value="{!! $key !!}" class="dropdown-item product_package" href="#">{!! $product !!}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-md-1">
                <a class="btn btn-primary form-group hide-btn" href="javascript:;" id="shippment">
                    {!!__('main.shippment') !!}
                </a>
            </div>

            <div class="col-md-2  text-md-right">
                <div class="dropdown" id="packandShipdiv">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {!!__('main.picking_and_packing') !!}
                    </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item picklist" id="picklist" href="javascript:;" order_id="{{ @$order_id }}" data-value="1">
                        {!!__('main.picking_list') !!}
                    </a>
                    <a class="dropdown-item packlist" id="packlist" href="javascript:;" order_id="{{ @$order_id }}" data-value="1" >
                        {!!__('main.complete_order') !!}
                    </a>

                     <a class="dropdown-item download-last-packlist" id="download_last_packlist" href="{!! route('main.order.downloadLastPackList', array(@$order_id)) !!}"  order_id="{{ @$order_id }}">
                        {!!__('main.last_packlist') !!}
                    </a>


                  </div>
                </div>
            </div>

            <div class="col-md-2  text-md-right">
                <a class="btn btn-primary form-group" id="save_all_materials" href="#">
                    {!!__('main.save') !!}
                </a>
                <a class="btn btn-primary form-group" href="javascript:;" data-id="" data-href="{{ route('main.order.getReturnProduct', [@$order_id]) }}" id="return_material">
                    {!!__('main.return') !!}
                </a>
            </div>
            <div class="col-md-2">
                <div class="form-group input-group">
                    {!! Form::text('product_search', @Session::get('product_search')['product_search'], array('id' => 'product_search_str', 'class' => 'form-control searchField', 'placeholder' => __('main.search').' '.strtolower(__('main.product')))) !!}
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-8 text-md-right">
                <a class="btn btn-primary form-group" href="javascript:;" data-id="" data-href="{{ route('main.order.getReturnProduct', [@$order_id]) }}" id="return_material">
                    {!!__('main.return') !!}
                </a>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    {!! Form::text('product_search', @Session::get('product_search')['product_search'], array('id' => 'product_search_str', 'class' => 'form-control searchField', 'placeholder' => __('main.search').' '.strtolower(__('main.product')))) !!}
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
                    </div>
                </div>
            </div>
        </div>
    @endif
{!! Form::close() !!}