@extends('layouts.layouts')
@section('title',trans('main.ccsheet_details'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('main.ccsheet_details') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('main.ccsheet_details') !!}</p>
@stop

@section('content')
<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!trans('main.ccsheet_details') !!}</b>
            <div class="float-right"><b>{!! @$ccsheet->created_date . ' ' . @$ccsheet->warehouse->shortname !!}</b></div>
        </div>
        <div class="card-body">
    	  	<div class="row">
	        	<div class="col-l col-sm-4 col-md-2 form-group">
					<select id='location' class="form-control">
						<option>{!! trans('main.selected') !!}</option>
				    	@foreach($locations as $key => $value)
		    				<option value="{!! $value!!}">{!! $value!!}</option>
				    	@endforeach
			    	</select>
		    	</div>
		    	<div class="col-l col-sm-4 col-md-2 form-group">
					<select id='stock_filter' class="form-control">
						<option>{!! trans('main.selected') !!}</option>
						<option value="1">{!! trans('main.show_all') !!}</option>
						<option value="00">{!! trans('main.show_only_zero') !!}</option>
			    	</select>
		    	</div>
		    	<div class="col-l col-sm-4 col-md-8">
		    		<b><p class="completed_p text-sm-float-right">
			    		@if (@$ccsheet->status == 5)
			    			{!! trans('main.completed') !!}
			    		@else
			    			{!! trans('main.open') !!}
			    		@endif
					</p></b>
		    	</div>
		    	@if(@$ccsheet->status != 5 && @$recount == 0)
		    	<div class="col-6 col-sm-6 form-group">
	        		<a class="btn btn-primary" id='create_new_row' type="button" href="#">{!! trans('main.addnew') !!}</a>
	        	</div>
	        	<div class="col-6 col-sm-6 form-group">
	        		<a class="btn btn-primary float-right" id='use_scanner' href="{!! route('main.ccsheet.scannerView', array(@$ccsheet->id)) !!}">{!! trans('main.use_scanner') !!}</a>
	        	</div>
	        	 @endif
	        </div>
	        <div class='table-responsive form-group'>
				<table class="table table-striped table-hover" id="ccsheet_details_table">
                <thead>
                    <tr>
						<th width="20%">
							<a>{!!trans('main.product') !!}</a>
						</th>
						<th width="20%">
							<a>{!!trans('main.location') !!}</a>
						</th>
						<th width="20%">
							<a>{!! trans('main.onstock')!!}</a>
						</th>
						<th  width="15%">
							<a>{!! trans('main.counted')!!}</a>
						</th>
						<th width="5%"></th>
                    </tr>
                </thead>
					<tbody>
                    	@foreach(@$ccsheet_details as $ccsheet_detail)
	                    	@if(@$ccsheet_detail->product_number)
			                    @if(@$ccsheet_detail->counted)
			                    	<tr style="background-color: rgba(145, 238, 145, 0.41);">
			                    @else
			                    	<tr>
			                    @endif
			                        <td>
			                        	{!! @$ccsheet_detail->product_number !!} {!! @$ccsheet_detail->product->description !!}
		                        	</td>
			                        <td>
			                        	{!! @$ccsheet_detail->location->name !!}
		                        	</td>
			                       	<td>
			                       		@if (@$ccsheet_detail->on_stock_qty == 0)
			                       			0 {!! @$ccsheet_detail->unit !!}
			                       		@else
			                       			{!! @$ccsheet_detail->on_stock_qty !!} {!! @$ccsheet_detail->unit !!}
			                       		@endif
			                       	</td>
									<td>
										@php
											if (@$recount == 1 && !@$ccsheet_detail->recounted_by) {
											 	@$ccsheet_detail->counted = @$ccsheet_detail->counted;
											 }
									 	@endphp
										{!! Form::text('counted',(@$ccsheet_detail->counted != '' ) ? @$ccsheet_detail->counted : '', array('class'=>'form-control counted numberWithSingleComma','required','id' => @$ccsheet_detail->ccsheet_detail_id, 'readonly' => 'readonly')) !!}
									</td>
									<td>
										@if(@$ccsheet_detail->delete_val == 1 && @$ccsheet_details[0]->ccsheet->status != 5 )
											<a class='btn delete-icon' id="remove_product" onclick="removeProduct('{!! @$ccsheet_detail->ccsheet_detail_id !!}', '{!! @$ccsheet_detail->product->id !!}', '{!! @$ccsheet_detail->location->id !!}',this);" title="Delete"> <i class="fas fa-trash-alt" ></i></a>
										@endif
									</td>
			                    </tr>
		                    @endif
                    	@endforeach
					</tbody>
				</table>
			</div>
			<div class="col-l">
				<a class="btn btn-primary hide_div" id="complete_and_close_button" href="{!! route('main.ccsheet.updateCCSheetStatus', array(@$ccsheet->id)) !!}">{!! trans('main.complete_and_close') !!}</a>
				<a class="btn btn-danger" href="{!! route('main.ccsheet.index') !!}">{!! trans('main.close') !!}</a>
			</div>
        </div>
    </div>
</div>
{!! Form::hidden('recount', @$recount, array('id' => 'recount')) !!}
{!! Form::textarea('products', @$product_options, array('class'=>'form-control hide_div','id'=>'hidden_product_options')) !!}
<input type="hidden" name="ccsheet_id_val" id="ccsheet_id_val" value="{!! @$ccsheet->id !!}">
@endsection

@section('page_style')
	{!! Html::style('css/jquery.flexdatalist.min.css') !!}
@endsection

@section('page_js')
{!! Html::script('js/jquery.flexdatalist.min.js') !!}

	<script type="text/javascript">
		var counted_serail_validation  = '{!! trans("main.counted_serail_validation") !!}';
		var data_table_language_url = "{!! URL::to('datatable_language') !!}/{!! Session::get('language') !!}";
		var set_count_route = "{!! route('main.ccsheet.setCounted') !!}";
		var warehouse = "{!! @$ccsheet->warehouse->shortname !!}";
		var ccsheet_status = "{!! @$ccsheet_details[0]->ccsheet->status !!}";
		$(document).ready(function () {
			//Data table
			$(document).on('change', 'select[name="ccsheet_details_table_length"]', function () {
				var selected_option = $(this).val();
				window.localStorage.setItem('ccsheet_details_option', selected_option);
			});
			var selected_option = window.localStorage.getItem('ccsheet_details_option');
			selected_option = selected_option ? selected_option : 10;
			selected_option = parseInt(selected_option);
			// datatable
			var dataTable = $("#ccsheet_details_table").DataTable({
				language : {
		    		'url' : data_table_language_url+"/main.json"
		    	},
				order: [[1, "asc"]],
		        columnDefs: [ {
		            targets: [ 0 ],
		            orderData: [ 0, 1 ]
		        }, {
		            targets: [ 1 ],
		            orderData: [ 1, 0 ]
		        }],
		        selected : true,
	            lengthMenu: [[10, 25, 50,100, 300, -1], [10, 25, 50,100, 300, "All"]],
		        iDisplayLength : selected_option,
		        fnDrawCallback : function (o) {
					var targetOffset = $('body').offset().top;
					$('html,body').animate({scrollTop: targetOffset}, 500);
				}
		    });


			var count = 0;
		    $('#create_new_row').on( 'click', function () {
		    	var product_options = "<option value='Select'>" + js_select_text + "</option>";
		    	// var product_option = $("#hidden_product_options").val();
				// var product_option = $.parseJSON(product_option);
		        dataTable.row.add(['<select style="width:100% !important;" class="product_number_select2 select2 form-control product product_number" id="product">' + product_options + '</select>', '<select class="form-control" id="product_loction"></select>', '<input type="text" id="onstock" name="onstock" class="form-control onstock" style="display:none">', '<input type="text" id="counted_quantity" class="form-control counted_quantity numberWithSingleComma" name="counted_quantity" style="display:none"><input type="hidden" class="sn_required_val" name="sn_required" id="sn_required">', '<button type="button" class="btn btn-primary form-control" onclick="saveRecord(this);" id="save_ccsheet_record" style="display:none">'+save_btn+'</button>',''] ).draw( false );
		        	// <input type="text" name="product_number" id="product" class="form-control product_'+count+' flexdatalist" autocomplete="off" list="products" ><datalist id="products">'+product_option+'</datalist></div>','<select class="form-control" id="product_loction"></select>
		        	$('.product_number_select2').select2({
			            language: {
			                inputTooShort: function(args) {
			                    return select_product;
			                },
			                noResults: function() {
			                    return not_found;
			                },
			                searching: function() {
			                    return searching;
			                }
			            },
			            minimumInputLength: 2,
			            ajax: {
			                url: '/getSelect2Products/3',
			                dataType: 'json',
			                delay: 250,
			                processResults: function(data) {
			                    return {
			                        results: $.map(data, function(item) {
			                            return {
			                                text: item.product_text,
			                                id: item.id
			                            }
			                        })
			                    };
			                },
			                cache: false,
			            }
			        });

		        	$('.product_'+count).flexdatalist( {
		        		searchContain: true
					}).on('select:flexdatalist', function(event, items) {
						getProductDetails(this, items['value']);
					});
					count++;
		    });

		    $(document).on("change", ".product_number_select2", function () {
		    	getProductDetails($(this), $(this).val());
		    });

		    

		    $(document).on("change", ".flexdatalist", function () {
		    	$(this).trigger('keypress');
		    });

			$('#location').on('change', function () {
				if (this.value == "Select" || this.value == "Velg" ) {
					dataTable.columns(1).search('').draw();
				} else {
		        	dataTable.columns(1).search(this.value).draw();
		    	}
			});

			$('#stock_filter').on('change', function () {
				if (this.value == "Select" || this.value == "Velg" || this.value == 1) {
					dataTable.columns(4).search('').draw();
				} else {
		        	dataTable.columns(4).search(this.value).draw();
		    	}
			});

			// change count
			$(document).on('change', '.counted', function () {
				var sn_required_val = $(this).closest('tr').find('.sn_required_val').val();
				var sheet_id = $(this).attr("id");
				var counted = $(this).val();
				counted = replaceComma(counted);
				counted = parseFloat(counted);
				counted = counted.toFixed(2);
				counted = counted ? counted : 0;
				$(this).attr('readonly', 'readonly');
				setCountedInCCSheet(sheet_id, counted);
				$(this).parents('tr').css('background-color', 'rgba(145, 238, 145, 0.41)');
				checkAllCountedAreFilled();
			});

			//Showing the complete and close button
			checkAllCountedAreFilled();
			function checkAllCountedAreFilled() {
				var enable_complete_button = false;
				dataTable.rows().nodes().to$().find('.counted').each(function () {
					console.log($(this).attr('id'));
					if ($(this).val() != "") {
						enable_complete_button = true;
					} else {
						$("#complete_and_close_button").hide();
						enable_complete_button = false;
						return false;
					}
				});
				if (enable_complete_button && ccsheet_status != 5) {
					$("#complete_and_close_button").removeClass('hide_div');
					$("#complete_and_close_button").show();
				}
			}

			// show input box
			$(document).on('click focus touchend', '.counted', function () {
				if (ccsheet_status != '5') {
					$(this).attr('readonly', false);
				}
			});

			// set counted via Ajax
			function setCountedInCCSheet(sheet_id, counted) {
				var user_id = "{!! Session::get('currentUserID') !!}";
				var recount = $("#recount").val();
				$.ajax({
					'type' : 'post',
					'url' : set_count_route,
					'async' : false,
					'data' : {
						'_token' : token,
						'counted' : counted,
						'id' : sheet_id,
						'counted_by' : user_id,
						'recount' : recount
					},
					'success' : function (data) {
						console.log(data);
					},
					'error' : function (data) {
					}
				});
			}
		});

		/**
		 * [getProductDetails description]
		 * @param  {[type]} obj [description]
		 * @return {[type]}     [description]
		 */
		function getProductDetails(obj, product_id) {
			var warehouse = "{!! @$ccsheet->whs_id !!}";
			if (product_id) {
				var data_url = "/ccsheet/products/getproduct/"+product_id+"/"+warehouse;
				$.ajax({
					'type' : 'get',
					'url' : data_url,
					'async' : false,
					'data' : false,
					'success' : function (data) {
						if (data) {
							var result = JSON.parse(data);
							if (result.status == 'success') {
									if (result['data']) {
										getProductNumberDetail(obj, result['data']);
									}
								}
								var location_options = "";
								if (result.location) {
			                    	$.each(result.location, function (index, value) {
			                            location_options += "<option value='" + index + "' id='" + index + "'>" + value + "</option>";
			                        });
			                    }
			                    $(obj).closest('td').siblings().find('#product_loction').append(location_options);
							}
					},
					'error' : function (data) {
					}
				});
			}
		}
		/**
		 * [getProductNumberDetail description]
		 * @param  {[type]} obj   [description]
		 * @param  {[type]} items [description]
		 * @return {[type]}       [description]
		 */
		function getProductNumberDetail(obj, items) {
			$(obj).closest('td').siblings().find('#onstock').val(0).attr('readonly', true).removeAttr('style');
			var productNumber = items['product_number'];
			var product_id = items['id'];
			var unit_text = items['unit-text'];
			var unit = items['unit_text'];
			$(obj).parent().prev().val(productNumber);
			$(obj).attr("product_id", product_id);
			$(obj).attr("product_text", items['product_number']+' - '+items['description']);
			$(obj).closest('td').siblings().find('#unit_text').val(unit);
			$(obj).closest('td').siblings().find('#unit_text').removeAttr('style');
			$(obj).closest('td').siblings().find('#counted_quantity').removeAttr('style');
			$(obj).closest('td').siblings().find('#save_ccsheet_record').removeAttr('style');
			$(obj).closest('td').siblings().find('#onstock').removeAttr('style');
		}

		$(document).on('keypress', '.onstock', function (e) {
		    var regex = new RegExp("^[0-9]+$");
		    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		    if (regex.test(str)) {
		        return true;
		    } else {
		        e.preventDefault();
		        return false;
		    }
		});


		/**
		 * saveRecord
		 * @param obj
		 */
		function saveRecord(obj){
			//validation messages starts
			var product_validation_msg = "{!! trans('main.product_validation_msg') !!}";
			var productlocation_validation_msg = "{!! trans('main.productlocation_validation_msg') !!}";
			var onstock_validation_msg = "{!! trans('main.onstock_validation_msg') !!}";
			var countedqty_validation_msg = "{!! trans('main.countedqty_validation_msg') !!}";
			////validation messages ends
			var token = "{!! csrf_token() !!}";

			var product_id = $(obj).closest('td').siblings().find('#product').attr('product_id');

			var product_location = $(obj).closest('td').siblings().find('#product_loction').val();

			var product = $(obj).closest('td').siblings().find('#product').val();


			var product_text = $(obj).closest('td').siblings().find('#product').attr('product_text');

			var onstock = $(obj).closest('td').siblings().find('#onstock').val();
			var counted_quantity = $(obj).closest('td').siblings().find('#counted_quantity').val();
			var ccsheet_id = $('#ccsheet_id_val').val();
			var unit_text = $(obj).closest('td').siblings().find('#unit_text').val();
			onstock = replaceComma(onstock);
			onstock = parseFloat(onstock);
			onstock = onstock.toFixed(2);
			counted_quantity = replaceComma(counted_quantity);
			counted_quantity = parseFloat(counted_quantity);
			counted_quantity = counted_quantity.toFixed(2);

			if (product == '' || product == undefined || product == null){
				alert(product_validation_msg);
				return false;
			}
			else if(product_location == '' || product_location == undefined || product_location == null) {
				alert(productlocation_validation_msg);
				return false;
			}
			else if(onstock == '' || onstock == undefined || onstock == null) {
				alert(onstock_validation_msg);
				return false;
			}
			else if (counted_quantity == '' || counted_quantity == undefined || counted_quantity == null) {
				alert(countedqty_validation_msg);
				return false;
			}
			$.ajax({
				type: "POST",
				url: "/ccsheetdetails/saverecord",
				data: {
					'_token':token,
					'rest':'true',
					'product_number':product,
					'location_id':product_location,
					'counted_qty':counted_quantity,
					'on_stock_qty':onstock,
					'ccsheet_id': ccsheet_id,
					'unit': unit_text,
					'product_id': product_id,
				},
				success : function(data) {
					if (data) {
	                    decoded_response = $.parseJSON(data);
	                    $(obj).parents('tr').css('background-color', 'rgba(145, 238, 145, 0.41)');
	                    $(obj).closest('td').siblings().find('#product_loction').replaceWith($("<span />").text(decoded_response.data.location_id));
	                    $(obj).closest('td').siblings().find('#onstock').replaceWith($("<span />").text(onstock));
	                    $(obj).closest('td').siblings().find('#product').replaceWith($("<span />").text(product_text));
	                    $(obj).closest('td').siblings().find('#unit_text').replaceWith($("<span />").text(unit_text));
	                    $(obj).closest('tr').find('.select2-container').remove();
	                    $(obj).closest('td').siblings().find('#counted_quantity').attr('id', decoded_response.data.id).addClass('counted');
	                    $(obj).hide();
	                    if (sn_required != 1) {
	                    	$('#'+product_id).remove();
	                    }
	                }
				},
				error: function(data) {
			    }
			});
		}

		/**
		 * [removeProduct description]
		 * @param ccsheetdetail_id
		 * @param product_id
		 * @param location_id
		 */
		function removeProduct(ccsheetdetail_id = false, product_id = false, location_id = false, obj){
			if (ccsheetdetail_id && product_id && location_id) {
				var confrim_delete = confirm("Do You want to remove the product?..");
		        if (confrim_delete) {
		            var token = "{!! csrf_token() !!}";
					$.ajax({
						type: "POST",
						url: "/ccsheetdetails/deleteccsheetproduct",
						data: {
							'_token':token,
							'rest':'true',
							'ccsheetdetail_id':ccsheetdetail_id,
							'product_id':product_id,
							'location_id':location_id
						},
						async : false,
						success : function(data) {
							if (data) {
								var result = JSON.parse(data);
								if (result.status == 'success') {
									if (result.data == 1){
										$(obj).closest('td').parent('tr').hide();
										$("#success-alert").fadeTo(1000, 500).slideUp(500, function(){
						                	$("#success-alert").slideUp(500);
						                });
									}
									if (result.data == 2) {
										$("#warning-alert").fadeTo(1000, 500).slideUp(500, function(){
						                	$("#warning-alert").slideUp(500);
						                });
									}
								}
							}
						},
						error: function(data) {

					    }
					});
		        }
		        else {
		            return false;
		        }
			}
		}
	</script>
@endsection
