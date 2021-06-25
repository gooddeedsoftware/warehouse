{!! Form::open(array('route' => array('main.order.search', $query_string),'class'=>'archived_order_search_form', 'id' =>'archived_order_search_form')) !!}
	<div class="row">
		<div class="offset-md-4 col-l col-sm-6 col-md-2 form-group d-none d-sm-block filterOnsm">
			@if (Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
				{!!Form::select('search_by_department',@$departments, @Session::get('order_search')['search_by_department'], array('class'=>'form-control search_by_department','id'=>'search_by_department','placeholder'=>trans('main.department')))!!}
			@endif
		</div>
		<div class="col-l col-sm-6 col-md-2 form-group d-none d-sm-block filterOnsm">
			@if (Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative" || Session::get('usertype') == "Department Chief")
				{!!Form::select('search_by_order_users',@$orders_search_categoty, @Session::get('order_search')['search_by_order_users'], array('class'=>'form-control','id'=>'archived_search_by_order_users','placeholder'=>trans('main.show')))!!}
			@endif
		</div>
		<div class="col-l col-sm-12 col-md-4 form-group">
			<div class="input-group">
                {!! Form::text('search', @Session::get('order_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(__('main.order')) )) !!}
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
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
						@if (Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative")
							{!!Form::select('search_by_department',@$departments, @Session::get('order_search')['search_by_department'], array('class'=>'form-control search_by_department','id'=>'search_by_department','placeholder'=>trans('main.department')))!!}
						@endif
					</div>
					<div class="col-12 form-group">
						@if (Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative" || Session::get('usertype') == "Department Chief")
							{!!Form::select('search_by_order_users',@$orders_search_categoty, @Session::get('order_search')['search_by_order_users'], array('class'=>'form-control','id'=>'archived_search_by_order_users','placeholder'=>trans('main.selected')))!!}
						@endif
					</div>
	        	</div>
            </div>
        </div>
    </div>

	<input type="hidden" name="order_status_hidden" value=2>

{!! Form::close() !!}