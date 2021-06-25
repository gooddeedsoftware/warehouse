var product_array;
var location_array;
window.localStorage.setItem("product_table_row_count", 0);
window.localStorage.setItem("supplier_order_status", "");
window.localStorage.setItem('received_serial_number_tab_index', '1');
window.localStorage.setItem('received_location_tab_index', '1');
setTabIndex();
/**
 * setTabIndex for input fields
 */
function setTabIndex() {
    $(":input[readonly='readonly']").each(function(i) {
        $(this).attr('tabindex', '-1');
    });
}
$("#order_status option[value='2']").hide();

function editSupplierOrder(order_status) {
    window.localStorage.setItem("product_table_row_count", 0);
    window.localStorage.setItem("supplier_order_status", order_status);
    if ($("#supplier").val() != "") {
        $("#cloneProductTableBtn").show();
        $("#product_packages_div").show();
    }
    setTimeout(function() {
        showHideOrderFields();
        constructProductTableDetails();
        if (order_status >= 2) {
            $("#order_status").attr('disabled', true);
            $("#supplier").attr('disabled', true);
            $("#order_status option[value='2']").show();
            disbleFormFields();
            if (order_status == 5 || order_status == 6) {
                $('#warehousesupplierorderform input,textarea,select,a,.select2').attr('readonly', true);
                $('#warehousesupplierorderform a,.select2,#priority,button,select').attr('disabled', true);
                $('#warehousesupplierorderform .close,.btn-danger').attr('disabled', false);
                $(".warehouseorder_submit_btn").attr('disabled', true);
                if (order_status == 5) {
                    $("#order_status").attr('disabled', false);
                    $("#order_status").attr('readonly', false);
                }
            }
            if (order_status == 2) {
                $("#order_comment").attr('readonly', false);
                $("#order_status").attr('readonly', false);
                $("#order_status").attr('disabled', false);
            }
        } else if (order_status == 1) {
            $("#order_status option[value='2']").show();
        }
        setTabIndex();
    }, 100);
}

function showHideOrderFields() {
    hideOrShowElements({
        'source_warehouse_div': 1,
        'destination_warehouse_div': 1,
        'warehouse_div': 1,
        'supplier_div': 1,
        'order_qty_symbol': 1,
        "location_th": 0,
        "source_warehouse_transfer_select": 1
    });
    hideOrShowElements({
        'supplier_div': 0,
        'destination_warehouse_div': 0,
        'location_th': 1,
        'rec_location_td': 1
    });
}

function hideOrShowElements(element_array) {
    if (element_array) {
        $.each(element_array, function(index, value) {
            if (value == "1") {
                $("#" + index).hide();
            } else {
                $("#" + index).show();
            }
        });
    }
}
// select product depend upon the supplier
$(document).on("change", "#supplier", function() {
    displayBlockUI();
    var supplier = $(this).val();
    $('#warehouse_product_order_table tbody').html("");
    getProdcuts(supplier, "");
    if (supplier) {
        $("#cloneProductTableBtn").show();
        $("#product_packages_div").show();
    } else {
        $("#cloneProductTableBtn").hide();
        $("#product_packages_div").hide();
    }
});
// find order status
$(document).on("change", "#order_status", function() {
    var order_status = $(this).val();
    var order_id_val = $('#rder_id_value').val();
    var previous_order_status = window.localStorage.getItem("supplier_order_status");
    $(".qty").attr('readonly', false);
    if (order_status >= 2) {
        $("#supplier").attr('disabled', true);
        if (order_status != 2) {
            $(this).attr('disabled', true);
        }
        if (previous_order_status > order_status) {
            showAlertMessage(not_allowed_to_change_status);
            $("#order_status").val(previous_order_status);
            setTabIndex();
            return false;
        } else {
            disbleFormFields();
            if (order_status == 2) {
                $("#order_comment").attr('readonly', false);
            }
        }
        if (order_status == 6) {
            confirm_msg = confirm(arichive_confimation_message);
            if (confirm_msg) {
                $(this).attr('disabled', 'disabled');
                $('.rec_location_td').hide();
                updateOrderStatusValueToArchive(order_id_val);
            } else {
                var order_status = $(this).val('5');
                setTabIndex();
                return false;
            }
        }
        if (order_status == 3 && $("#received_quantity_00").length > 0) {
            $("#received_quantity_00").focus();
        }
    } else {
        enableFormFields();
    }
    var order_type = $("#order_type").val();
    setTabIndex();
});
// disable form fields
function disbleFormFields() {
    $('#warehousesupplierorderform input,textarea,select,a,.select2').attr('readonly', true);
    $('#warehousesupplierorderform #warehouse').attr('disabled', true);
    $('#warehousesupplierorderform a,.select2,#priority, #destination_warehouse').attr('disabled', true);
    $('#warehousesupplierorderform .select2').css('pointer-events', 'none');
    $("#cloneProductTableBtn").hide();
    $("#product_packages_div").hide();
    $(".deleteHourloggBtn").hide();
    $(".product_delete_td").hide();
    var order_status = $("#order_status").val();
    if (order_status == 2) {
        $(".rec_qty_td").hide();
    } else {
        $(".rec_qty_td, .rec_location_td, .serial_number_td, .rec_warehouse_td, .rec_date_td").show();
    }
    $(".rec_qty_td").find('input[class="makeedit form-control"]').attr('readonly', false);
    $(".rec_qty_td").find('input[class="makeedit form-control validateNumbers"]').attr('readonly', false);
    $(".rec_qty_td").find('input[class="makeedit form-control numberWithSingleComma"]').attr('readonly', false);
    $(".rec_qty_td").find('input[type=text][readonly]').attr('readonly', true);
    if (order_status == 5) {
        $('#order_status').attr('readonly', false);
        $('#order_status').attr('disabled', false);
    } else {
        if (order_status != '6') {
            $('.qty').attr('readonly', false);
        }
    }
}
// enable the form fields
function enableFormFields() {
    $('#warehousesupplierorderform input,textarea,select,a,.select2').attr('readonly', false);
    $('#warehousesupplierorderform #warehouse').attr('disabled', false);
    $('#warehousesupplierorderform a,.select2,#priority,select').attr('disabled', false);
    $('#warehousesupplierorderform .select2').css('pointer-events', '');
    $(".product_delete_td").show();
    $(".rec_qty_td, .rec_location_td, .serial_number_td, .rec_warehouse_td, .rec_date_td").hide();
    $(".rec_location_td").find('.select2').attr('disabled', false);
    $("#cloneProductTableBtn").show();
    $("#product_packages_div").show();
    $(".rec_qty_td").find('input[type=text][readonly]').attr('readonly', true);
    $(".rec_qty_td").find('input').attr('readonly', false);
    $(".serial_number_td").find('input').attr('readonly', true);
    $(".rec_location_td").find('.select2').attr('disabled', true);
    $('#warehousesupplierorderform #warehouseorder_submit_btn').attr('disabled', false);
    $("#order_status").attr('readonly', false);
}
// get products
function getProdcuts(supplier, warehouse) {
    var warehouse_order_url = url + "/product/getProductDetailFromOrderType";
    $.ajax({
        type: "POST",
        url: warehouse_order_url,
        async: false,
        data: {
            '_token': token,
            'rest': 'true',
            "order_type": 3,
            "supplier_id": supplier,
            "warehouse_id": warehouse,
        },
        success: function(response) {
            if (response) {
                decoded_response = $.parseJSON(response);
                if (decoded_response['status'] == SUCCESS) {
                    product_array = decoded_response['data']['products'];
                    $("#products_array").text(JSON.stringify(product_array));
                } else if (decoded_response['status'] == ERROR) {
                    showAlertMessage(decoded_response['data']);
                }
                $.unblockUI();
            }
        },
        fail: function() {
            showAlertMessage(something_went_wrong);
        }
    });
}


// create new row in product details table
function createNewProductTableRow() {
    var i = window.localStorage.getItem("product_table_row_count");
    var product_options = getProductAsOptions("");
    var uuid = createUUID();
    var product_id = "order_product_" + i;
    console.log(product_id, "product_id")
    var products = "<td><div id='"+product_id+"_div'> <select name="+product_id+" class='order_product' id=order_product_" + i + "  onchange='productChange(this);'>" + product_options + "</select>";
    products += "<input type='hidden' id='whs_product_id_" + i + "' value='" + uuid + "'>";
    products += "<input type='hidden' class='is_package' id='is_content_" + i + "' value='0'>";
    products += "<input type='hidden' class='is_content' id='is_package_" + i + "' value='0'></div></td>";
    var qty = "<td><input class='qty form-control numberWithSingleComma' type=text id='qty_" + i + "' value='')></td>";
    var comment = "<td class='product_comment_td'><input class='form-control' type=text id='comment_" + i + "' value='' ></td>";
    var delete_td = "<td class='product_delete_td'><i class='fa fa-minus deleteHourloggBtn' id='delete_product_orders" + i + "' onclick=deleteProductRow(this,''); ></i></td>";
    var html_string = "<tr id= product_tr_" + i + ">" + products + qty + comment + delete_td + "</tr>";
    $('#warehouse_product_order_table tbody:last').append(html_string);
    i++;
    $("#hidden_warehouse_table_row_count").val(i);
    window.localStorage.setItem("product_table_row_count", i);
    setTimeout(function() {
        $("#"+product_id).select2();
        
    }, 100);

}
// Product options
function getProductAsOptions(product_id) {
    var product_options = "<option>Select</option>";
    product_array = $("#products_array").val();
    // product detail options
    if (product_array) {
        product_array = $.parseJSON(product_array);
        $.each(product_array, function(index, value) {
            if (product_id == index) {
                product_options += "<option id=" + index + " selected='selected'>" + value + "</option>";
            } else {
                product_options += "<option id=" + index + ">" + value + "</option>";
            }
        });
    }
    return product_options;
}
// Location options
function getLocationAsOptions(location_id) {
    var location_options = "<option>Select</option>";
    location_array = $("#locations_array").val();
    if (location_array) {
        location_array = $.parseJSON(location_array);
        if (location_array) {
            location_obj_length = Object.keys(location_array).length;
            var location_obj_index = 0;
            $.each(location_array, function(index, value) {
                if (location_id == index) {
                    location_options += "<option id=" + index + " selected='selected' value=" + value + ">" + value + "</option>";
                } else {
                    if (location_obj_length == 1) {
                        location_options += "<option selected='selected' id=" + index + " value=" + value + ">" + value + "</option>";
                    } else {
                        if (location_obj_index == 0) {
                            location_options += "<option selected='selected' id=" + index + " value=" + value + ">" + value + "</option>";
                        } else {
                            location_options += "<option id=" + index + " value=" + value + ">" + value + "</option>";
                        }
                    }
                }
                location_obj_index++;
            });
        }
    }
    return location_options;
}

// Destination location options
function destinationLocationOptions(location_id) {
    destination_location_array = $("#destination_locations_array").val();
    var destination_location_options = "<option>Select</option>";
    if (destination_location_array != "null" && destination_location_array) {
        destination_location_array = $.parseJSON(destination_location_array);
        var destination_location_size = Object.keys(destination_location_array).length;
        var destination_location_obj_index = 0;
        $.each(destination_location_array, function(index, value) {
            if (location_id == index) {
                destination_location_options += "<option selected='selected' id=" + index + " value=" + value + ">" + value + "</option>";
            } else {
                if (destination_location_size == 1) {
                    destination_location_options += "<option selected='selected' id=" + index + " value=" + value + ">" + value + "</option>";
                } else {
                    if (destination_location_obj_index == 0) {
                        destination_location_options += "<option selected='selected' id=" + index + " value=" + value + ">" + value + "</option>";
                    } else {
                        destination_location_options += "<option id=" + index + " value=" + value + ">" + value + "</option>";
                    }
                }
            }
            destination_location_obj_index++;
        });
    }
    return destination_location_options;
}
// check location is selected for adjustment order before enter the quantity
function checkLocationIsSeleted(iteration, object) {
    var location = $("#product_location_" + iteration).val();
    if (location == "Select" || location == "velg" || location == '' || location == null) {
        showAlertMessage(select_location);
        if (object) {
            $(object).val('');
        }
        $(".warehouseorder_submit_btn").attr('disabled', true);
    } else {
        var order_type = $("#order_type").val();
        if (order_type == 2) {
            var warehouse = $("#warehouse").val();
            var product_id = $("#order_product_" + iteration + " option:selected").attr("id");
            var location_id = $("#product_location_" + iteration + " option:selected").attr("id");
            var qty = replaceComma($(object).val());
            var qty_val = parseFloat(replaceComma($(object).val()));
            if (qty.indexOf('-') == 0 && qty.length > 1) {
                displayBlockUI();
                if (warehouse) {
                    $.ajax({
                        type: 'POST',
                        url: checkproduct_exist_url,
                        data: {
                            _token: token,
                            'warehouse_id': warehouse,
                            'product_id': product_id,
                            'location_id': location_id
                        },
                        success: function(response) {
                            if (response) {
                                var decoded_data = $.parseJSON(response);
                                if (decoded_data['status'] == SUCCESS) {
                                    if (decoded_data['data'] == "") {
                                        showAlertMessage(product_not_avaliable)
                                        $(object).val('');
                                    } else if (decoded_data['qty'] < Math.abs(qty_val)) {
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
        }
        $(".warehouseorder_submit_btn").attr('disabled', false);
    }
}
// fill order_product table from JSON (JSON is in textarea)
function constructProductTableDetails() {
    displayBlockUI();
    var product_details = $("#product_details").val();
    var order_status = $("#order_status").val();
    var dom_string = "";
    var remove_default_location = true;
    var order_type = $("#order_type").val();
    var serial_number_length = 0;
    var destination_location_options = destinationLocationOptions("");
    if (product_details) {
        product_details = $.parseJSON(product_details);
        for (var j = 0; j < product_details.length; j++) {
            var product_options = getProductAsOptions(product_details[j]['product_id']);
            var location_options = '';
            var warehouse_options = '';
            var sn_val = 0;
            var product_dom = "<td><select disabled='disabled' sn_val='" + sn_val + "' data-val='1' class='form-control order_product' id='order_product_" + j + "'  onchange='productChange(this);'>" + product_options + "</select>";
            if (product_details[j]['is_package'] == 1 || product_details[j]['is_content'] == 1) {
                var content_product_options = "";
                var contents = product_details[j];
                content_product_options += "<option selected='selected' id=" + contents['product_id'] + ">" + contents['product_text'] + "</option>";
                var product_dom = "<td class='product_td'><select style='width:100% !important;' sn_val='" + sn_val + "' data-val='1' class='form-control product order_product' id=order_product_" + j + " readonly='readonly' disabled='disabled'>" + content_product_options + "'</select>";
                product_dom += "<input type='hidden' class='unique_package' id='unique_package_id_" + j + "' value=" + contents['unique_package'] + ">";
            } else {
                var product_dom = "<td><select style='width: 50%;' sn_val='" + sn_val + "'  data-val='1' class='select2 order_product' id='order_product_" + j + "'  onchange='productChange(this);'>" + product_options + "</select>";
            }
            product_dom += "<input type='hidden' class='is_package' id='is_package_" + j + "' value='" + product_details[j]['is_package'] + "'>";
            product_dom += "<input type='hidden' class='is_content'id='is_content_" + j + "' value='" + product_details[j]['is_content'] + "'>";
            if (product_details[j]['is_content'] == 1) {
                product_dom += "<input type='hidden' id='whs_product_package_id_" + j + "' value='" + product_details[j]['package_id'] + "'>";
            }
            product_dom += "<input type='hidden' id='whs_product_id_" + j + "' value='" + product_details[j]['whs_product_id'] + "'>";
            product_dom += "<input type='hidden' class='sn_required' id='sn_required_" + j + "' value='" + product_details[j]['sn_required'] + "'></td>";
            if (product_details[j]['sn_required'] != 0) {
                serial_number_length = product_details[j]['order_details'].length;
            }
            var location_dom = "";
            var quantity_dom = "<td><input style='width:100%;' type=text class='qty form-control numberWithSingleComma' id='qty_" + j + "' value='" + replaceDot(product_details[j]['qty']) + "')>";
            quantity_dom += "<input type='hidden' value=" + product_details[j]['ordered_date'] + " id='ordered_date_" + j + "'>";
            var received_quantity_dom = "<td class='rec_qty_td' style='display: none;''>";
            var received_location_dom = "<td class='rec_location_td' id='rec_location_td_" + j + "' style='display: none;'>";
            var received_date_dom = "<td class='rec_date_td' id='rec_date_td_" + j + "' style='display: none;'>";
            var rec_product_warehouse_dom = "<td class='rec_warehouse_td' id='rec_warehouse_td_" + j + "' style='display: none;'>";
            if (product_details[j]['qty'] != "") {
                if (product_details[j]['order_details'].length > 0) {
                    received_quantity_dom += constructReceivedQuantityDOM(product_details[j]['order_details'], j, product_details[j]['qty'], product_details[j], serial_number_length, product_details[j]['sn_required']);
                    var rec_qty_val = "";
                    for (var k = 0; k < product_details[j]['order_details'].length; k++) {
                        if (product_details[j]['order_details'][k]['serial_number_products'].length > 0) {
                            remove_default_location = false;
                            rec_qty_val = product_details[j]['order_details'][k]['serial_number_products'][0]['rec_qty'] ? product_details[j]['order_details'][k]['serial_number_products'][0]['rec_qty'] : "";
                            location_options = getLocationsForSupplierOrder(product_details[j]['order_details'][k]['serial_number_products'][0]['rec_location_id']);
                            warehouse_options = getWarehouseOptions(product_details[j]['order_details'][k]['serial_number_products'][0]['rec_warehouse_id'])
                            rec_product_warehouse_dom += "<select disabled='disabled' class='form-control rec_product_warehouse' id=rec_product_warehouse_" + j + k + "  >" + warehouse_options + "</select><br>";
                            received_location_dom += "<select disabled='disabled' class='form-control rec_product_location' id=rec_product_location_" + j + k + "  >" + location_options + "</select><br>";
                        }
                        received_date_dom += "<input class='form-control' type=text readonly disabled value=" + product_details[j]['order_details'][k]['received_date'] + "><br/>";
                    }
                } else { // The order is in draft status
                    received_quantity_dom += "<input readonly type=hidden id='received_date_" + j + "0' class='received_date_input form-control' >";
                    received_quantity_dom += "<input type=text  class='makeedit form-control numberWithSingleComma' onkeyup='generateSerialNumberDOM(this,\"serial_number_td_" + j + "\", \"rec_location_td_" + j + "\", " + j + " );'   id='received_quantity_" + j + "0" + "' ><br>";
                }
            } else {
                removeStatus();
            }
            received_quantity_dom += "</td>";
            received_location_dom += "</td>";
            rec_product_warehouse_dom += "</td>";
            received_date_dom += "</td>";
            var comment_dom = "<td class='product_comment_td'><input type=text class='form-control' id='comment_" + j + "' value='" + product_details[j]['comment'] + "' ></td>";
            var delete_td = "<td class='product_delete_td'><i class='fa fa-minus deleteHourloggBtn' id='delete_product_orders" + j + "' onclick=deleteProductRow(this,''); ></i></td>";
            if (product_details[j]['is_package'] == 1 || product_details[j]['is_content'] == 1) {
                dom_string += "<tr id= product_tr_" + j + " data-val=" + product_details[j]['unique_package'] + ">" + product_dom + location_dom + quantity_dom + comment_dom + received_quantity_dom + rec_product_warehouse_dom + received_location_dom + received_date_dom + delete_td + "</tr>";
            } else {
                dom_string += "<tr id= product_tr_" + j + " data-val=" + product_details[j]['package_id'] + ">" + product_dom + location_dom + quantity_dom + comment_dom + received_quantity_dom + rec_product_warehouse_dom + received_location_dom + received_date_dom + delete_td + "</tr>";
            }
            $("#hidden_warehouse_table_row_count").val((j + 1));
            window.localStorage.setItem("product_table_row_count", (j + 1));
        }
        $('#warehouse_product_order_table tbody').append(dom_string);
        setTimeout(function() {
            if (remove_default_location) { // remove product default location (if rec qty is not available)
                $(".rec_product_location").find('option').removeAttr('selected', '');
            }
            $(".select2").select2();
            if (order_status > 1 && order_status != '2') {
                $('.select2').css('pointer-events', 'none');
            }
            setTabIndex();
        }, 50);
    }
    setTimeout($.unblockUI, 100);
}
// remove status
function removeStatus() {
    $("#order_status option").each(function() {
        var order_type = $("#order_type").val();
        if (order_type == 2) {
            if ($(this).val() != '1' && $(this).val() != '2') {
                $(this).remove();
            }
        } else {
            if ($(this).val() != '1') {
                $(this).remove();
            }
        }
    });
    $("#order_status").trigger("change");
}
// construct received qunatity DOM
function constructReceivedQuantityDOM(data, iteration, order_quantity, product_details, serial_number_length, sn_required_val) {
    var dom_string = "";
    var received_quantity = 0;
    var new_iteration = 1;
    var order_type = $("#order_type").val();
    var tabindex = 0;
    var class_name = 'numberWithSingleComma';
    if (data) {
        for (var i = 0; i < data.length; i++) {
            var current_rec_qty = isNaN(parseFloat(data[i]['received_quantity'])) ? 0 : parseFloat(data[i]['received_quantity']);
            if (current_rec_qty) {
                new_iteration = i;
                received_quantity = received_quantity + current_rec_qty;
                if (order_type == 3 && !received_quantity || order_type == 3 && received_quantity == 'NaN') {
                    dom_string += "<input class='makeedit form-control " + class_name + "' data-order='supplier' type=text onkeyup='generateSerialNumberDOM(this,\"serial_number_td_" + iteration + "\", \"rec_location_td_" + iteration + "\", " + iteration + " );' id='received_quantity_" + iteration + "0' value=''><br>";
                } else {
                    if (data[i]['received_quantity']) {
                        dom_string += "<input readonly='readonly' data-order='supplier' type=text id='received_quantity_" + iteration + i + "' class='received_quantity_input form-control' value=" + replaceDot(data[i]['received_quantity']) + "><br>";
                    }
                }
                if (data[i]['serial_number_products'].length) {
                    dom_string += "<input style='width:80px;' readonly type=hidden id='received_date_" + iteration + i + "' class='received_date_input form-control' value=" + data[i]['received_date'] + ">";
                } else {
                    dom_string += "<input readonly type=hidden id='received_date_" + iteration + i + "' class='received_date_input form-control' value=''>";
                }
            }
        }
        new_iteration = new_iteration + 1;
        if (received_quantity > 0 && received_quantity < order_quantity) {
            dom_string += "<input class='makeedit form-control " + class_name + "' data-order='supplier' type=text onkeyup='generateSerialNumberDOM(this,\"serial_number_td_" + iteration + "\", \"rec_location_td_" + iteration + "\", " + iteration + "," + received_quantity + ");' id='received_quantity_" + iteration + new_iteration + "' value=''><br>";
            setTimeout(function() {
                $("#qty_" + iteration).removeAttr("readonly");
                $("#qty_" + iteration).attr('min', received_quantity);
                $("#qty_" + iteration).change(function() {
                    var min = parseFloat($("#qty_" + iteration).attr('min'));
                    if ($(this).val() < min) {
                        showAlertMessage(order_qty_not_less_rec_qty);
                        $(this).val(replaceDot(min));
                    }
                });
            }, 200);
        } else {
            setTimeout(function() {
                $("#qty_" + iteration).attr("readonly", true);
                $("#qty_" + iteration).attr("disabled", true);
            }, 500);
        }
    }
    return dom_string;
}
// change event for product
function productChange(obj) {
    var dom_id = $(obj).attr("id");
    var delete_qty_val = $(obj).attr("data-val");
    if (delete_qty_val == 1 || delete_qty_val == "1") {
        $(obj).removeAttr("data-val")
    } else {
        $(obj).closest('tr').find('.qty').val('');
    }
    $("#supplier").attr('disabled', true);
    $("#order_status option[value='2']").show();
}


// generate serial number DOM's
function generateSerialNumberDOM(obj, serial_number_td, rec_location_td, iteration, previous_rec_qty) {
    var rec_warehouse_td = "rec_warehouse_td_" + iteration;
    var received_qty = replaceComma($(obj).val());
    var actual_qty = replaceComma($("#qty_" + iteration).val());
    var total_qty = '';
    total_qty = received_qty;
    var order_type = $("#order_type").val();
    var serial_number_input = "";
    var received_location_input = "";
    if (previous_rec_qty && order_type != 2) {
        total_qty = parseFloat(received_qty) + parseFloat(previous_rec_qty);
    }
    if (order_type == 3) {
        if (total_qty && (parseFloat(total_qty) != actual_qty)) {
            if ($(obj).closest('tr').find('.is_package').val() == 1) {
                $(obj).val($("#qty_" + iteration).val());
                $(obj).focus();
                $(obj).trigger('keyup');
                return false;
            }
        }
    }
    if (order_type == 3 && (received_qty == '' || received_qty == undefined)) {
        if ($(obj).closest('tr').find('.is_package').val() == 1) {
            var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
            $(elements).each(function() {
                if ($(this).find('.is_content').val() == 1) {
                    $(this).closest('tr').find('.rec_location_td').find('.rec_product_location').html('')
                    $(this).closest('tr').find('.rec_location_td').find('.rec_product_location').attr('disabled', 'disabled');
                    $(this).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').html('')
                    $(this).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').attr('disabled', 'disabled');
                }
            });
        }
    }
    total_qty = parseFloat(total_qty);
    total_qty = total_qty.toFixed(2);
    if (total_qty > parseFloat(actual_qty) && order_type != 2) {
        showAlertMessage(rect_qty_not_greater_order_qty);
        var non_sn_location_index = 0;
        if (previous_rec_qty) {
            if ($(obj).closest('tr').find('.is_content').val() != 1) {
                $("#" + rec_location_td).find(".rec_product_location").each(function(index) {
                    if ($(this).attr('disabled')) {
                        non_sn_location_index = index + 1;
                    } else {
                        $("#rec_product_location_" + iteration + index).remove();
                        $("#rec_product_warehouse_" + iteration + index).remove();
                        $(this).remove();
                        $("#sn_br" + iteration + index).remove();
                        $('.rec_location_td').find("#rl_br" + iteration + index).remove();
                        $('.rec_warehouse_td').find("#rl_br" + iteration + index).remove();
                    }
                });
            } else {
                $(obj).closest('td').find("input").each(function(index) {
                    if ($(this).attr('data-order')) {
                        if ($(this).attr('readonly')) {
                            non_sn_location_index = index + 1;
                        } else {
                            $("#rec_product_location_" + iteration + non_sn_location_index).remove();
                            $("#rec_product_warehouse_" + iteration + non_sn_location_index).remove();
                            $("#sn_br" + iteration + non_sn_location_index).remove();
                            $('.rec_location_td').find("#rl_br" + iteration + non_sn_location_index).remove();
                            $('.rec_warehouse_td').find("#rl_br" + iteration + non_sn_location_index).remove();
                        }
                    }
                });
            }
        }
        $(obj).val("");
        $(obj).focus();
        $(obj).trigger('keyup');
        return false;
    }
    // remove the dynamic generated DOM
    var non_sn_location_index = 0;
    if (previous_rec_qty) {
        if ($(obj).closest('tr').find('.is_content').val() != 1) {
            $("#" + rec_location_td).find(".rec_product_location").each(function(index) {
                if ($(this).attr('disabled')) {
                    non_sn_location_index = non_sn_location_index + 1;
                } else {
                    //$("#rec_product_location_"+iteration+index).select2('destroy');
                    $("#rec_product_location_" + iteration + index).remove();
                    $("#rec_product_warehouse_" + iteration + index).remove();
                    $(this).remove();
                    $("#sn_br" + iteration + index).remove();
                    $('.rec_location_td').find("#rl_br" + iteration + index).remove();
                    $('.rec_warehouse_td').find("#rl_br" + iteration + index).remove();
                }
            });
        } else {
            $(obj).closest('td').find("input").each(function(index) {
                if ($(this).attr('data-order')) {
                    if ($(this).attr('readonly')) {
                        non_sn_location_index = non_sn_location_index + 1;
                    } else {
                        //$("#rec_product_location_"+iteration+index).select2('destroy');
                        $("#rec_product_location_" + iteration + non_sn_location_index).remove();
                        $("#rec_product_warehouse_" + iteration + non_sn_location_index).remove();
                        // $(this).remove();
                        $("#sn_br" + iteration + non_sn_location_index).remove();
                        $('.rec_location_td').find("#rl_br" + iteration + non_sn_location_index).remove();
                        $('.rec_warehouse_td').find("#rl_br" + iteration + non_sn_location_index).remove();
                    }
                }
            });
        }
    } else {
        $("#" + serial_number_td).html("");
        $("#" + rec_location_td).html("");
        $("#" + rec_warehouse_td).html("");
    }
    var destination_location_options = destinationLocationOptions("");
    var warehouse_options = getWarehouseOptions();
    if (Math.abs(received_qty)) {
        var serial_number_input = "";
        var received_location_input = "";
        var rec_product_warehouse = "";
        var x = 0;
        var location_unique_id = createUUID();
        var k = previous_rec_qty ? previous_rec_qty : 0;
        var tab_index = window.localStorage.getItem('received_serial_number_tab_index');
        for (var i = 0; i < received_qty; i++) {
            serial_number_input += "<input class='form-control supplier_serial_number' onchange='validateSerialNunmber(this);'  type=text id='serial_number_" + iteration + k + "' value=''><br id='sn_br" + iteration + k + "'>";
            tab_index = parseInt(tab_index) + 1;
            var iteration_val = iteration.toString() + k.toString();
            if (x == 0) {
                rec_product_warehouse += "<select  main_warehouse=1 unique_id='selected_warehouse_" + location_unique_id + "' class='form-control rec_product_warehouse' id=rec_product_warehouse_" + iteration + k + "  data-val=" + iteration_val + ">" + warehouse_options + "</select><br id='rl_br" + iteration + k + "'>";
                received_location_input += "<select common_id=" + location_unique_id + " rec_qty_val=" + received_qty + " main_location=1 unique_id='selected_location_" + location_unique_id + "'' class='form-control rec_product_location' id=rec_product_location_" + iteration + k + "  >" + destination_location_options + "</select><br id='rl_br" + iteration + k + "'>";
            } else {
                rec_product_warehouse += "<select  main_warehouse=0 unique_id='warehouse_" + location_unique_id + "' class='form-control rec_product_warehouse' id=rec_product_warehouse_" + iteration + k + "  data-val=" + iteration_val + " style='visibility:hidden'>" + warehouse_options + "</select><br id='rl_br" + iteration + k + "'>";
                received_location_input += "<select  main_location=0 unique_id='location_" + location_unique_id + "' class='form-control rec_product_location' id=rec_product_location_" + iteration + k + "   style='visibility:hidden'>" + destination_location_options + "</select><br id='rl_br" + iteration + k + "'>";
            }
            x++;
            k++;
            tab_index++;
        }
        window.localStorage.setItem('received_serial_number_tab_index', tab_index);
        var iteration_val = iteration.toString() + non_sn_location_index.toString();
        var rec_product_warehouse = "<select  class='form-control rec_product_warehouse' id=rec_product_warehouse_" + iteration + non_sn_location_index + "  data-val=" + iteration_val + ">" + warehouse_options + "</select><br id='rl_br" + iteration + non_sn_location_index + "'>";
        var received_location_input = "<select class='form-control rec_product_location' id=rec_product_location_" + iteration + non_sn_location_index + "  >" + destination_location_options + "</select><br id='rl_br" + iteration + non_sn_location_index + "'>";
        $("#" + rec_location_td).append(received_location_input);
        $("#" + rec_warehouse_td).append(rec_product_warehouse);
        if ($("#received_quantity_" + (iteration + 1) + "0").length) {
            $("a").attr("tabindex", -1);
        }
        setTimeout(function() {
            if ($(obj).closest('tr').find('.is_content').val() == 1) {
                var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
                $(elements).each(function() {
                    if ($(this).find('.is_package').val() == 1) {
                        $(obj).closest('tr').find('.rec_location_td').find('.rec_product_location').html('');
                        $(obj).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').html('');
                        if ($(this).find('.rec_product_location').val() != undefined && $(this).find('.rec_product_location').val() != 'Select') {
                            $(obj).closest('tr').find('.rec_location_td').find('.rec_product_location').html($(this).closest('tr').find('.rec_location_td').find('.rec_product_location').html());
                            $(obj).closest('tr').find('.rec_location_td').find('.rec_product_location').attr('disabled', 'disabled');
                            $(obj).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').html($(this).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').html());
                            $(obj).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').attr('disabled', 'disabled');
                        } else {
                            $(obj).closest('tr').find('.rec_location_td').find('.rec_product_location').html('')
                            $(obj).closest('tr').find('.rec_location_td').find('.rec_product_location').attr('disabled', 'disabled');
                            $(obj).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').html('')
                            $(obj).closest('tr').find('.rec_warehouse_td').find('.rec_product_warehouse').attr('disabled', 'disabled');
                        }
                    }
                });
            }
            if ($(obj).closest('tr').find('.is_package').val() == 1) {
                var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
                $(elements).each(function() {
                    if ($(this).find('.is_content').val() == 1) {
                        if (replaceComma($(this).find('.rec_qty_td').find('.makeedit').val()) > 0) {
                            $(this).find('.rec_qty_td').find('.makeedit').trigger('onkeyup');
                        }
                    }
                });
            }
        }, 100);
    }
}
$(document).on('focus', '.select2-container', function(e) {
    $(this).closest("select + *").prev().select2('open');
});
// remove serial number option
function removeSerialNumberOption(obj, obj_class) {
    var obj_id = $(obj).attr("id");
    var obj_val = $("#" + obj_id + " option:selected").attr("id");
    var obj_text = $(obj).val();
    $("." + obj_class + " option").each(function() {
        if ($(this).attr("id") == obj_val) {
            $(this).remove();
        }
    });
    $("#" + obj_id).append("<option selected id=" + obj_val + ">" + obj_text + "</option>");
}
/**
 * Warehouse form submit
 * Create and Update actions were done here
 * JSON consturcted to handle the product details
 */
$(document).on("click", ".warehouseorder_submit_btn", function() {
    var submit_btn_value = $(this).val();
    $('#submit_button_value').val(submit_btn_value);
    displayBlockUI();
    $("#product_details").val("");
    window.localStorage.setItem("stop_form_submit", "");
    var order_product_table_row_count = $("#hidden_warehouse_table_row_count").val();
    var product_data = [];
    var order_status = $("#order_status").val();
    var order_type = $("#order_type").val();
    if (order_status >= 2 && order_type == 3 && $("#destination_warehouse option:selected").val() == "") {
        setTimeout(function() {
            $.unblockUI();
        }, 100);
        showAlertMessage(destination_warehosue_is_required);
        return false;
    }
    if (order_status >= 2 && order_product_table_row_count == 0) {
        setTimeout(function() {
            $.unblockUI();
        }, 100);
        showAlertMessage(please_select_atleast_one_product);
        return false;
    }
    for (var i = 0; i <= order_product_table_row_count; i++) {
        if ($("#product_tr_" + i).length && $("#order_product_" + i).val() != "") {
            var order_details_array = [];
            var rec_qty = "";
            var new_rec_qty = "";
            if ($("#qty_" + i).val() != "") {
                var quantity_count = $("#qty_" + i).val();
                window.localStorage.setItem("received_quantity", 0);
                window.localStorage.setItem("previous_iteration", "");
                for (var j = 0; j > -1; j++) {
                    var serial_number_location_array = [];
                    if ($("#received_quantity_" + i + j).length != "") {
                        if (order_status >= 2) {
                            if ($("#destination_warehouse option:selected").val() == "") {
                                showAlertMessage("Select destination warehouse");
                                $("#destination_warehouse").attr('disabled', false);
                                setTimeout(function() {
                                    $.unblockUI();
                                }, 100);
                                return false;
                            }
                            // construct serail number array
                            if ($("#received_quantity_" + i + j).val() != "" && replaceComma($("#received_quantity_" + i + j).val()) != 0) {
                                serial_number_location_array = constrcutSerialNumberArray(Math.abs(replaceComma($("#received_quantity_" + i + j).val())), i);
                                if (!serial_number_location_array) {
                                    setTimeout(function() {
                                        $.unblockUI();
                                    }, 100);
                                    return false;
                                }
                                order_details_array.push({
                                    "received_quantity": replaceComma($("#received_quantity_" + i + j).val()),
                                    "received_date": $("#received_date_" + i + j).val() ? $("#received_date_" + i + j).val() : getCurrentDate('.'),
                                    "serial_number_products": serial_number_location_array,
                                    "newly_received": $("#received_date_" + i + j).val() ? false : true,
                                });
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
            if ($("#order_product_" + i + " option:selected").attr("id") && $("#order_product_" + i).val()) {
                var qty = $("#qty_" + i).val();
                var add_or_remove = 0;
                if (order_type == 2) {
                    add_or_remove = (qty > 0) ? 0 : 1;
                }
                product_data.push({
                    "product_id": $("#order_product_" + i + " option:selected").attr("id"),
                    "whs_product_id": $("#whs_product_id_" + i).val(),
                    "product_text": $("#order_product_" + i).val(),
                    "package_id": $("#whs_product_package_id_" + i).val(),
                    "is_package": $("#is_package_" + i).val(),
                    "unique_package": $("#unique_package_id_" + i).val(),
                    "is_content": $("#is_content_" + i).val(),
                    "location_id": $("#product_location_" + i + " option:selected").attr("id"),
                    "location_text": $("#product_location_" + i).val(),
                    "add_or_remove": add_or_remove,
                    "ordered_date": $("#ordered_date_" + i).val() ? $("#ordered_date_" + i).val() : getCurrentDate('.'),
                    "qty": replaceComma($("#qty_" + i).val()),
                    "comment": $("#comment_" + i).val(),
                    "sn_required": $("#sn_required_" + i).val(),
                    "order_details": order_details_array,
                    "ccsheet_id": window.localStorage.getItem('create_adjustment_order_id')
                });
            }
        }
    }
    if (product_data) {
        $("#product_details").val(JSON.stringify(product_data));
    }
    var form_submit = window.localStorage.getItem("stop_form_submit");
    if (form_submit == "") {
        $("#supplier").attr('disabled', false);
        $("#destination_warehouse").attr('disabled', false);
        $('input:disabled, select:disabled').each(function() {
            $(this).removeAttr('disabled');
        });
        window.localStorage.setItem('create_adjustment_order_id', '');
        $("#warehousesupplierorderform").submit();
    } else {
        setTimeout(function() {
            $.unblockUI();
        }, 100);
    }
});
// construct serail number array
function constrcutSerialNumberArray(received_quantity, iteration) {
    var serial_number_location_array = [];
    var new_iteration = 0;
    var previous_iteration = window.localStorage.getItem("previous_iteration");
    if (previous_iteration && previous_iteration == iteration) {
        var new_iteration = window.localStorage.getItem("received_quantity");
        new_iteration = parseInt(new_iteration) + 1;
    } else {
        new_iteration = 0;
    }
    if ($("#sn_required_" + iteration).val() == "1") {
        var order_type = $("#order_type").val();
        for (var j = 0; j < received_quantity; j++) {
            if (order_type != 2) {
                if (typeof $("#rec_product_location_" + iteration + new_iteration + " option:selected").attr("id") == "undefined" || $("#rec_product_location_" + iteration + new_iteration + " option:selected").attr("id") == "" || typeof $("#serial_number_" + iteration + new_iteration + "").attr("id") == "undefined") {
                    showAlertMessage(select_location);
                    return false;
                }
            }
            if (order_type == 2) {
                var new_iteration = j;
            }
            if ($("#serial_number_" + iteration + new_iteration + "").val() == "" || $("#serial_number_" + iteration + new_iteration + "").val() == "Select") {
                window.localStorage.setItem("stop_form_submit", "1");
                showAlertMessage(fill_serial_no);
                return false;
            }
            serial_number_location_array.push({
                'rec_location_id': $("#rec_product_location_" + iteration + new_iteration + " option:selected").attr("id"),
                'rec_warehouse_id': $("#rec_product_warehouse_" + iteration + new_iteration + " option:selected").attr("id"),
                'rec_warehouse_text': $("#rec_product_warehouse_" + iteration + new_iteration).val(),
                'rec_location_text': $("#rec_product_location_" + iteration + new_iteration).val(),
                'serial_number': $("#serial_number_" + iteration + new_iteration).val(),
                'serial_number_id': $("#serial_number_" + iteration + new_iteration + " option:selected").attr("id"),
            });
            window.localStorage.setItem("received_quantity", new_iteration);
            new_iteration = ++new_iteration;
            window.localStorage.setItem('previous_iteration', iteration);
        }
    } else {
        if (received_quantity) {
            if (typeof $("#rec_product_location_" + iteration + new_iteration + " option:selected").attr("id") == "undefined" || $("#rec_product_location_" + iteration + new_iteration + " option:selected").attr("id") == "") {
                showAlertMessage(product_location_validation);
                window.localStorage.setItem("stop_form_submit", "1");
                return false;
            }
            serial_number_location_array.push({
                'rec_location_id': $("#rec_product_location_" + iteration + new_iteration + " option:selected").attr("id"),
                'rec_warehouse_id': $("#rec_product_warehouse_" + iteration + new_iteration + " option:selected").attr("id"),
                'rec_warehouse_text': $("#rec_product_warehouse_" + iteration + new_iteration).val(),
                'rec_location_text': $("#rec_product_location_" + iteration + new_iteration).val(),
                'serial_number': $("#serial_number_" + iteration + new_iteration).val(),
            });
            window.localStorage.setItem("received_quantity", new_iteration);
            new_iteration = ++new_iteration;
            window.localStorage.setItem('previous_iteration', iteration);
        }
    }
    return serial_number_location_array;
}
// delete order_product
function deleteProductRow(obj, hourlogg_ids) {
    if ($(obj).closest('tr').find('.is_package').val() == 1) {
        $('#delete_package_button').attr('data-val', $(obj).closest('tr').attr('data-val'));
        $('#delete_package_button').trigger('click');
        return false;
    } else {
        var confirm_delete_msg = confirm(confirm_delete);
        if (confirm_delete_msg) {
            $(obj).closest('tr').remove();
        }
    }
}
// redirect to warehouse detail page
function redirectToWarehouseDetail(url) {
    window.location.href = url + "/activetab/3";
}

function updateOrderStatusValueToArchive(order_id_val) {
    // displayBlockUI();
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
                        $('#warehousetransferorderform a,.select2,#priority,button').attr('disabled', true);
                        $('#warehousetransferorderform .close,.btn-danger').attr('disabled', false);
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
 * [getPackageProducts description]
 * @param  {[type]} package_id [description]
 * @return {[type]}            [description]
 */
function getPackageProducts(package_id) {
    if (package_id) {
        var decoded_data = '';
        var product_url = url + "/ordermaterial/getPacakageProduct";
        if (package_id) { // if product is serial number required product
            $.ajax({
                type: "POST",
                url: product_url,
                asyc: true,
                data: {
                    '_token': token,
                    'rest': 'true',
                    'package_id': package_id,
                    'supplier_id': $('#supplier').val(),
                    'type': 1
                },
                success: function(response) {
                    decoded_response = $.parseJSON(response);
                    if (decoded_response['status'] == SUCCESS) {
                        decoded_data = decoded_response['data'];
                        generateProductPackageRow(decoded_data);
                    }
                },
                fail: function() {
                    console.log("Something Went Wrong");
                }
            });
        }
    }
}
/**
 * [generateProductPackageRow description]
 * @param  {[type]} product_package [description]
 * @return {[type]}                 [description]
 */
function generateProductPackageRow(product_package) {
    var i = window.localStorage.getItem("product_table_row_count");
    var location_options = getLocationAsOptions("");
    var uuid = createUUID();
    var unique_package_id = createUUID();;
    var product_package_options = "";
    if (product_package) {
        // product_package_options += "<option  selected='selected' id=" + product_package['id'] + ">" + product_package['product_number'] + " - " + product_package['description'] + "</option>";
        // var products = "<td class='product_td'><select style='width:100% !important;' class='form-control product order_product' id=order_product_" + i + " readonly='readonly' disabled='disabled'>" + product_package_options + "'</select><label class='labelProduct hide_div'>test</label></td>";
        // products += "<input type='hidden' id='whs_product_id_" + i + "' value='" + uuid + "'>";
        // products += "<input type='hidden' class='unique_package' id='unique_package_id_" + i + "' value=" + unique_package_id + ">";
        // products += "<input type='hidden' class='is_package' id='is_package_" + i + "' value='1'>";
        // products += "<input type='hidden' class='is_content' id='is_content_" + i + "' value='0'>";
        // var location = "";
        // var warehouse = "";
        // var order_type = $("#order_type").val();
        // var qty = "<td><input class='qty form-control validateNumbers package_qty' type=text id='qty_" + i + "' value=1></td>";
        // var comment = "<td class='product_comment_td'><input class='form-control' type=text id='comment_" + i + "' value='' ></td>";
        // var delete_td = "<td class='product_delete_td'><i class='fa fa-minus deleteHourloggBtn' id='delete_product_orders" + i + "' onclick=deleteProductRow(this,''); ></i></td>";
        // var html_string = "<tr id= product_tr_" + i + " data-val=" + unique_package_id + ">" + products + warehouse + location + qty + comment + delete_td + "</tr>";
        // $('#warehouse_product_order_table tbody:last').append(html_string);
        // i++;
        // $("#hidden_warehouse_table_row_count").val(i);
        window.localStorage.setItem("product_table_row_count", i);
        if (product_package['package_products'] != undefined && product_package['package_products'].length > 0) {
            for (var j = 0; j < product_package['package_products'].length; j++) {
                var uuid = createUUID();
                var content_product_options = "";
                var contents = product_package['package_products'][j];
                content_product_options += "<option selected='selected' id=" + contents['product_id'] + ">" + contents['product_number'] + " - " + contents['description'] + "</option>";
                var products = "<td class='product_td'><select style='width:100% !important;' class='form-control product order_product' id=order_product_" + i + " readonly='readonly' disabled='disabled'>" + content_product_options + "'</select><label class='labelProduct hide_div'>test</label></td>";
                products += "<input type='hidden' id='whs_product_id_" + i + "' value='" + uuid + "'>";
                products += "<input type='hidden' id='whs_product_package_id_" + i + "' value='" + product_package['id'] + "'>";
                products += "<input type='hidden' class='is_package' id='is_package_" + i + "' value='0'>";
                products += "<input type='hidden' class='unique_package' id='unique_package_id_" + i + "' value=" + unique_package_id + ">";
                products += "<input type='hidden'  class='sn_required' id='sn_required_" + i + "' value=" + contents['sn_required'] + ">";
                var location = "";
                var warehouse = "";
                var order_type = $("#order_type").val();
                if (contents['sn_required'] == 1) {
                    var qty = "<td><input class='qty form-control validateNumbers' type=text id='qty_" + i + "' value=" + contents['qty'] + "></td>";
                } else {
                    var qty = "<td><input class='qty form-control numberWithSingleComma' type=text id='qty_" + i + "' value=" + contents['qty'] + "></td>";
                }
                var comment = "<td class='product_comment_td'><input class='form-control' type=text id='comment_" + i + "' value='' ></td>";
                var delete_td = "<td class='product_delete_td'><i class='fa fa-minus deleteHourloggBtn' id='delete_product_orders" + i + "' onclick=deleteProductRow(this,''); ></i></td>";
                var html_string = "<tr id= product_tr_" + i + " data-val=" + unique_package_id + ">" + products + warehouse + location + qty + comment + delete_td + "</tr>";
                $('#warehouse_product_order_table tbody:last').append(html_string);
                i++;
                $("#hidden_warehouse_table_row_count").val(i);
                window.localStorage.setItem("product_table_row_count", i);
            }
        }
    }
    setTimeout(function() {
        $(".select2").select2();
    }, 100);
}
$(document).on('change', '.rec_product_location', function(e) {
    $("option[value=" + this.value + "]", this).attr("selected", true).siblings().removeAttr("selected")
    var order_type = $("#order_type").val();
    if (order_type == 3) {
        if ($(this).closest('tr').find('.is_package').val() == 1) {
            var elements = $('tr[data-val="' + $(this).closest('tr').attr('data-val') + '"]');
            $(elements).each(function() {
                if ($(this).find('.is_content').val() == 1) {
                    if (replaceComma($(this).find('.rec_qty_td').find('.makeedit').val()) > 0) {
                        $(this).find('.rec_qty_td').find('.makeedit').trigger('onkeyup');
                    }
                }
            });
        }
        var sn_required = $(this).closest('tr').find('.sn_required').val();
        if (sn_required == 1) {
            var splitted_id = $(this).attr('id').split("_");
            $('#serial_number_' + splitted_id[3]).trigger('change');
            if (replaceComma($(this).attr('rec_qty_val')) > 1 && $(this).attr('main_location') == 1) {
                var unique_id = $(this).attr('common_id');
                var location_html = $('select[unique_id="selected_location_' + unique_id + '"]').html();
                var warehouse_html = $('select[unique_id="selected_warehouse_' + unique_id + '"]').html();
                var location_elements = $('select[unique_id="location_' + unique_id + '"]');
                var warehouse_elements = $('select[unique_id="warehouse_' + unique_id + '"]');
                $(warehouse_elements).each(function() {
                    $(this).html('');
                    $(this).html(warehouse_html);
                });
                $(location_elements).each(function() {
                    $(this).html();
                    $(this).html(location_html);
                    $(this).trigger('change');
                });
            }
        }
    }
});
$("#delte_package_yes_btn").click(function() {
    var pacakge_id = $('#delete_package_button').attr('data-val');
    var elements = $('tr[data-val="' + pacakge_id + '"]');
    $(elements).each(function() {
        $(this).closest('tr').remove();
    });
    var pacakge_id = $('#delete_package_button').removeAttr('data-val');
    $("#delete_package_model").modal("hide");
});
$("#delete_package_no_btn").click(function() {
    var pacakge_id = $('#delete_package_button').attr('data-val');
    var elements = $('tr[data-val="' + pacakge_id + '"]');
    $(elements).each(function() {
        if ($(this).find('.is_package').val() == 1) {
            $(this).closest('tr').remove();
        }
    });
    var pacakge_id = $('#delete_package_button').removeAttr('data-val');
    $("#delete_package_model").modal("hide");
});

function getWarehouseOptions(warehouse_id) {
    warehouses_array = $("#warehouses_array").val();
    if (warehouse_id == '' || warehouse_id == null || warehouse_id == undefined) {
        warehouse_id = $('#supplier_warhouse_id').val();
    }
    var warehouses_options = "<option>Select</option>";
    if (warehouses_array != "null" && warehouses_array) {
        warehouses_array = $.parseJSON(warehouses_array);
        var warehouses_array_size = Object.keys(warehouses_array).length;
        var warehouse_obj_index = 0;
        $.each(warehouses_array, function(index, value) {
            if (warehouse_id == index) {
                warehouses_options += "<option id=" + index + " value=" + value + " selected='selected'>" + value + "</option>";
            } else {
                warehouses_options += "<option id=" + index + " value=" + value + ">" + value + "</option>";
            }
            warehouse_obj_index++;
        });
    }
    return warehouses_options;
}
$(document).on("change", ".rec_product_warehouse", function() {
    var dom_id = $(this).attr("id");
    var seleted_warehouse = $("#" + dom_id + " option:selected").attr("id");
    var selected_product = $(this).closest('tr').find('.order_product option:selected').attr("id");
    if (seleted_warehouse && selected_product) {
        var decoded_data = '';
        var location_url = url + "/location/getlocationbywarehouse/" + seleted_warehouse +"?product_id="+selected_product;
        if (seleted_warehouse) { // if product is serial number required product
            $.ajax({
                type: "GET",
                url: location_url,
                asyc: true,
                data: {},
                success: function(response) {
                    if (response) {
                        var jsonresult = $.parseJSON(response);
                        console.log(jsonresult, "jsonresult")
                        if (jsonresult['status'] == 'success') {
                            setLocations(jsonresult['location_details'], dom_id);
                        }
                    }
                },
                fail: function() {
                    console.log("Something Went Wrong");
                }
            });
        }
    }
});
/**
 * [setLocations description]
 * @param {[type]} equipments [description]
 */
function setLocations(locations, dom_id) {
    try {
        var iteration_val = $('#' + dom_id).attr('data-val');
        var location_id = "rec_product_location_" + iteration_val;
        $("#" + location_id).html(locations);
        $("#" + location_id).trigger('change');
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}
/**
 * [getLocationsForSupplierOrder description]
 * @param  {[type]} location_id [description]
 * @return {[type]}             [description]
 */
function getLocationsForSupplierOrder(location_id) {
    var all_locations_array = $("#all_locations_array").val();
    var locations_options = "<option>Select</option>";
    if (all_locations_array != "null" && all_locations_array) {
        all_locations_array = $.parseJSON(all_locations_array);
        var locations_array_size = Object.keys(all_locations_array).length;
        var location_obj_index = 0;
        $.each(all_locations_array, function(index, value) {
            if (location_id == index) {
                locations_options += "<option id=" + index + " value=" + value + " selected='selected'>" + value + "</option>";
            } else {
                locations_options += "<option id=" + index + " value=" + value + ">" + value + "</option>";
            }
            location_obj_index++;
        });
    }
    return locations_options;
}
$(document).on("change", "select", function() {
    if ($(this).attr('class') == 'form-control rec_product_location' || $(this).attr('class') == 'form-control rec_product_warehouse') {
        $("option[value=" + this.value + "]", this).attr("selected", true).siblings().removeAttr("selected")
    }
});
/* warehous
/* warehouse order script logic end */