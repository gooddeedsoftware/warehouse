@extends('layouts.layouts')
@section('title',trans('main.reporting'))
@section('header')
<h3>
    <i class="icon-message">
    </i>
    {!!trans('main.reporting') !!}
</h3>
@stop

@section('help')
<p class="lead">
    {!!trans('main.reporting') !!}
</p>
<p>
    {!!trans('main.area.help') !!}
</p>
@stop

@section('content')
<div class="container">
    <div class="card">
    	{!! Form::open( array('route' => 'main.reporting.downloadReport', 'class' => 'form-horizontal')) !!}
	        <div class="card-header">
	            <div class="row">
	                <div class="col-md-2">
	                    <b>
	                        {!!__('main.reporting') !!}
	                    </b>
	                </div>
	                <div class="col-md-10">
	                    <span class="float-right">
	                        <div class="form-group row">
	                            {!! Form::label('report_filter_type', trans('main.selected'), array('class' => 'control-label col-sm-4') ) !!}
	                            <div class="col-sm-8">
	                                {!! Form::select('report_filter_type', @$hourlogg_filter_types, @$hourlogg_filter_type, array('class' => 'form-control report_filter_type', 'placeholder' => trans('main.selected'))) !!}
	                            </div>
	                        </div>
	                    </span>
	                </div>
	            </div>
	        </div>
	        <div class="card-body">
	            <div class="row">
	                <div class="col-sm-3">
	                    <div class="form-group hide_div" id="warehouse_div">
	                        {!! Form::label('warehouse', trans('main.warehouse'), array('class' => 'control-label')) !!}
	                        <div>
	                            {!! Form::select('warehouse', @$warehouses, '', array('class' => 'form-control', 'placeholder' => trans('main.selected'), 'id' => 'warehouse' )) !!}
	                        </div>
	                    </div>
	                </div>
	                <div class="col-sm-3">
	                    <div class="form-group hide_div ccsheet_report" id="ccsheet_date_div">
	                        {!! Form::label('ccsheet_date', trans('main.date'), array('class' => 'control-label')) !!}
	                        <div>
	                            {!! Form::select('ccsheet_date', array(), '', array('class' => 'form-control', 'id' => 'ccsheet_date', 'placeholder' => trans('main.selected') )) !!}
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row">
	                <div class="col-sm-3" id="button_div">
	                    <div class="form-group">
	                        <label class="control-label">
	                        </label>
	                        <div>
	                            <button class="btn btn-primary hide_div submit_btn" id="pdf" name="report_type" type="submit" value="pdf">
	                                {!! trans('main.pdf') !!}
	                            </button>
	                            <button class="btn btn-primary hide_div submit_btn" id="xlsx" name="report_type" type="submit" value="xlsx">
	                                {!! trans('main.xlsx') !!}
	                            </button>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
    var warehouse_date_url = "{!! route('main.reporting.getccsheetdates', array('123456')) !!}";
		var default_select_option = "<option value=''>{!! trans('main.selected') !!}</option>";
		$("#report_filter_type").change( function () {
			$("#warehouse_div").addClass('hide_div');
			$("#ccsheet_date_div").addClass('hide_div');
			$(".submit_btn").addClass('hide_div');
			$("#ccsheet_date").html(default_select_option);
			$("#warehouse").val('');
		 	if ($(this).val() == 'ccsheet_report') {
				$("#warehouse_div").removeClass('hide_div');
				$("#ccsheet_date_div").removeClass('hide_div');
			} else if ($(this).val() == 'stock') {
				$("#warehouse_div").removeClass('hide_div');
			}
		});

		$("#warehouse").change( function () {
			if ($("#report_filter_type").val() == "ccsheet_report") {
				if ($(this).val() != "") {
					displayBlockUI();
					$("#ccsheet_date").html(default_select_option);
					var reconstructed_url = warehouse_date_url.replace('123456', $(this).val())
					$.ajax({
						url : reconstructed_url,
						type : 'GET',
						async : false,
						success : function (data) {
							console.log(data);
							setTimeout($.unblockUI, 1000);
							if (data) {
								var parsed_data = JSON.parse(data);
								if (parsed_data['status'] == SUCCESS) {
									$.each(parsed_data['data'], function (key, value) {
										$("#ccsheet_date").append('<option value="'+value["id"]+'">'+value["name"]+'</option>');
									});
								} else {
									$("#ccsheet_date").html(default_select_option);
								}
							}
						},
						fail : function (e) {
							setTimeout($.unblockUI, 1000);
						}
					});
				} else {
					$("#ccsheet_date_div").addClass('hide_div');
					$("#button_div").addClass('hide_div');
				}
			} else {
				if ($(this).val() != "") { 
					$('#xlsx').removeClass('hide_div');
				} else {
					$('#xlsx').addClass('hide_div');
				}
			}
		});

		$("#ccsheet_date").change(function () {
			if ($(this).val() != '' ) {
				$(".submit_btn").removeClass('hide_div');
			} else {
				$(".submit_btn").addClass('hide_div');
			}
		});
</script>
@endsection
