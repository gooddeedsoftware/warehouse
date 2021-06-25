{!! Form::open(array('route' => array('main.ccsheet.search', $query_string), 'id' => 'ccsheet_search_form')) !!}
<div class="row">
	<div class="col-3 col-sm-2 col-md-2 form-group">
		<a class="btn btn-primary" href="{!! route('main.ccsheet.create') !!}" >{!!trans('main.add') !!}</a>
	</div>
	<div class="col-sm-10 col-md-3 form-group  d-none d-sm-block filterOnsm">
		{!! Form::select('warehouse',@$warehouses, @Session::get('ccsheet_search')['warehouse'], array('class'=>'form-control warehouse','id'=>'warehouse','placeholder'=>trans('main.selected')))!!}
	</div>
	<div class="col-sm-6 col-md-2 form-group  d-none d-sm-block filterOnsm">
		{!! Form::text('start_date', @Session::get('ccsheet_search')['start_date'], array('id' => 'start_date','class' => 'form-control', 'placeholder' => trans('main.from_date'))) !!}
	</div>
	<div class="col-sm-6 col-md-2 form-group  d-none d-sm-block filterOnsm">
		{!! Form::text('end_date', @Session::get('ccsheet_search')['end_date'], array('id' => 'end_date','class' => 'form-control', 'placeholder' => trans('main.to_date'))) !!}
	</div>
	<div class="col-9 col-sm-12 col-md-3 form-group">
		<div class="input-group">
			{!! Form::text('search', @Session::get('ccsheet_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search'))) !!}
			<div class="input-group-append">
				<button type="submit" class="btn btn-primary"><i class="fa fa-search" id="customer_search_btn"></i></button>
			</div>
		</div>
	</div>
</div>

<div class="accordion form-group  d-block d-sm-none" id="accordionExample">
    <div class="card ccsheetCard">
        <div class="card-header" id="headingOne">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne">{!! __('main.filter') !!}</button>
            <button type="button" class="btn btn-link float-right offer_product_collapse_btn" data-toggle="collapse" data-target="#collapseOne" id="offer_product_collapse_btn"><i class="fa fa-plus"></i></button>
        </div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
        	<div class="row mt-3">
         		<div class="col-12 form-group">
        			{!! Form::select('warehouse',@$warehouses, @Session::get('ccsheet_search')['warehouse'], array('class'=>'form-control warehouse','id'=>'warehouse','placeholder'=>trans('main.selected')))!!}
        		</div>
        		<div class="col-12 form-group">
        			{!! Form::text('start_date', @Session::get('ccsheet_search')['start_date'], array('id' => 'start_date','class' => 'form-control', 'placeholder' => trans('main.from_date'))) !!}
        		</div>
        		<div class="col-12 form-group">
        			{!! Form::text('end_date', @Session::get('ccsheet_search')['end_date'], array('id' => 'end_date','class' => 'form-control', 'placeholder' => trans('main.to_date'))) !!}
        		</div>
        	</div>
        </div>
    </div>
</div>
{!! Form::close() !!}