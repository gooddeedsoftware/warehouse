var transfer_transfer_product_array;
var transfer_location_array;
window.localStorage.setItem("transfer_product_table_row_count", 0);
window.localStorage.setItem("transfer_order_status", "");
localStorage.setItem('warehouse_activeTab', '#list_orders');

setTabIndex();

// show blockUI
function displayBlockUI() {
    $.blockUI({message:"Loading...",baseZ: 1000, css: {border: 'none',padding: '15px',backgroundColor: '#000','-webkit-border-radius': '10px','-moz-border-radius': '10px',opacity: .5,color: '#fff'}});
}

// edit warehouse order
function editTransferWarehouseOrder(order_type, order_status,url) {
    //$('#edit_order').modal('show').find('.modal-dialog').load(url, function () {
        window.localStorage.setItem("transfer_product_table_row_count",0);
        if (order_status > 1 || $("#transfer_source_warehouse").val()) {
            displayBlockUI();
        }
        setTimeout(
            function () {
                showHideTransferOrderFields(order_type);
                constructTransferProductTableDetails();
                window.localStorage.setItem("transfer_order_status", order_status);
                if (order_status >= 3 && order_status != 7) {
                    disbleTransferFormFields();
                    if (order_status == 5 || order_status == 6) {
                        $('#warehousetransferorderform input,textarea,select,a,.select2').attr('readonly', true);
                        $('#warehousetransferorderform a,.select2,#priority,button').attr('disabled', true);
                        $('#warehousetransferorderform .close,.btn-danger').attr('disabled', false);
                        $('.order_qty').attr('disabled', 'disabled');   // Added By david
                    }
                } else if (order_status == 7) { // if order status is request then hide the header fields in warehouse order_status
                    $(".warehosue_header_fields").attr('readonly', true);
                    $(".product_delete_td").html("");
                    $("#transfer_cloneProductTableBtn").hide();
                }
                setTabIndex();
            }, 3000
        );
    //});

}

/**
 * setTabIndex for input fields
 */
function setTabIndex() {
    $(":input[readonly='readonly']").each(function (i) {
        $(this).attr('tabindex', '-1');
    });

    $(":input:not([readonly='readonly'])").each(function (i) {
        $(this).attr('tabindex', i + 1);
    });
}

// show/hide order fields depend upon order type
function showHideTransferOrderFields(order_type) {
    hideOrShowTransferElements({'source_warehouse_div': 1 , 'destination_warehouse_div' : 1, 'warehouse_div' : 1, 'supplier_div' : 1, 'order_qty_symbol' : 1 , "location_th": 1 ,"source_warehouse_transfer_select" : 1, "receive_order_btn" : 1});
    switch (order_type) {
        case "1": // transfer
        case 1:
            hideOrShowTransferElements({'source_warehouse_div' : 0, 'destination_warehouse_div' : 0, 'source_warehouse_transfer_select' : 0 , 'source_warehouse_select' : 1});
            break;
        default:
            hideOrShowTransferElements({'source_warehouse_div' : 0, 'destination_warehouse_div': 0 });
            break;
    }
}

// find order status
$(document).on("change", "#transfer_order_status", function () {

    $('#transfer_order_status_hidden_value').val($(this).val());

    var order_status = $(this).val();
    var order_id_val = $('#trasnfer_order_id_value').val();
    if (order_status >= 3 && order_status != 7) {
        //$(".makeedit").trigger("change");
        var previous_order_status = window.localStorage.getItem("transfer_order_status");
        if (previous_order_status != "7" && previous_order_status > order_status) {
            alert(notallowed_to_change_the_status);
            $(this).val(previous_order_status);
            return false;
        } else {

            if (order_status == 6){
                confirm_msg = confirm(arichive_confimation_message);
                if (confirm_msg) {
                    updateStatusValueToArchive(order_id_val);
                    disbleTransferFormFields();
                } else {
                    var order_status = $(this).val('5');
                    return false;
                }
            } else {
                disbleTransferFormFields();
            }
        }
    } else if (order_status == 7) { // if order status is request then hide the header fields in warehouse order_status
        enableTransferFormFields();
        $(".warehosue_header_fields").attr('disabled', true);
        $(".product_delete_td").html("");
        $("#transfer_cloneProductTableBtn").hide();
        $(".order_product").attr('disabled', true);
        $('.order_product, .select2').css('pointer-events', 'none');
    } else {
        enableTransferFormFields();
    }
    if (order_status == 3) {
        $(".product_location").removeAttr('readonly');

        $('.product_location').each(function () {
            try {
                $($(this).children()[1]).prop('selected', 'selected');
            } catch (e){

            }
        });

    }
});


function updateStatusValueToArchive(order_id_val){
    // displayBlockUI();
    var order_id_val = order_id_val;
    var trasnfer_order_url = url+"/warehouseorder/updateStatusToArchive";
    if (order_id_val) {
        $.ajax({
            type: "POST",
            url: trasnfer_order_url,
            async : false,
            data : {
                '_token': token,
                'rest' : 'true',
                "order_id_val" : order_id_val,
            },
            success: function (response) {
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
                alert("Something went wrong");
                // setTimeout($.unblockUI, 1000);
            }

        });
    }

}
// disable form fields
function disbleTransferFormFields () {
    $('#warehousetransferorderform input,textarea,select,a,.select2').attr('readonly', true);
    $('#warehousetransferorderform a,.select2,#priority').attr('disabled', true);
    $('#warehousetransferorderform .select2').css('pointer-events', 'none');
    $("#transfer_cloneProductTableBtn").hide();
    $("#location_th, .product_location_td").show();
    $(".picked_qty_td").show();
    $("#rec_qty_td").find('input[type=text][readonly]').attr('readonly', true);
    $(".rec_qty_td").find('input[class=makeedit]').attr('readonly', false);
    $(".picked_qty_td").find('input[type=text][readonly]').attr('readonly', true);
    $(".picked_qty_td").find('input[class=makeedit]').attr('readonly', false);
    $(".picked_qty_td").find('input[class="makeedit picked_quantity_input form-control"]').attr('readonly', false);
    $(".picked_qty_td").find('input[class="makeedit picked_quantity_input validateNumbers form-control"]').attr('readonly', false);
    $(".picked_qty_td").find('input[class="makeedit picked_quantity_input numberWithSingleComma form-control"]').attr('readonly', false);
    $(".product_location, #receive_order_btn").attr('disabled', false);
    $(".rec_product_location").attr('disabled', false);

    $('#transfer_order_status').attr('disabled', 'disabled');
    // $('#transfer_order_status').attr('readonly', false);
    // $("#order_comment").attr("readonly", false);
    $("#order_comment").attr("disabled", "disabled");//Added By david
    $(".warehosue_header_fields").attr('disabled', true);

    var order_status = $("#transfer_order_status").val();
    $(".product_delete_td").html("");
    if (order_status == "3") {
        $(".product_comment_td").find('input').attr("readonly", false);
    }
    //Added by David
    if (order_status != 6){
        $(".serial_number_td, .rec_qty_td").hide();

    } else {
        $('#transfer_order_status').attr('disabled', 'disabled');
    }

    if (order_status == 5){
        $('#transfer_order_status').attr('disabled', false);
    }
    //$('.order_qty').find('input').attr('readonly', false);
}

// enable the form fields
function enableTransferFormFields () {
    $('#warehousetransferorderform input,textarea,select,a,.select2').attr('readonly', false);
    $('#warehousetransferorderform a,.select2,#priority, #receive_order_btn').attr('disabled', false);
    $('#warehousetransferorderform .select2').css('pointer-events', '');
    $(".product_comment_td").show();
    $(".rec_qty_td, .rec_location_td, .rec_date_td, .serial_number_td, .picked_qty_td, #receive_order_btn, .location_th, .product_location_td, #location_th").hide();
    $(".rec_location_td").find('.select2').attr('disabled', false);
    $("#transfer_cloneProductTableBtn").show();
    $(".rec_qty_td").find('input').attr('readonly', false);
    $(".picked_qty_td").find('input').attr('readonly', false);
    $(".serial_number_td").find('input').attr('readonly', true);
    $(".rec_location_td").find('.select2').attr('disabled', true);
    $('#warehousetransferorderform #transferorder_submit_btn').attr('disabled', false);
    $(".rec_qty_td, .rec_location_td, .rec_date_td, .serial_number_td, .picked_qty_td, #receive_order_btn, .rec_qty_td").hide();
    $(".warehosue_header_fields").attr('disabled', false);
    $('.order_product, .select2').css('pointer-events', '');
}

// common function to hide elements
function hideOrShowTransferElements(element_array) {
    if (element_array) {
        $.each (element_array, function (index, value) {
            if ($("#"+index).length > 0) {
                if (value == "1") {
                    $("#"+index).hide();
                } else {
                    $("#"+index).show();
                }
            }
        });
    }
}

// clear order_product table if order_type is changed
$(document).on("change", "#warehouse", function() {
    $('#warehouse_product_order_table tbody').html("");
});

// select product depend upon the supplier
$(document).on("change", "#supplier", function() {
    var supplier = $(this).val();
    $('#warehouse_product_order_table tbody').html("");
    getTransferProdcuts(supplier, "");
});

// select product depend upon the source warehouse
$(document).on("change", "#transfer_source_warehouse", function() {
    var warehouse = $(this).val();
    $('#warehouse_product_order_table tbody').html("");
    getTransferProdcuts("", warehouse);
});

// get products
function getTransferProdcuts(supplier, warehouse) {
    var warehouse_order_url = url+"/product/getProductDetailFromOrderType";
    var order_type = $("#order_type").val();
    if (order_type) {
        $.ajax({
            type: "POST",
            url: warehouse_order_url,
            async : false,
            data : {
                '_token': token,
                'rest' : 'true',
                "order_type" : order_type,
                "supplier_id" : supplier,
                "warehouse_id" : warehouse,
            },
            success: function (response) {
                if (response) {
                    decoded_response = $.parseJSON(response);
                    if (decoded_response['status'] == SUCCESS) {
                        transfer_product_array = decoded_response['data']['products'];
                        transfer_location_array = decoded_response['data']['locations'];
                        $("#products_array").text(JSON.stringify(transfer_product_array));
                        $("#locations_array").text(JSON.stringify(transfer_location_array));
                    } else if (decoded_response['status'] == ERROR) {
                        alert(decoded_response['data']);
                    }
                }
            },
            fail: function() {
                alert("Something went wrong");
            }
        });
    }
}

// create new row in product detasil table
function createNewTransferProductTableRow() {
    var i = window.localStorage.getItem("transfer_product_table_row_count");
    var product_options = getProductAsOptions("");
    var location_options = getLocationAsOptions("");
    var products = "<td><select style='width:100%;' class='select2 order_product' id=order_product_"+i+ "  onchange='trasferOrderproductChange(this);'>"+product_options+"</select>";
    var uuid = createUUID();
    products += "<input type='hidden' id='whs_product_id_"+i+"' value='"+uuid+"'>";
    products += "<input type='hidden' id='sn_required_"+i+"' value=''></td>";
    var order_type = $("#order_type").val();
    var location = "";
    var qty = "<td class='order_qty'><input class='form-control transfer_order_qty validateNumbers' type=text id='qty_"+i+"' value=''/></td>";
    var comment = "<td class='product_comment_td'><input class='form-control' type=text id='comment_"+i+"' value='' ></td>";
    var delete_td = "<td class='product_delete_td'><i class='glyphicon glyphicon-minus deleteHourloggBtn' id='delete_product_orders"+i+"' onclick=deleteTransferProductRow(this,''); ></i></td>";
    var html_string = "<tr id= product_tr_"+i+">"+products+location+qty+comment+delete_td+"</tr>";
    $('#warehouse_product_order_table tbody:last').append(html_string);
    i++;
    $("#hidden_transfer_table_row_count").val(i);
    window.localStorage.setItem("transfer_product_table_row_count",i);
    setTimeout(function() {
        $(".select2").select2();
    },100);
    removeStatus();
}

// fill order_product table from JSON (JSON is in textarea)
function constructTransferProductTableDetails() {
    var product_details = $("#product_details").val();
    var dom_string = "";
    var remove_default_location = true;
    var order_type = $("#order_type").val();
    var destination_location_options = destinationLocationOptions("");
    var enable_rec_pic_qty_array = [];
    var order_status = $("#transfer_order_status").val();
    var show_rec_qty_and_location = false;
    var picked_quantity = 0 ;
    var received_quantity = 0;
    var serial_number_length = 0;

    try {
        if (product_details) {
            product_details = $.parseJSON(product_details);
            var order_length = window.localStorage.setItem("order_length", "");
            for (var j = 0 ; j < product_details.length; j++) {
                window.localStorage.setItem("existing_iteration", "");
                var product_options = getProductAsOptions(product_details[j]['product_id']);
                var location_options = getLocationAsOptions(product_details[j]['location_id']);
                if (order_status >= 3) {
                    var product_dom = "<td><select style='width:100%;' data-val='1' sn_val='"+product_details[j]['sn_required']+"' class='order_product form-control' disabled='disabled' id='order_product_"+j+"'  onchange='trasferOrderproductChange(this);'>"+product_options+"</select>";
                } else {
                    var product_dom = "<td><select style='width:100%;' data-val='1' sn_val='"+product_details[j]['sn_required']+"' class='select2 order_product ' id='order_product_"+j+"'  onchange='trasferOrderproductChange(this);'>"+product_options+"</select>";
                }
                product_dom += "<input type='hidden' id='whs_product_id_"+j+"' value='"+product_details[j]['whs_product_id']+"'>";
                product_dom += "<input type='hidden' id='sn_required_"+j+"' value='"+product_details[j]['sn_required']+"'></td>";

                var quantity_dom = "<td class='order_qty'><input class='form-control order_qty numberWithSingleComma transfer_order_qty' type=text id='qty_"+j+"' value='"+replaceDot(product_details[j]['qty'])+"' style='width:100% !important;' onkeyup='checkPickedQuantity(this, "+product_details[j]['qty']+", event);'>";
                quantity_dom += "<input type='hidden' value="+product_details[j]['ordered_date']+" id='ordered_date_"+j+"'> </td>";
                var location_dom = "<td class='product_location_td'>";

                if (product_details[j]['sn_required'] != 0){
                    serial_number_length = product_details[j]['serial_number_array'].length;
                }
                if (product_details[j]['serial_number_array'].length > 0) {
                    $(".serial_number_td").show();
                    location_dom += "<select style='width:100%;padding-left:2px;'  disabled='disabled' class='form-control' onchange='transferOrderLocationChange(this);' id=product_location_"+j+ "  >"+location_options+"</select></td>";
                } else {
                    /*if (order_status == 7 || order_status == 1) {
                        location_dom += "<select style='width:100%;' class='form-control product_location' onchange='transferOrderLocationChange(this);' id=product_location_"+j+ "  ></select></td>";
                    } else {*/
                        location_dom += "<select style='width:100%;' class='form-control product_location' onchange='transferOrderLocationChange(this);' id=product_location_"+j+ "  >"+location_options+"</select></td>";
                    //}
                }
                if (product_details[j]['location_id'] && order_status != 7) {
                    $(".picked_qty_td").show();
                    var picked_quantity_dom = "<td class='picked_qty_td' >";
                } else {
                    var picked_quantity_dom = "<td class='picked_qty_td' style='display: none;'>";
                }
                var received_quantity_dom = "<td class='rec_qty_td' style='display: none;''>";
                var serial_number_dom = "<td class='serial_number_td' id='serial_number_td_"+j+"' style='display: none;'>";
                if (product_details[j]['order_details'].length > 0 && product_details[j]['order_details'][0]['received_quantity'] > 0) {
                    var rec_location = "<td id='rec_location_td_"+j+"'>";
                    var rec_date = "<td id='rec_date_td_"+j+"'>";
                } else {
                    var rec_location = "<td class='rec_location_td' id='rec_location_td_"+j+"' style='display: none;'>";
                    var rec_date = "<td class='rec_daten_td' id='rec_daten_td_"+j+"' style='display: none;'>";
                }
                if (product_details[j]['qty'] > 0 ) {
                    if (product_details[j]['order_details'].length > 0) {
                        picked_quantity_dom += constructTransferPickedQuantityDOM(product_details[j]['order_details'], j, product_details[j]['qty'], serial_number_length, product_details[j]['sn_required']);
                        received_quantity_dom += constructTransferReceivedQuantityDOM(product_details[j]['order_details'], j, product_details[j]['qty'],serial_number_length, product_details[j]['sn_required']);
                        if (product_details[j]['sn_required'] == "1") { // if product is sn_required
                            var iteration = 0;
                            var global_iteration = 0;
                            var new_iteration = 0;
                            var new_iteration_non_qty = 0;
                            for (var k = 0; k < product_details[j]['order_details'].length; k++) {
                                if (product_details[j]['order_details'][k]['serial_number_products'].length > 0) {
                                    remove_default_location = false;
                                    for (var i = 0; i < product_details[j]['order_details'][k]['serial_number_products'].length; i++) {
                                        var location_options = getLocationAsOptions(product_details[j]['order_details'][k]['serial_number_products'][i]['rec_location_id']);
                                        var destination_location_options = destinationLocationOptions(product_details[j]['order_details'][k]['serial_number_products'][i]['rec_location_id']);
                                        serial_number_value = product_details[j]['order_details'][k]['serial_number_products'][i]['serial_number'] ? product_details[j]['order_details'][k]['serial_number_products'][i]['serial_number'] : "";
                                        serial_number_id = product_details[j]['order_details'][k]['serial_number_products'][i]['serial_number_id'] ? product_details[j]['order_details'][k]['serial_number_products'][i]['serial_number_id'] : "";
                                        serial_number_dom += "<select style='width:100%;' class='select2' disabled='disabled' id='serial_number_"+j+iteration+"'><option selected='selected' id='"+serial_number_id+"'>"+serial_number_value+"</option></select><br></br>";
                                        if (product_details[j]['order_details'][k]['serial_number_products'][i]['rec_location_id'] ) {
                                            rec_location += "<select style='width:100%;' disabled='disabled' class='form-control disable_location rec_product_location' id=rec_product_location_"+j+iteration+ "  >"+destination_location_options+"</select><br>";
                                        } else {
                                            rec_location += "<select style='width:100%;display:none;' disabled='disabled' class='form-control disable_location rec_product_location' id=rec_product_location_"+j+iteration+ "  >"+destination_location_options+"</select><br>";
                                        }
                                        rec_date += "<input class='form-control' type=text readonly disabled value="+product_details[j]['order_details'][k]['received_date']+"><br/>";
                                        iteration = ++iteration;
                                        enable_rec_pic_qty_array.push(j);
                                        $(".rec_location_td").show();
                                        $(".rec_date_td").show();                                   
                                        $(".rec_qty_td").show();
                                        show_rec_qty_and_location = true;
                                        global_iteration = iteration;
                                    }
                                } else {
                                    if (product_details[j]['order_details'][k]['received_quantity'] == "" && product_details[j]['serial_number_array'].length > 0  || typeof product_details[j]['received_quantity'] == "undefined" ) { // if order is picked(that is picked quantity is availbale)
                                        new_iteration = global_iteration;
                                        s = k;
                                        if (product_details[j]['serial_number_array'].length > 1) {
                                            if (product_details[j]['order_details'].length > 1 ) {
                                                for (t = 0; t < product_details[j]['order_details'][k]['picked_quantity']; t++) {
                                                    serial_number_value = product_details[j]['serial_number_array'][new_iteration]['serial_number'] ? product_details[j]['serial_number_array'][new_iteration]['serial_number'] : "";
                                                    serial_number_id = product_details[j]['serial_number_array'][new_iteration]['serial_number_id'] ? product_details[j]['serial_number_array'][new_iteration]['serial_number_id'] : "";
                                                    serial_number_dom += "<select style='width:100%;' class='select2' disabled='disabled' id='serial_number_"+j+new_iteration+"'><option selected='selected' id='"+serial_number_id+"'>"+serial_number_value+"</option></select><br><br>";
                                                    var location_options = destinationLocationOptions("");
                                                    rec_location += "<select style='width:100%;display:none;' disabled='disabled' class='form-control rec_product_location' id=rec_product_location_"+j+new_iteration+ "  >"+location_options+"</select><br>";
                                                    rec_date += "<input class='form-control' type=text readonly disabled value="+product_details[j]['order_details'][k]['received_date']+"><br/>";
                                                    new_iteration = ++new_iteration;
                                                    global_iteration = ++global_iteration;
                                                }
                                            } else {
                                                for (t = 0; t < product_details[j]['serial_number_array'].length; t++) {
                                                    serial_number_value = product_details[j]['serial_number_array'][new_iteration]['serial_number'] ? product_details[j]['serial_number_array'][new_iteration]['serial_number'] : "";
                                                    serial_number_id = product_details[j]['serial_number_array'][new_iteration]['serial_number_id'] ? product_details[j]['serial_number_array'][new_iteration]['serial_number_id'] : "";
                                                    serial_number_dom += "<select style='width:100%;' class='select2' disabled='disabled' id='serial_number_"+j+new_iteration+"'><option selected='selected' id='"+serial_number_id+"'>"+serial_number_value+"</option></select><br><br>";
                                                    var location_options = destinationLocationOptions("");
                                                    rec_location += "<select disabled='disabled' style='display:none;' class='form-control rec_product_location' id=rec_product_location_"+j+new_iteration+ "  >"+location_options+"</select><br>";
                                                    rec_date += "<input class='form-control' type=text readonly disabled value="+product_details[j]['order_details'][k]['received_date']+"><br/>";
                                                    new_iteration = ++new_iteration;
                                                    global_iteration = ++global_iteration;
                                                }
                                            }
                                        } else {
                                            serial_number_value = product_details[j]['serial_number_array'][s]['serial_number'] ? product_details[j]['serial_number_array'][s]['serial_number'] : "";
                                            serial_number_id = product_details[j]['serial_number_array'][s]['serial_number_id'] ? product_details[j]['serial_number_array'][s]['serial_number_id'] : "";
                                            serial_number_dom += "<select style='width:100%;' class='select2' disabled='disabled' id='serial_number_"+j+new_iteration+"'><option selected='selected' id='"+serial_number_id+"'>"+serial_number_value+"</option></select><br></br>";
                                            var location_options = destinationLocationOptions("");
                                            rec_date += "<input class='form-control' type=text readonly disabled value="+product_details[j]['order_details'][k]['received_date']+"><br/>";
                                            rec_location += "<select disabled='disabled' style='display:none;width:100%;' class='form-control rec_product_location' id=rec_product_location_"+j+new_iteration+ "  >"+location_options+"</select>";
                                        }
                                        enable_rec_pic_qty_array.push(j);
                                    } else {
                                        new_iteration_non_qty = new_iteration;
                                        serial_number_dom += "<select style='width:100%;' class='select2 disable_location' disabled='disabled' id='serial_number_"+j+new_iteration_non_qty+"'><option selected='selected' id=''>Select</option></select><br>";
                                        rec_date += "<input class='form-control' type=text readonly disabled value="+product_details[j]['order_details'][k]['received_date']+"><br/>";
                                        rec_location += "<select disabled='disabled' style='width:100%;display:none;' class='form-control rec_product_location' id=rec_product_location_"+j+new_iteration_non_qty+ "  >"+location_options+"</select><br>";
                                    }
                                }
                            }
                        } else {
                            if (product_details[j]['order_details'].length < 0) {
                                var destination_location_options = destinationLocationOptions("");
                                rec_location += "<select style='width:100%;padding-left:2px;' disabled='disabled' class='form-control rec_product_location' id=rec_product_location_"+j+"0"+ "  >"+destination_location_options+"</select><br>";
                            }
                        }
                    } else {
                        picked_quantity_dom += "<input readonly type=hidden id='picked_date_"+j+"0' class='picked_date_input' >";
                        if (product_details[j]['sn_required'] == "1") {
                            picked_quantity_dom += "<input style='width:100%;'  type=text class='makeedit picked_quantity_input validateNumbers form-control'  onchange='generateSerialNumberDOMForTransferOrder(this,\"serial_number_td_"+j+"\", \"rec_location_td_"+j+"\", "+j+" );' id='picked_quantity_"+j+"0' value=''><br>";
                        } else {
                            picked_quantity_dom += "<input style='width:100%;' type=text class='makeedit picked_quantity_input numberWithSingleComma form-control'  onchange='generateSerialNumberDOMForTransferOrder(this,\"serial_number_td_"+j+"\", \"rec_location_td_"+j+"\", "+j+" );'   id='picked_quantity_"+j+"0"+"' ><br>";
                        }
                        received_quantity_dom += "<input type=text class='form-control' readonly='readonly' id='received_quantity_"+j+"0' ><br>";
                    }
                    if (product_details[j]['sn_required'] == "1") { // if product is sn_required
                    } else { // for normal products(not sn required products)
                        var rec_qty_val = "";
                        // need to construct new destination location(To avoid selected option)
                        if (product_details[j]['order_details'].length > 0) {
                            for (var k = 0; k < product_details[j]['order_details'].length; k++) {
                                if (product_details[j]['order_details'][k]['serial_number_products'].length > 0) {
                                    var location_options = destinationLocationOptions(product_details[j]['order_details'][k]['serial_number_products'][0]['rec_location_id']);
                                    rec_location += "<select style='width:100%;padding-left:2px;' id='rec_product_location_"+j+k+"'  class='form-control'  >"+location_options+"</select><br>";
                                    rec_date += "<input class='form-control' type=text readonly disabled value="+product_details[j]['order_details'][k]['received_date']+"><br/>";
                                    show_rec_qty_and_location = true;
                                    $(".rec_location_td").show();
                                    $(".rec_date_td").show();
                                    $(".rec_qty_td").show();
                                } else {
                                    rec_location += "<select disabled='disabled' style='display:none;width:100%;' class='form-control rec_product_location' id=rec_product_location_"+j+k+ "  >"+destination_location_options+"</select><br>";
                                }
                                enable_rec_pic_qty_array.push(j);
                            }
                        }
                    }
                } else {
                    removeStatus();
                }
                received_quantity_dom += "</td>";
                picked_quantity_dom += "</td>";
                rec_location += "</td>";
                rec_date += "</td>";
                var comment = "<td class='product_comment_td'><input class='form-control' type=text id='comment_"+j+"' value='"+product_details[j]['comment']+"' ></td>";
                var delete_td = "<td class='product_delete_td'><i class='glyphicon glyphicon-minus deleteHourloggBtn' id='delete_product_orders"+j+"' onclick=deleteTransferProductRow(this,''); ></i></td>";
                dom_string += "<tr id= product_tr_"+j+">"+product_dom+quantity_dom+comment+location_dom+picked_quantity_dom+received_quantity_dom+rec_location+rec_date+delete_td+"</tr>";
                $("#hidden_transfer_table_row_count").val((j+1));
                window.localStorage.setItem("transfer_product_table_row_count",(j+1));
            }
            $('#warehouse_product_order_table tbody').append(dom_string);
            // set timeout used for tirgger option after the values are filled
            setTimeout(function () {
                if (order_status != 5) {
                    $(".order_product").trigger("change");
                }
                $("input[type=text][class=picked_qty][readonly]").trigger('change');
                $('.disable_location').attr('disabled', true);
                $(".select2").select2();
                // if serial number is picked then enable the serial number td and receive button
                if (enable_rec_pic_qty_array.length > 0) {
                    for (var s = 0 ; s < enable_rec_pic_qty_array.length; s++) {
                        showRecAndPicQtytr("product_location_"+enable_rec_pic_qty_array[s]);
                    }
                    $(".serial_number_td").show();
                    $("#receive_order_btn").show();
                }
                // show received quantity and received location td if received qunatity is available
                if (show_rec_qty_and_location) {
                    $(".rec_qty_td").show();
                }
                // hide received location if location is not selected
                $(".rec_product_location").each(function () {
                    if ($(this).val() == "" || $(this).val() == "Select" || $(this).val() == "Velg") {
                        rec_prod_location = $(this).attr("id");
                        $("#"+rec_prod_location).hide();
                        $(this).hide();
                    }
                });
                // hide received qty
                $(".rec_qty_td").find('input').each(function () {
                    if($(this).val() == "") {
                        $(this).hide();
                    }
                });
                if (order_status == "1" || order_status == "7") {// hide the location,picked_qty, serial_number if status is draft or request
                    $(".product_location_td").hide();
                    $(".picked_qty_td").hide();
                    $(".serial_number_td").hide();
                    $(".receive_order_btn").hide();
                } else if (order_status == 3) {
                    $(".product_location").removeAttr('readonly');
                }
                $(".picked_quantity_input").each(function () {
                    if ($(this).val()) {
                        var cur_val = replaceComma($(this).val());
                        cur_val = parseFloat(cur_val);
                        picked_quantity = parseFloat(picked_quantity) + cur_val;
                    }
                });
                $(".received_quantity_input").each(function () {
                    if ($(this).val()) {
                        var cur_val = replaceComma($(this).val());
                        cur_val = parseFloat(cur_val);
                        received_quantity = parseFloat(received_quantity) + cur_val;
                    }
                });
                if (picked_quantity > received_quantity) {
                    $("#receive_order_btn").show();
                } else {
                    $("#receive_order_btn").hide();
                }
                $(".order_product").each(function() {
                    $(this).removeAttr('data-val');
                    if ($(this).attr('sn_val') == 1) {
                        $(this).closest('tr').find('.transfer_order_qty').removeClass('numberWithSingleComma');
                        $(this).closest('tr').find('.transfer_order_qty').addClass('validateNumbers');
                        $(this).closest('tr').find('.transfer_order_qty').val(replaceCommaWithNoDecimal($(this).closest('tr').find('.transfer_order_qty').val()));
                    }
                });
                setTabIndex();
            },100);
        }
    } catch (Exception) {
        console.log("Unexpected error ");
    }
    setTimeout($.unblockUI, 1000);
}

$(document).on('focus', '.select2-container', function(e) {
     $(this).closest("select + *").prev().select2('open');
});

/**
 * checkPickedQuantity, check order qty greater or equal to picked quantity
 * @param  HTMLobject obj
 * @param  Integer actual_qty
 * @param  keyevent event
 * @return bbolean
 */
function checkPickedQuantity(obj, actual_qty, event) {
    /*var quantity = $(obj).val();
    var picked_quantity = 0;
    $(obj).parent().siblings('.picked_qty_td').find('.picked_quantity_input').each(function (e) {

        picked_quantity = picked_quantity + parseFloat($(this).val());
    });
    if (picked_quantity == 0) {
        return false;
    } else if (event.which != 8 && picked_quantity > quantity) {
        alert(order_quantity_less_validation);
        $(obj).val('');
        $(obj).val(actual_qty);
        return false;
    }*/
}


// Destination location options
function destinationLocationOptions(location_id) {
    destination_location_array = $("#destination_locations_array").val();
    var destination_location_options = "<option>Select</option>";
    if (destination_location_array) {
        destination_location_array = $.parseJSON(destination_location_array);
        var destination_location_size = Object.keys(destination_location_array).length;
        var destination_location_obj_index = 0;
        var order_status = $("#transfer_order_status").val();
        $.each(destination_location_array,function(index,value) {
            if (location_id == index) {
                destination_location_options += "<option selected='selected' id="+index+">"+value+"</option>";
            } else {
                if (destination_location_size == 1) {
                    destination_location_options += "<option selected='selected' id="+index+">"+value+"</option>";
                } else {

                    if (destination_location_obj_index == 0 && order_status != 1  && order_status != 7) {
                        destination_location_options += "<option selected='selected' id="+index+">"+value+"</option>";
                    } else {
                        destination_location_options += "<option id="+index+">"+value+"</option>";
                    }
                }
            }
            destination_location_obj_index++;
        });
    }
    return destination_location_options;
}

// Product options
function getProductAsOptions(product_id) {
    var product_options = "<option>Select</option>";
    product_array = $("#products_array").val();

    // product detail options
    if (product_array) {
        product_array = $.parseJSON(product_array);
        $.each(product_array,function(index,value) {
            if (product_id == index) {
                product_options += "<option id="+index+" selected='selected'>"+value+"</option>";
            } else {
                product_options += "<option id="+index+">"+value+"</option>";
            }
        });
    }
    return product_options;
}

// Location options
function getLocationAsOptions(location_id) {
    // location options
    var location_options = "<option>Select</option>";
    location_array = $("#locations_array").val();
    if (location_array) {
        location_array = $.parseJSON(location_array);
        location_obj_length = Object.keys(location_array).length;
        var location_obj_index = 0;
        var order_status = $("#transfer_order_status").val();
        $.each(location_array,function(index,value) {
            if (location_id == index) {
                location_options += "<option id="+index+" selected='selected'>"+value+"</option>";
            } else {
                if (location_obj_index == 0 && order_status != 1 && order_status != 7) {
                        location_options += "<option selected='selected' id="+index+">"+value+"</option>";
                } else {
                    location_options += "<option id="+index+">"+value+"</option>";
                }
            }
            location_obj_index++;
        });
    }
    return location_options;
}

// remove status
function removeStatus() {
    $("#order_status option").each(function() {
        if ( $(this).val() != '1' ) {
            $(this).remove();
        }
    });
    $("#order_status").trigger("change");
}

// redirect to warehouse detail page
function redirectToWarehouseDetail(url) {
    window.location.href = url;
}

// remove serial number option
function removeSerialNumberOption(obj, obj_class) {
    var obj_id = $(obj).attr("id");
    var obj_val = $("#"+obj_id+" option:selected").attr("id");
    var obj_text = $(obj).val();
    $("." + obj_class + " option").each(function () {
        if ( $(this).attr("id") == obj_val ) {
            $(this).remove();
        }
    });
    $("#"+obj_id).append("<option selected id="+obj_val+">"+obj_text+"</option>");
}

// construct received quantity DOM
function constructTransferPickedQuantityDOM(data, iteration, order_quantity, serial_number_length,sn_required_val) {
    var dom_string = "";
    var received_quantity = 0;
    var new_iteration = 1;
    var previuos_id_count = 0;
    var previous_rec_qty = 0;
    if (data) {
        for (var i = 0 ; i < data.length; i++) {
            new_iteration = new_iteration + i;
            previuos_id_count = i;
            received_quantity = received_quantity + parseFloat(data[i]['picked_quantity']);
            previous_rec_qty = previous_rec_qty + parseFloat(data[i]['picked_quantity']);
            if (typeof data[i]['received_quantity'] != "undefined" && data[i]['received_quantity'] != "") {
                dom_string += "<input style='width:100% !important;'  readonly='readonly' type=text id='picked_quantity_"+iteration+i+"' class='picked_quantity_input form-control'  value="+replaceDot(data[i]['picked_quantity'])+" onchange='generateSerialNumberDOMForTransferOrder(this,\"serial_number_td_"+iteration+"\", \"rec_location_td_"+iteration+"\", "+iteration+");'><br>";
                if (sn_required_val != 0 && serial_number_length != 0 && parseFloat(data[i]['picked_quantity']) > 1) {
                    dom_string += "";
                    for (var k = 1; k < parseFloat(data[i]['picked_quantity']); k++) {
                        dom_string += "<input type='text' value='test' class='form-control' style='visibility:hidden;' ><br/>";
                    }
                }
                dom_string += "<input readonly type=hidden id='picked_date_"+iteration+i+"' class='picked_date_input form-control' value="+data[i]['picked_date']+">";
            } else if (typeof data[i]['picked_quantity'] != "undefined" && data[i]['picked_quantity'] != "") {
                dom_string += "<input style='width:100% !important;'  readonly='readonly' id='picked_quantity_"+iteration+i+"' class='picked_quantity_input form-control'  type=text onchange='generateSerialNumberDOMForTransferOrder(this,\"serial_number_td_"+iteration+"\", \"rec_location_td_"+iteration+"\", "+iteration+","+previous_rec_qty+");'  value='"+replaceDot(data[i]['picked_quantity'])+"'><br>";
                if (sn_required_val != 0 && serial_number_length != 0 && parseFloat(data[i]['picked_quantity']) > 1) {
                    dom_string += "";
                    for (var k = 1; k < parseFloat(data[i]['picked_quantity']); k++) {
                        dom_string += "<input type='text' value='test' class='form-control' style='visibility:hidden;' ><br/>";
                    }
                }
                dom_string += "<input readonly type=hidden id='picked_date_"+iteration+i+"' class='picked_date_input' value="+data[i]['picked_date']+">";
            }
        }
        if (received_quantity > 0 && received_quantity < order_quantity) {
            previuos_id_count = ++previuos_id_count;
            var class_name = 'numberWithSingleComma';
            if (sn_required_val == 1) {
                class_name = 'numberWithSingleComma'
            } 
            dom_string += "<input style='width:100% !important;' id='picked_quantity_"+iteration+previuos_id_count+"'  class='makeedit picked_quantity_input "+class_name+" form-control'  type=text onchange='generateSerialNumberDOMForTransferOrder(this,\"serial_number_td_"+iteration+"\", \"rec_location_td_"+iteration+"\", "+iteration+","+received_quantity+");' value=''><br>";
        }
        if (order_quantity  == received_quantity) {
            $("#qty_"+iteration).attr("readonly", true);
        } else {
            setTimeout(function () {
                $("#qty_"+iteration).removeAttr("readonly");
            }, 3000);
        }
    }
    return dom_string;
}

// construct received quantity DOM
function constructTransferReceivedQuantityDOM(data, iteration, order_quantity,serial_number_length, sn_required_val) {
    var dom_string = "";
    var received_quantity = 0;
    var new_iteration = 1;
    var previuos_id_count = 0;
    var picked_quantity = 0;
    if (data) {
        for (var i = 0 ; i < data.length; i++) {
            new_iteration = new_iteration + i;
            previuos_id_count = i;
            received_quantity = received_quantity + parseFloat(data[i]['picked_quantity']);
            picked_quantity = picked_quantity + parseFloat(data[i]['picked_quantity']);
            data[i]['received_quantity'] ? data[i]['received_quantity'] : "";
            if (data[i]['received_quantity'] ) {
                dom_string += "<input style='width:100% !important;'  readonly='readonly' type=text id='received_quantity_"+iteration+i+"' class='received_quantity_input form-control' value="+replaceDot(data[i]['received_quantity'])+"><br>";
                if (sn_required_val != 0 && serial_number_length != 0 && parseFloat(data[i]['picked_quantity']) > 1){
                    dom_string += "";
                    for (var k = 1; k < parseFloat(data[i]['picked_quantity']); k++) {
                        dom_string += "<input type='text' value='test' class='form-control' style='visibility:hidden;' ><br/>";
                    }
                }
            } else {
                dom_string += "<input style='display:none;width:100% !important;' readonly='readonly' type=text id='received_quantity_"+iteration+i+"' class='received_quantity_input form-control' value="+replaceDot(data[i]['received_quantity'])+"><br>";
                if (sn_required_val != 0 && serial_number_length != 0 && parseFloat(data[i]['picked_quantity']) > 1){
                    dom_string += "";
                    for (var k = 1; k < parseFloat(data[i]['picked_quantity']); k++) {
                        dom_string += "<input type='text' value='test' class='form-control' style='visibility:hidden;' ><br/>";
                    }
                }
            }
            dom_string += "<input readonly type=hidden id='received_date_"+iteration+i+"' class='received_date_input' value="+data[i]['received_date']+">";
        }
        if (received_quantity > 0 && received_quantity < order_quantity) {
            previuos_id_count = ++previuos_id_count;
            dom_string += "<input style='display:none;width:100% !important;' readonly='readonly' class='form-control' type=text id='received_quantity_"+iteration+previuos_id_count+"' value=''><br>";
        }
        //Added by david
        setTimeout(function () {
            //$("#qty_"+iteration).removeAttr("readonly");
            $("#qty_"+iteration).attr('min', received_quantity);
            $("#qty_"+iteration).on('focusin', function(){
                $(this).data('val', $(this).val());
            });
            $("#qty_"+iteration).change(function() {
                var min = parseFloat($("#qty_"+iteration).attr('min'));
                if (replaceComma($(this).val()) < min) {
                    alert(order_quantity_should_not_less_than_received_quantity);
                    $(this).val($(this).data("val"));
                } else if (replaceComma($(this).val()) == min) {
                    $(this).closest('tr').find('.picked_qty_td input:last').val('');
                    $(this).closest('tr').find('.picked_qty_td input:last').hide();
                    $(this).val(min);
                } else {
                    $(this).closest('tr').find('.picked_qty_td input:last').show();
                }
            });
        }, 200);
    }
    return dom_string;
}

// change event for product
function trasferOrderproductChange (obj) {
    var dom_id = $(obj).attr("id");
    const self = $(obj);
    var delete_qty_val = $(obj).attr("data-val"); 
    if (delete_qty_val == 1 || delete_qty_val == "1") {
        $(obj).removeAttr("data-val")
    } else {
        $(obj).closest('tr').find('.transfer_order_qty').val('');
    }
    var selected_product = $("#"+dom_id+" option:selected").attr("id");
    var warehouse_product_url = url+"/product/getProductDetailFromId";
    var splitted_id = dom_id.split("_");
    var order_type = $("#order_type").val();
    var warehouse_id = $(".transfer_source_warehouse").val();
    var selected_location = $("#product_location_"+splitted_id[2]+" option:selected").attr("id");
    var order_status = $("#transfer_order_status").val();
    $.ajax({
        type : "POST",
        url : warehouse_product_url,
        async : false,
        data : {
            '_token': token,
            'rest' : 'true',
            'product_id' : selected_product,
            'order_type' : order_type,
            'warehouse_id' : warehouse_id
        },
        success: function (response) {
            if (response) {
                try {
                    decoded_response = $.parseJSON(response);
                    if (decoded_response['status'] == SUCCESS) {
                        product_details = decoded_response['data'];
                        if (order_type == 1) {
                            $("#product_location_"+splitted_id[2]).html("");
                            $("#product_serail_number_array").html();
                            // set serial number required
                           if (product_details['sn_required'] == 1) {
                                self.closest('tr').find('.transfer_order_qty').addClass('validateNumbers');
                                self.closest('tr').find('.transfer_order_qty').removeClass('numberWithSingleComma');
                                $("#sn_required_" + splitted_id[2]).val("1");
                            } else {
                                self.closest('tr').find('.transfer_order_qty').addClass('numberWithSingleComma');
                                self.closest('tr').find('.transfer_order_qty').removeClass('validateNumbers');
                                $("#sn_required_" + splitted_id[2]).val("0");
                            }
                            var order_product_details = $("#product_details").val();
                            var current_iteration = splitted_id[2];
                            if (product_details['serial_numbers'] && product_details['serial_numbers'].length > 0) {
                                var transfer_location_options = "<option>Select</option>"
                                var enable_location_from_local = true;
                                var transfer_location_options_index = 0;
                                $.each(product_details['serial_numbers'],function(index,value) {
                                    if (value['ID'] == selected_location) {
                                        enable_location_from_local = false;
                                        transfer_location_options += "<option selected id="+value['ID']+">"+value['NAME']+"</option>";
                                    } else {
                                        if (value['ID']) {
                                            if (transfer_location_options_index == 0 && order_status != 1 && order_status != 7) {
                                                transfer_location_options += "<option selected='selected' id="+value['ID']+">"+value['NAME']+"</option>";
                                            } else {

                                                transfer_location_options += "<option id="+value['ID']+">"+value['NAME']+"</option>";
                                            }
                                        } else if (transfer_location_options_index == 0) {
                                            //transfer_location_options += "<option selected='selected' id="+value['ID']+">"+value['NAME']+"</option>";
                                        }
                                    }
                                    transfer_location_options_index++;
                                });

                                if (order_product_details && enable_location_from_local) {
                                    order_product_details = $.parseJSON(order_product_details);
                                    location_id = order_product_details[current_iteration]['location_id'];
                                    location_text = order_product_details[current_iteration]['location_text'];
                                    if (location_text && location_text != "Select" && location_text != "Velg") {
                                        transfer_location_options += "<option selected id="+location_id+">"+location_text+"</option>";
                                    }
                                }
                                $("#product_location_"+splitted_id[2]).html(transfer_location_options);
                                //$("#product_location_"+splitted_id[2]).select2();
                            } else {
                                if (order_product_details) { // if picked quantity is avaialble
                                    var location_options = "<option>Select</option>"
                                    order_product_details = $.parseJSON(order_product_details);
                                    location_id = order_product_details[current_iteration]['location_id'];
                                    location_text = order_product_details[current_iteration]['location_text'];
                                    if (location_text && location_text != 'undefined') {
                                        if (location_id)
                                        location_options += "<option selected id="+location_id+">"+location_text+"</option>";
                                    }
                                    $("#product_location_"+splitted_id[2]).html(location_options);
                                    //$("#product_location_"+splitted_id[2]).select2();
                                    $("#sn_required_"+splitted_id[2]).val(order_product_details[current_iteration]['sn_required']);
                                }

                            }

                        }
                    } else if (decoded_response['status'] == ERROR) {
                        $("#product_location_"+splitted_id[2]).html("");
                    }
                } catch (Exception) {}
            }
        },
        fail: function() {
            alert("Something went wrong");
        }
    });
}

function showRecAndPicQtytr(obj) {
    $("#warehouse_product_order_table thead tr").find(".picked_qty_td").show();
    $("#"+obj).parent('td').closest('tr').find(".serial_number_td, .picked_qty_td").show();
    //$("#receive_order_btn").show();
}

// location change event
function transferOrderLocationChange (obj) {
    var obj_val = $(obj).val();
    if (obj_val == "Select" || obj_val == "Velg") {
        $(obj).parent('td').closest('tr').find(".serial_number_td, .picked_qty_td").hide();
    } else {
        showRecAndPicQtytr($(obj).attr("id"));
    }
}

// compareRecqtyAndOrderQty
function compareRecqtyAndOrderQtyForTransferOrder (obj, iteration, previous_rec_qty) {
    var received_qty = $(obj).val();
    var actual_qty = $("#qty_"+iteration).val();
    var total_qty = '';
    total_qty = received_qty;
    if (previous_rec_qty) {
        total_qty = parseFloat(received_qty) + parseFloat(previous_rec_qty);
    }
}

// generate serial number DOM's
function generateSerialNumberDOMForTransferOrder (obj, serial_number_td, rec_location_td, iteration, previous_rec_qty) {
    var product_actual_quantity = '';//Added by David
    if ($(obj).attr('readonly')) {
        return false;
    }
    displayBlockUI();
    $("#receive_order_btn").hide();
    var received_qty = replaceComma($(obj).val());
    if (received_qty <= 0) {
        $(obj).val("");
        $(obj).focus();
        alert(qty_alert)
        setTimeout($.unblockUI, 500);
        return false;
    }
    if (received_qty && $("#product_location_"+iteration+" option:selected").val() == "" || received_qty && $("#product_location_"+iteration).val() == "Select" || received_qty && $("#product_location_"+iteration).val() == "Velg") {
        alert(please_select_location);
        $(obj).val("");
        $("#product_location_"+iteration).focus();
        setTimeout($.unblockUI, 1000);
        return false;
    }
    window.localStorage.setItem("serial_number_id", "");
    var received_location_input = "";
    var actual_qty = replaceComma($("#qty_"+iteration).val());
    var total_qty = parseFloat(received_qty);
    if (previous_rec_qty) {
        total_qty = parseFloat(received_qty) + parseFloat(previous_rec_qty);
    }
    if (parseFloat(total_qty) > actual_qty) {
        alert(picked_qty_not_greater_than_order_qty);
        $(obj).val("");
        $(obj).focus();
        setTimeout($.unblockUI, 1000);
        return false;
    }
    var selected_location = $("#product_location_"+iteration+" option:selected").attr("id");
    var product_warehouse_id = $('#transfer_source_warehouse').val();
    //Checking the actual quantity // Addedb by david
    $.ajax({
        type : "POST",
        url : url+"/warehouseinventory/getProductActualQuantity",
        async : false,
        data : {
            '_token': token,
            'rest' : 'true',
            'product_id' : $("#order_product_"+iteration+ " option:selected").attr('id'),
            'location_id' : selected_location,
            'warehouse_id' : product_warehouse_id,
        },
        success: function(response) {
            if (response) {
                decoded_response = $.parseJSON(response);
                if (decoded_response['status'] == SUCCESS) {
                    product_actual_quantity = decoded_response['data'];
                } else {
                    $(obj).val("");
                    alert(decoded_response['message']);
                }
            }
        }, fail : function () {
        }
    });

    // Added by david
    //checking the quatity with db data
    if (parseFloat($(obj).val()) > parseFloat(product_actual_quantity)) {
        alert(picked_quantity_must_lesser_than_stock);
        $(obj).val("");
        $(obj).focus();
        setTimeout($.unblockUI, 1000);
        return false;
    }
    if ($("#sn_required_"+iteration).val() == "1") {
        $.ajax({
            type : "POST",
            url : url+"/warehouseinventory/getSerialNumbers",
            data : {
                '_token': token,
                'rest' : 'true',
                'product_id' : $("#order_product_"+iteration+ " option:selected").attr('id'),
                'location_id' : selected_location,
                'picked_quantity' : received_qty
            },
            success: function(response) {
                var existing_serial_number_array = [];
                var existing_rec_location_array = [];
                if (previous_rec_qty) {
                    for (var k = 0; k < previous_rec_qty; k++) {
                        existing_serial_number_array[k] = $("#hidden_serial_number_"+iteration+k).val();
                        existing_rec_location_array[k] = $("#hidden_rec_product_location_"+iteration+k).val();
                    }
                }
                var decoded_data = "";
                if (response) {
                    decoded_response = $.parseJSON(response);
                    if (decoded_response['status'] == SUCCESS) {
                        $(".serial_number_td").show();
                        decoded_data = decoded_response['data'];
                    } else if (decoded_response['status'] == ERROR) {
                        alert(picked_quantity_must_lesser_than_stock);
                        $(obj).val("");
                        setTimeout($.unblockUI, 1000);
                        return false;
                    }
                }
                // remove the dynamic generated DOM
                if (previous_rec_qty) {
                    var actual_qty = replaceComma($("#qty_"+iteration).val());
                    for (var j = previous_rec_qty; j <= actual_qty; j++) {
                        $("#serial_number_"+iteration+j).select2('destroy');
                        $(".sn_br"+iteration+j).next('br').remove();
                        $("#serial_number_"+iteration+j).remove();
                        $("#rec_product_location_"+iteration+j).remove();
                        $(".sn_br"+iteration+j).remove();
                        $(".rl_br"+iteration+j).remove();
                    }
                } else {
                    $("#"+serial_number_td).html("");
                    $("#"+rec_location_td).html("");
                }
                var received_qty = $(obj).val();
                var actual_qty = replaceComma($("#qty_"+iteration).val());
                var total_qty = '';
                total_qty = received_qty;
                if (received_qty) {
                    var serial_number_input = "";
                    var picked_quantity_break = "";
                    var received_location_input = "";
                    var k = previous_rec_qty ? previous_rec_qty : 0 ;
                    for (var i = 0; i < received_qty; i++) {
                        if (decoded_data) {
                            var serial_numbers_array = "<option>Select</option>";
                            $.each(decoded_data, function(index,value) {
                                if (previous_rec_qty) {
                                    if ($.inArray(index, existing_serial_number_array) !== -1) {
                                        var array_index = existing_serial_number_array.indexOf(index);
                                        existing_serial_number_array.splice(array_index, 1);
                                        serial_numbers_array += "<option selected id="+index+">"+value+"</option>";
                                    } else {
                                        serial_numbers_array += "<option id="+index+">"+value+"</option>";
                                    }
                                } else {
                                    serial_numbers_array += "<option id="+index+">"+value+"</option>";
                                }
                            });
                        }
                        var destination_locations = "<option>Select</option>";
                        var destination_transfer_location_array = $("#destination_locations_array").val();
                        if (destination_transfer_location_array) {
                            destination_transfer_location_array = $.parseJSON(destination_transfer_location_array);
                            location_obj_length = Object.keys(destination_transfer_location_array).length;
                            var destination_transfer_location_index = 0;
                            $.each(destination_transfer_location_array,function(index,value) {
                                if ($.inArray(index, existing_rec_location_array) !== -1) {
                                    var array_index = existing_rec_location_array.indexOf(index);
                                    existing_rec_location_array.splice(array_index, 1);
                                    destination_locations += "<option selected id="+index+">"+value+"</option>";
                                } else {
                                    if (destination_transfer_location_index == 0) {
                                        destination_locations += "<option selected='selected' id="+index+">"+value+"</option>";
                                    } else {
                                        destination_locations += "<option id="+index+">"+value+"</option>";
                                    }
                                }
                                destination_transfer_location_index++;
                            });
                        }
                        var obj_id = $(obj).attr("id");
                        var splitted_id = obj_id.split("_");
                        if ($("#received_quantity_"+splitted_id[2]).length > 0 && $("#received_quantity_"+splitted_id[2]).val() == "" ) {
                            picked_quantity_break += "<br><br>" ;
                            serial_number_input += "<div><select style='width:100%;' class='select2 serial_number"+iteration+"' id=serial_number_"+iteration+k+ " onchange='removeSerialNumberOption(this,\"serial_number"+iteration+"\");' >"+serial_numbers_array+"</select><br class='sn_br"+iteration+k+"'></div>";
                            serial_number_input += "<br class='sn_br"+iteration+k+"'>";
                            received_location_input += "<select style='width:100%;' disabled='disabled' class='form-control rec_product_location' id=rec_product_location_"+iteration+k+ "  >"+destination_locations+"</select><br class='rl_br"+iteration+k+"'>";
                        } else {
                            var product_details = $("#product_details").val();
                            if (product_details) { // if picked quantity is avaialble
                                product_details = $.parseJSON(product_details);
                                if (product_details[iteration]['serial_number_array'].length > 0) { // if order is picked(that is picked quantity is availbale)
                                    var serial_number_dom = "";
                                    for (var s = 0; s < product_details[iteration]['serial_number_array'].length; s++) {
                                        var serial_number_id_from_ls = window.localStorage.getItem("serial_number_id");
                                        serial_number_id = product_details[iteration]['serial_number_array'][s]['serial_number_id'] ? product_details[iteration]['serial_number_array'][s]['serial_number_id'] : "";
                                        if (product_details[iteration]['serial_number_array'][s]['newly_picked'] && serial_number_id_from_ls != serial_number_id) {
                                            serial_number_value = product_details[iteration]['serial_number_array'][s]['serial_number'] ? product_details[iteration]['serial_number_array'][s]['serial_number'] : "";
                                            serial_number_id = product_details[iteration]['serial_number_array'][s]['serial_number_id'] ? product_details[iteration]['serial_number_array'][s]['serial_number_id'] : "";
                                            serial_number_dom = "<option selected='selected' id='"+serial_number_id+"'>"+serial_number_value+"</option>";
                                            window.localStorage.setItem("serial_number_id", serial_number_id);
                                            break;
                                        }
                                    }
                                    serial_number_input += "<div><select style='width:100%;' disabled='disabled' class='select2 serial_number"+iteration+"' id=serial_number_"+iteration+k+ " onchange='removeSerialNumberOption(this,\"serial_number"+iteration+"\");' >"+serial_number_dom+"</select><br class='sn_br"+iteration+k+"'></div>";
                                    received_location_input += "<select style='width:100%;' class='form-control rec_product_location' id=rec_product_location_"+iteration+k+ "  >"+destination_locations+"</select2><br class='rl_br"+iteration+k+"'>";
                                } else {
                                    serial_number_input += "<div><select style='width:100%;' disabled='disabled' class='select2 serial_number"+iteration+"' id=serial_number_"+iteration+k+ " onchange='removeSerialNumberOption(this,\"serial_number"+iteration+"\");' >"+serial_numbers_array+"</select><br class='sn_br"+iteration+k+"'></div>";
                                    received_location_input += "<select style='width:100%;' class='form-control rec_product_location' id=rec_product_location_"+iteration+k+ "  >"+destination_locations+"</select><br class='rl_br"+iteration+k+"'>";
                                }
                            }
                        }
                        k++;
                    }
                }
                $("#"+serial_number_td).append(serial_number_input);
                //$("#"+rec_location_td).append(received_location_input);
                setTimeout(function () {
                    $(".select2").select2();
                },100);
                setTimeout($.unblockUI, 1000);
            }, fail : function () {
                setTimeout($.unblockUI, 1000);
            }
        });
    } else {
        var non_sn_location_index = 0;
        if (previous_rec_qty) {
            $("#"+rec_location_td).find( ".rec_product_location, .disable_location" ).each(function( index ) {
              if ($(this).attr('disabled') &&  $(this).val() != "Select"  &&  $(this).val() != "Velg") {
                    non_sn_location_index = index + 1;
              } else {
                $(".n_br"+iteration+index).remove();
                $(".rl_br"+iteration+index).remove();
              }
            });

        } else {
            $("#"+serial_number_td).html("");
            $("#"+rec_location_td).html("");
        }
        var destination_location_options = destinationLocationOptions("");
        var obj_id = $(obj).attr("id");
        var splitted_id = obj_id.split("_");
        if ($("#received_quantity_"+splitted_id[2]).length > 0 && $("#received_quantity_"+splitted_id[2]).val() == "" ) {
            //received_location_input += "<select disabled style='width: 50%' class='select2 rec_product_location' id=rec_product_location_"+iteration+non_sn_location_index+ "  >"+destination_location_options+"</select><br id='rl_br"+iteration+non_sn_location_index+"'>";
        } else {
            received_location_input += "<select style='width:100%;' class='form-control rec_product_location' id=rec_product_location_"+iteration+non_sn_location_index+ "  >"+destination_location_options+"</select><br class='rl_br"+iteration+non_sn_location_index+"'>";
        }
        $("#"+rec_location_td).append(received_location_input);
        setTimeout(function () {
            $(".select2").select2();
            setTimeout($.unblockUI, 1000);
        },100);
    }
}

// receive button click event
$(document).on("click", "#receive_order_btn", function() {
    $(".picked_quantity_input").each(function() {
        var obj_id = $(this).attr("id");
        var split_obj_id = obj_id.split("_");
        var iteration_id = split_obj_id[2];
        var obj_val = $(this).val();
        if (obj_val) {
            $("#received_quantity_"+iteration_id).show();
            $("#received_quantity_"+iteration_id).closest("td").show();
            $(".rec_qty_td").show();
            $(".rec_location_td").show();
            $(".rec_product_location").show();
            $(".rec_product_location").removeAttr('readonly');
            //$(".rec_product_location").addClass("select2");
            $(".select2").select2();

        } else {
            $("#received_quantity_"+iteration_id).hide();
        }
        if ($("#received_quantity_"+iteration_id).val() == "" || $("#received_quantity_"+iteration_id).val() == "0,00") {
            $("#received_quantity_"+iteration_id).val(obj_val);
            //$(this).trigger("change");
        }
        $("#receive_order_btn").attr("disabled", true);
    });
});

// tansfer order form submit
$(document).on("click", ".transferorder_submit_btn", function() {
    var submit_btn_value = $(this).val();
    $('#submit_button_value').val(submit_btn_value)
    if (submit_btn_value) {
        $("#update").val("1");
    }
    displayBlockUI();
    $("#product_details").val("");
    window.localStorage.setItem("stop_form_submit","");
    var order_transfer_product_table_row_count = $("#hidden_transfer_table_row_count").val();
    var product_data = [];
    var order_status = $("#transfer_order_status").val();
    if (order_status >= 2) {
        if ($("#destination_warehouse option:selected").val() == "") {
            alert(destination_warehosue_is_required);
            setTimeout($.unblockUI, 1000);
            return false;
        }
        if ($("#source_warehouse option:selected").val() == "") {
            alert(source_warehosue_is_required);
            setTimeout($.unblockUI, 1000);
            return false;
        }
        if (order_transfer_product_table_row_count < 1) {
            alert(please_select_atleast_one_product);
            setTimeout($.unblockUI, 1000);
            return false;
        }
    }
    for (var i = 0 ; i <=  order_transfer_product_table_row_count; i++) {
        if ($("#product_tr_"+i).length && $("#order_product_"+i).val() != "") {
            var serial_number_products = [];
            var rec_qty = "";
            var picked_qty = "";
            var new_rec_qty = "";
            var new_picked_qty = "";
            var order_details_array = [];
            var serial_numbers_array = [];
            var quantity_count = replaceComma($("#qty_"+i).val());
            window.localStorage.setItem("transfer_received_quantity", 0);
            window.localStorage.setItem("transfer_previous_iteration", "");
            window.localStorage.setItem("serial_received_quantity", 0);
            window.localStorage.setItem('serial_previous_iteration', "");
            for (var j = 0; j > -1; j++ ) {
                var serial_number_location_array = [];
                if ($("#picked_quantity_"+i+j).length > 0 && $("#picked_quantity_"+i+j).val()) {
                    if (order_status >= 3) {
                        // construct serial number array
                        serial_number_location_array = constructTransferSerailNumberArray($("#received_quantity_"+i+j).val(), i);
                    }
                    var received_date = (($("#received_quantity_"+i+j).val()) ? ($("#received_date_"+i+j).val() ? $("#received_date_"+i+j).val() : getCurrentDate('.')) : "");
                    order_details_array.push({
                        "picked_quantity": $("#picked_quantity_"+i+j).val() ? replaceComma($("#picked_quantity_"+i+j).val()) : '',
                        "picked_date": $("#picked_date_"+i+j).val() ? $("#picked_date_"+i+j).val() : getCurrentDate('.'),
                        "received_quantity": $("#received_quantity_"+i+j).val() ? replaceComma($("#received_quantity_"+i+j).val()) : '',
                        "received_date": received_date,
                        "serial_number_products" : serial_number_location_array,
                        "newly_received": $("#received_date_"+i+j).val() ? false : true,
                        "newly_picked": $("#picked_date_"+i+j).val() ? false : true,
                    });
                } else {
                    break;
                }
            }
            // construct serail number array(If item is picked)
            if (quantity_count > 0) {
                if (order_status >= 3) {
                    serial_numbers_array = getSerialNumbers(i, quantity_count);
                }
            }

            if ($("#order_product_"+i+ " option:selected").attr("id") && $("#order_product_"+i).val()) {
                product_data.push({
                    "product_id" : $("#order_product_"+i+ " option:selected").attr("id"),
                    "whs_product_id" : $("#whs_product_id_"+i).val(),
                    "product_text" : $("#order_product_"+i).val(),
                    "location_id" : $("#product_location_"+i+ " option:selected").attr("id"),
                    "location_text" : $("#product_location_"+i).val(),
                    "ordered_date" : $("#ordered_date_"+i).val() ? $("#ordered_date_"+i).val() : getCurrentDate('.'),
                    "qty" : replaceComma($("#qty_"+i).val()),
                    "comment" : $("#comment_"+i).val(),
                    "sn_required" : $("#sn_required_"+i).val(),
                    "order_details" : order_details_array,
                    "serial_number_array" : serial_numbers_array,
                    "destination_warehouse_id" : $("#destination_warehouse").val(),
                    "source_warehouse" : $("#source_warehouse").val(),
                });
            }
        }
    }
    if (product_data) {
        $("#product_details").val(JSON.stringify(product_data));
    }
    var form_submit = window.localStorage.getItem("stop_form_submit");
    if (form_submit == "") {
        $(".warehosue_header_fields").attr('disabled', false);
        $("#warehousetransferorderform").submit();
    }
    setTimeout($.unblockUI, 1000);
});

// construct product serial number array
function constructTransferSerailNumberArray(received_qty, iteration) {
    var serial_numbers_array = [];
    var new_iteration = 0;
    received_qty = replaceComma(received_qty);
    var previous_iteration = window.localStorage.getItem("transfer_previous_iteration");
    if (previous_iteration && previous_iteration == iteration) {
        var new_iteration = window.localStorage.getItem("transfer_received_quantity");
        new_iteration = parseFloat(new_iteration)+1;
    } else {
        new_iteration = 0;
    }
    if ($("#sn_required_"+iteration).val() == "1" && received_qty > 0) {
        for (var j = 0; j < received_qty; j++ ) {
            if (typeof $("#rec_product_location_"+iteration+new_iteration+" option:selected").attr("id") == "undefined" || $("#rec_product_location_"+iteration+new_iteration+ " option:selected").attr("id") == "" || typeof $("#serial_number_"+iteration+new_iteration+"").attr("id") == "undefined" || $("#serial_number_"+iteration+new_iteration+ "").val() == "") {
                window.localStorage.setItem("stop_form_submit","1");
                alert(serial_number_and_received_location_are_needed);
                setTimeout($.unblockUI, 1000);
                break;
                return false;
            }
            if ($("#serial_number_"+iteration+new_iteration+ "").val() == "") {
                window.localStorage.setItem("stop_form_submit","1");
                alert(please_select_serial_number);
                setTimeout($.unblockUI, 1000);
                break;
                return false;
            }
            serial_numbers_array.push({
                'rec_location_id' : $("#rec_product_location_"+iteration+new_iteration+ " option:selected").attr("id"),
                'rec_location_text' : $("#rec_product_location_"+iteration+new_iteration).val(),
                'serial_number' : $("#serial_number_"+iteration+new_iteration).val(),
                'serial_number_id' : $("#serial_number_"+iteration+new_iteration+ " option:selected").attr("id"),
            });
            window.localStorage.setItem("transfer_received_quantity", new_iteration);
            new_iteration = ++new_iteration;
            window.localStorage.setItem('transfer_previous_iteration', iteration);
        }
    } else {
        if (received_qty > 0) {
            if (typeof $("#rec_product_location_"+iteration+new_iteration+" option:selected").attr("id") == "undefined" || $("#rec_product_location_"+iteration+new_iteration+" option:selected").attr("id") == "") {
                setTimeout($.unblockUI, 1000);
                return false;
            }

            if ($("#rec_product_location_"+iteration+new_iteration+ "").val() == "") {
                window.localStorage.setItem("stop_form_submit","1");
                alert(product_location_validation);
                setTimeout($.unblockUI, 1000);
                return false;
            }

            serial_numbers_array.push({
                'rec_location_id' : $("#rec_product_location_"+iteration+new_iteration+" option:selected").attr("id"),
                'rec_location_text' : $("#rec_product_location_"+iteration+new_iteration).val(),
                'serial_number' : "",
                'serial_number_id' : "",
            });
            window.localStorage.setItem("transfer_received_quantity", new_iteration);
            new_iteration = ++new_iteration;
            window.localStorage.setItem('transfer_previous_iteration', iteration);
        }
    }
    return serial_numbers_array;
}

// get serial numbers
function getSerialNumbers(iteration, quantity_count) {
    var serial_numbers_array = [];
    var new_iteration = 0;
    for (var i = 0 ; i < quantity_count ; i++) {
        var previous_iteration = window.localStorage.getItem("serial_previous_iteration");
        if (previous_iteration && previous_iteration == iteration) {
            var new_iteration = window.localStorage.getItem("serial_received_quantity");
            new_iteration = parseFloat(new_iteration)+1;
        } else {
            new_iteration = 0;
        }
        if ($("#picked_quantity_"+iteration+i).length > 0 && $("#picked_quantity_"+iteration+i).val()) {
            received_qty =  replaceComma($("#picked_quantity_"+iteration+i).val());
            if ($("#sn_required_"+iteration).val() == "1") {
                for (var j = 0; j < received_qty; j++ ) {
                    //new_iteration = new_iteration + j;
                    if ($("#serial_number_"+iteration+new_iteration+ "").val() == "" || $("#serial_number_"+iteration+new_iteration+ "").val() == "Select") {
                        window.localStorage.setItem("stop_form_submit","1");
                        alert(please_select_serial_number);
                        break;
                        return false;
                    }
                    serial_numbers_array.push({
                        'serial_number' : $("#serial_number_"+iteration+new_iteration).val(),
                        'serial_number_id' : $("#serial_number_"+iteration+new_iteration+ " option:selected").attr("id"),
                        "newly_picked": $("#picked_date_"+iteration+i).val() ? false : true,
                    });
                    window.localStorage.setItem("serial_received_quantity", new_iteration);
                    new_iteration = ++new_iteration;
                    window.localStorage.setItem('serial_previous_iteration', iteration);
                }
            } else {
                if (received_qty) {
                    serial_numbers_array.push({
                        "newly_picked": $("#picked_date_"+iteration+i).val() ? false : true,
                        "picked_quantity": replaceComma($("#picked_quantity_"+iteration+i).val()),
                    });
                    window.localStorage.setItem("serial_received_quantity", new_iteration);
                    new_iteration = ++new_iteration;
                    window.localStorage.setItem('serial_previous_iteration', iteration);
                }
            }
        }
    }
    return serial_numbers_array;
}
// delete order_product
function deleteTransferProductRow(obj, hourlogg_ids) {
    var confirm_delete_msg = confirm(confirm_delete);
    if (confirm_delete_msg) {
        $(obj).closest('tr').remove();
    }
}

/* warehouse order script logic end */