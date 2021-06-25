@extends('layouts.layouts')
@section('title',trans('main.warehouse'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.warehouse') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.warehouse') !!}</p>
@stop

@section('content')
<div class="container warehouseContainer">
    <div class="card">
        <div class="card-header warehouseContainer-Header">
            <ul class="nav nav-tabs card-header-tabs">
                @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                    <li class="nav-item">
                        <a class="nav-link " href="{!! route('main.warehousedetails.index') !!}">{!! trans('main.stock') !!}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{!! route('main.warehouseorder.index') !!}">{!! trans('main.whs_orders') !!}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/ccsheet') }}">{!! trans('main.ccsheet') !!}</a>
                    </li>
                @endif
                <li class="nav-item">
                   <a class="nav-link active" href="#">{!! trans('main.products') !!}</a>
                </li>
                @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                    <li class="nav-item">
                        <a class="nav-link"  href="{!! route('main.productpackage.index') !!}">{!! trans('main.productpackage') !!}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"  href="{{ url('whs_history') }}">{!! trans('main.history') !!}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            @php
                $query_string = formatqueryString(str_replace(Request::url(), '', Request::fullUrl()));
            @endphp
            {!! Form::open(array('route' => array('main.product.search', $query_string), 'id' => 'product_search_form')) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        @if(Session::get('usertype') == "Admin" || Session::get('usertype') == "Department Chief" || Session::get('usertype') == "Administrative" )
                            <a class="btn btn-primary" href="{!! route('main.product.create') !!}" >
                                <i class="d-block d-sm-none fa fa-plus"></i>
                                <div class="d-none d-sm-block">{!!trans('main.add').' '.strtolower(trans('main.product')) !!} </div>
                            </a>
                             <button type="button" class="btn btn-primary export_btn" id="export_btn">{!! trans('main.export') !!}</button>
                             <button type="button" class="btn btn-primary import_btn" id="import_btn" data-toggle="modal" data-target="#importModal">{!! trans('main.import') !!}</button>
                         @endif
                    </div>
                </div>
                <div class="col-md-3">
                     <div class="form-group">
                        {!!Form::select('search_by_supplier',@$suppliers, @Session::get('warehousedetails_product_search')['search_by_supplier'], array('class'=>'form-control','id'=>'search_by_supplier','placeholder'=>trans('main.selected')))!!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group input-group">
                        {!! Form::text('search', @Session::get('warehousedetails_product_search')['search'], array('id'=>'search_id','class' => 'form-control searchField','placeholder'=>trans('main.search').' '.strtolower(trans('main.product')) )) !!}
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" id="department_search_btn"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="product_table">
                    <thead>
                        <tr>
                            <th>
                                <a>@sortablelink('product_number', trans('main.product_number'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('nobb', trans('main.nobb'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('description', trans('main.description'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('account', trans('main.account_no'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('sale_price', trans('main.sale_price'))</a>
                            </th>
                            <th>
                                <a>@sortablelink('vendor_price', trans('main.vendor_price_nok_short'))</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(@$products)
                            @foreach (@$products as $product)
                                <tr>
                                    <td>
                                        <a href="{!! route('main.product.edit', array(@$product->id))!!}">{!! @$product->product_number !!}</a>
                                    </td>
                                    <td> {!! htmlspecialchars(@$product->nobb) !!} </td>
                                    <td> {!! htmlspecialchars(@$product->description) !!} </td>
                                    <td> {!! htmlspecialchars(@$product->acc_plan->AccountNo) !!} </td>
                                    <td> {!! Number_format(@$product->sale_price, "2", ",", "")  !!} </td>
                                    <td> {!! Number_format(@$product->vendor_price, "2", ",", "")  !!} </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if (@$products)
                @include('common.pagination', array('paginator' => @$products, 'formaction' => 'product_search_form'))
            @endif
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="importModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">{{ trans('main.import_product') }}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            {!! Form::open(array('route' => 'main.product.import', 'id' => 'product_import_form', 'files' => true)) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            {!! Form::label('file', trans('main.file'), array('class' => 'col-md-2 col-form-label text-md-right ')) !!}
                            <div class="col-md-9">
                                 <input ccept=".xlsx" name="import_excel" type="file" id="file_upload" class="required_field" required>
                                <div class="clearfix"></div>
                                <span style="color:red">( {{trans('main.use_exported_file')}} )</span> 
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>

      <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="productImportbtn">{{ trans('main.import') }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('main.cancel') }}</button>
        </div>

    </div>
  </div>
</div>


{!! Form::open(array('route' => 'main.product.export', 'id' => 'product_export_form')) !!}
{!! Form::close() !!}
@endsection

@section('page_js')
    <script type="text/javascript">
        $("#product_import_form").validate();
        $("#search_by_supplier").on("change", function (e) {
            $("#product_search_form").submit();
        });

        $("#export_btn").on("click", function (e) {
            $("#product_export_form").submit();
        });

         $('#productImportbtn').on("click", function(e) {
            displayBlockUI();
            $(this).attr('disabled', 'disabled')
            if (!$('#product_import_form').valid()) {
                $(this).removeAttr('disabled')
                $.unblockUI();
                return false;
            }
            $('#product_import_form').submit();
        });
    </script>
@endsection