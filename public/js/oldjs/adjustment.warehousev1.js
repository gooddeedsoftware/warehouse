var product_array;

function setFields() {
    var status = $('#order_status').val();
    if (status == 1 && $('#warehouse').val()) {
        $("#cloneProductTableBtn").show();
        setLocations();
    } else if (status == 5) {
        disableAll();
        $('#order_status').removeAttr('disabled');
        $('#order_status').removeAttr('readonly');
        $('.warehouseorder_submit_btn').attr('disabled', true);
        $('.rec_date').removeClass('hide-div');
    } else if (status == 6) {
        disableAll();
        $('.warehouseorder_submit_btn').attr('disabled', true);
        $('.rec_date').removeClass('hide-div');
    }
}
// find order status
$(document).on("change", "#order_status", function() {
    var order_status = $(this).val();
    var order_id_val = $('#rder_id_value').val();
    if (order_status == 3) {
        disableAll();
    } else if (order_status == 6) {
        confirm_msg = confirm(arichive_confimation_message);
        if (confirm_msg) {
            $(this).attr('disabled', 'disabled');
            $('.rec_location_td').hide();
            updateOrderStatusValueToArchive(order_id_val);
        } else {
            var order_status = $(this).val('5');
            return false;
        }
    } else {}
});

function disableAll() {
    $('#warehouseorderform').find('.form-control').attr('disabled', true);
    $('#warehouseorderform').find('.form-control').attr('readonly', true);
    $('.select').select2("enable", false);
    $('.rec_qty_element').removeClass('hide-div');
    $("#cloneProductTableBtn").hide();
    $('.deleteRowBtn').hide();
}

function enableAll() {
    $('#warehouseorderform').find('.form-control').attr('disabled', false);
    $('#warehouseorderform').find('.form-control').attr('readonly', false);
    $('.select').select2("enable", true);
}
// select product/location depend upon the warehouse
$(document).on("change", "#warehouse", function() {
    $('#warehouse_product_order_table tbody').html("");
    $("#cloneProductTableBtn").hide();
    var warehouse = $("#warehouse").val();
    if (warehouse) {
        $("#cloneProductTableBtn").show();
    }
});
// create new row in product details table
function createNewProductTableRow() {
    var product_options = "<option value='Select'>" + js_select_text + "</option>";;
    var location_options = "";
    var products = "<td><select style='width: 100%;' class='product_number_select2  newRowselect2 order_product' onchange='productChange(this);'>" + product_options + "</select>";
    var location = "<td><select style='width: 100%;' class='form-control product_location' onchange='locationChange(this);'>" + location_options + "</select></td>";
    var qty = "<td><input class='form-control qty numberWithSingleMinus' type=text value='' onchange='checkLocationIsSeleted(this);'>";
    qty += "</td>";
    var comment = "<td class='product_comment_td'><input class='form-control comment' type=text  value='' ></td>";
    var delete_td = "<td class='product_delete_td'><i class='delete-icon fa fa-trash deleteRowBtn' onclick=deleteProductRow(this,''); ></i></td>";
    var html_string = "<tr class='product_tr'>" + products + location + qty + comment + delete_td + "</tr>";
    $('#warehouse_product_order_table tbody:last').append(html_string);
    setTimeout(function() {
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
    }, 100);
}

function productChange(obj) {
    var selected_product = $(obj).val();
    var order_type = $("#order_type").val();
    warehouse_id = $("#warehouse").val();
    setProductLocation(selected_product, order_type, warehouse_id, obj, 1, '')
}

function setProductLocation(selected_product, order_type, warehouse_id, obj, type, location_id) {
    $(obj).closest('tr').find('.product_location').html("");
    var warehouse_product_url = url + "/product/getProductDetailFromId";
    $.ajax({
        type: "POST",
        url: warehouse_product_url,
        async: false,
        data: {
            '_token': token,
            'rest': 'true',
            'product_id': selected_product,
            'order_type': order_type,
            'warehouse_id': warehouse_id
        },
        success: function(response) {
            if (response) {
                try {
                    decoded_response = $.parseJSON(response);
                    if (decoded_response['status'] == SUCCESS) {
                        product_details = decoded_response['data'];
                        var transfer_location_options = "<option>Select</option>"
                        var warehouse_location_index = 0
                        console.log(product_details['warehouse_locations']);
                        $.each(product_details['warehouse_locations'], function(index, value) {
                            if (warehouse_location_index == 0) {
                                transfer_location_options += "<option selected='selected' value=" + index + ">" + value + "</option>";
                            } else {
                                transfer_location_options += "<option value=" + index + ">" + value + "</option>";
                            }
                            warehouse_location_index++;
                        });
                        $(obj).closest('tr').find('.product_location').html(transfer_location_options).trigger('change');
                        if (type == 1) {
                            $(obj).closest('tr').find('.qty').val('')
                        } else {
                            $(obj).closest('tr').find('.product_location').val(location_id).trigger('change');
                        }
                    } else if (decoded_response['status'] == ERROR) {
                        showAlertMessage(decoded_response['data']);
                    }
                } catch (Exception) {}
            }
        },
        fail: function() {
            showAlertMessage(something_went_wrong);
        }
    });
}
// Product options
function getProductAsOptions(product_id) {
    var product_options = "<option>Select</option>";
    product_array = $("#products_array").val();
    if (product_array) {
        product_array = $.parseJSON(product_array);
        $.each(product_array, function(index, value) {
            if (product_id == index) {
                product_options += "<option value=" + index + " selected='selected'>" + value + "</option>";
            } else {
                product_options += "<option value=" + index + ">" + value + "</option>";
            }
        });
    }
    return product_options;
}
// check location is selected for adjustment order before enter the quantity
function checkLocationIsSeleted(object) {
    var location = $(object).val();
    if (location == "Select" || location == "velg" || location == '' || location == null) {
        showAlertMessage(select_location);
        if (object) {
            $(object).val('');
        }
        $(".warehouseorder_submit_btn").attr('disabled', true);
    } else {
        var order_type = $("#order_type").val();
        var warehouse = $("#warehouse").val();
        var product_id = $(object).closest('tr').find('.order_product').val();
        var qty = replaceComma($(object).val());
        var qty_val = parseFloat(replaceComma($(object).val()));
        if (qty) {
            displayBlockUI();
            if (warehouse) {
                $.ajax({
                    type: 'POST',
                    url: checkproduct_exist_url,
                    data: {
                        _token: token,
                        'warehouse_id': warehouse,
                        'product_id': product_id,
                        'location_id': location
                    },
                    success: function(response) {
                        if (response) {
                            var decoded_data = $.parseJSON(response);
                            if (decoded_data['status'] == SUCCESS) {
                                if (decoded_data['qty'] + qty_val < 0) {
                                    showAlertMessage(stock_not_availabl);
                                    $(object).val('');
                                }
                            } else if (decoded_data['data'] == ERROR) {}
                        }
                        setTimeout($.unblockUI, 100);
                    },
                    error: function() {
                        setTimeout($.unblockUI, 100);
                        showAlertMessage(something_went_wrong);
                    }
                });
            }
        }
        $(".warehouseorder_submit_btn").attr('disabled', false);
    }
}
// location change event
function locationChange(obj) {
    $(obj).closest('tr').find('.qty').val('');
}
$(document).on('focus', '.select2-container', function(e) {
    $(this).closest("select + *").prev().select2('open');
});
$(document).on("click", ".warehouseorder_submit_btn", function() {
    var submit_btn_value = $(this).val();
    $('#submit_button_value').val(submit_btn_value);
    displayBlockUI();
    enableAll();
    $("#product_details").val("");
    var order_product_table_row_count = $("#hidden_warehouse_table_row_count").val();
    var product_data = [];
    $('.product_tr').each(function() {
        if ($(this).closest('tr').find('.order_product').val() && $(this).closest('tr').find('.order_product').val() != "Select" && $(this).closest('tr').find('.order_product').val() != "velg") {
            product_data.push({
                "product_id": $(this).closest('tr').find('.order_product').val(),
                "product_text": $(this).closest('tr').find('.order_product option:selected').text(),
                "location_id": $(this).closest('tr').find('.product_location').val(),
                "location_text": $(this).closest('tr').find('.product_location option:selected').text(),
                "qty": replaceComma($(this).closest('tr').find('.qty').val()),
                "comment": $(this).closest('tr').find('.comment').val(),
            });
        }
    });
    if (product_data) {
        $("#product_details").val(JSON.stringify(product_data));
    }
    $("#warehouseorderform").submit();
});

function deleteProductRow(obj, hourlogg_ids) {
    var confirm_delete_msg = confirm(confirm_delete);
    if (confirm_delete_msg) {
        $(obj).closest('tr').remove();
    }
}

function updateOrderStatusValueToArchive(order_id_val) {
    var order_id_val = order_id_val;
    var trasnfer_order_url = url + "/warehouseorder/updateStatusToArchive";
    if (order_id_val) {
        $.ajax({
            type: "POST",
            url: trasnfer_order_url,
            async: false,
            data: {
                '_token': token,
                'rest': 'true',
                "order_id_val": order_id_val,
            },
            success: function(response) {
                if (response) {
                    decoded_response = $.parseJSON(response);
                    if (decoded_response['status'] == SUCCESS) {
                        $('#warehouseorderform').find('.form-control').attr('disabled', true);
                        $('#warehouseorderform').find('.form-control').attr('readonly', true);
                        $('.select').select2("enable", false);
                        $('.rec_qty_element').removeClass('hide-div');
                        $("#cloneProductTableBtn").hide();
                    } else if (decoded_response['status'] == ERROR) {
                        return false;
                    }
                }
                new PNotify({
                    title: message_text,
                    text: archived_status_text,
                    type: "success",
                    delay: 2000,
                });
            },
            fail: function() {
                showAlertMessage(something_went_wrong);
            }
        });
    }
}
/**
 * [setLocations description]
 */
function setLocations() {
    $('.product_location').each(function() {
        setProductLocation($(this).closest('tr').find('.order_product').val(), 2, $("#warehouse").val(), this, 2, $(this).val());
    })
}