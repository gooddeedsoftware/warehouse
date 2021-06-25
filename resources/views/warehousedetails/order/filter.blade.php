{!! Form::open(array('route' => array('main.warehouseorder.search', $query_string), 'id' => 'order_search_form')) !!} 
    <div class="row">
        <div class="col-3 col-sm-6 col-md-3">
            <div class="dropdown form-group">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {!! trans('main.new') !!}
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{!! route('main.warehouseorder.createTransferOrder') !!}">{!! trans("main.transfer_order") !!}</a>
                    <a class="dropdown-item" href="{!! route('main.warehouseorder.createSupplierOrder') !!}">{!! trans("main.supplier_order") !!}</a>
                    <a class="dropdown-item" href="{!! route('main.warehouseorder.createAdjustmentOrder') !!}" >{!! trans("main.adjustment_order") !!}</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 form-group d-none d-sm-block filterOnsm">
           {!!Form::select('search_by_order_status',@$warehouse_order_status, @Session::get('warehousedetails_order_search')['search_by_order_status'], array('class'=>'form-control','id'=>'search_by_order_status','placeholder'=>trans('main.selected')))!!}
        </div>
        <div class="col-sm-6 col-md-3 form-group d-none d-sm-block filterOnsm">
           {!!Form::select('search_by_order_type',@$warehouse_order_types, @Session::get('warehousedetails_order_search')['search_by_order_type'], array('class'=>'form-control','id'=>'search_by_order_type','placeholder'=>trans('main.selected')))!!}
        </div>
        <div class="col-9 col-sm-6 col-md-3">
            <div class="form-group input-group">
                {!! Form::text('search', @Session::get('warehousedetails_order_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(trans('main.order')) )) !!}
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="currency_search_btn"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="accordion form-group  d-block d-sm-none" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne">{!! __('main.filter') !!}</button>
                <button type="button" class="btn btn-link float-right offer_product_collapse_btn" data-toggle="collapse" data-target="#collapseOne" id="offer_product_collapse_btn"><i class="fa fa-plus"></i></button>
            </div>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="row mt-3">
                    <div class="col-12 form-group">
                        {!!Form::select('search_by_order_status',@$warehouse_order_status, @Session::get('warehousedetails_order_search')['search_by_order_status'], array('class'=>'form-control','id'=>'search_by_order_status','placeholder'=>trans('main.selected')))!!}
                    </div>
                    <div class="col-12 form-group">
                        {!!Form::select('search_by_order_type',@$warehouse_order_types, @Session::get('warehousedetails_order_search')['search_by_order_type'], array('class'=>'form-control','id'=>'search_by_order_type','placeholder'=>trans('main.selected')))!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
{!! Form::close() !!}