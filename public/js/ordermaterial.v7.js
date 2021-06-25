hideOrShowPack();
$(document).ready(function() {
    $('.locationTrigger').each(function() {
        var choosenWhs = $(this).find('.warehouse').val();
        if (choosenWhs == '' || choosenWhs == null || choosenWhs == 'undefined' || choosenWhs == 'Select' || choosenWhs == 'velg') {
            productOnchnage($(this).find('.product_number'))
        }
        if ($(this).find('.location_td .location').val() == null) {
            getProductDetailsForMaterials($(this).find('.warehouse_td .warehouse'), 2);
        }
    });
});
$('.product_package').on('click', function(e) {
    var selected_package = $(this).attr('value');
    getPackageProducts(selected_package);
});
$("#return_material").click(function(event) {
    $(".subPanelModelLG").modal("show");
    $('#subPanelContentLG').load($(this).attr('data-href'), function() {});
});
$('.delivery_date').datetimepicker({
    format: 'DD.MM.YYYY',
    locale: 'en-gb'
}).on("dp.change", function(e) {
    setContentDate($(this));
});
$(document).on("click", ".picklist", function() {
    $('.pickListModal').modal('show');
});
$(document).on("click", ".packlist", function() {
    // if ($(this).attr('shipment') == 0) {
    //     var confirm_msg = confirm(continue_without_shipment_message);
    //     if (!confirm_msg) {
    //         return false;
    //     }
    // }
    $(this).hide();
    displayBlockUI();
    $('#packlist-form').submit();
});
if (filetodownload) {
    window.open(url + '/downloadFile');
}
$("tbody").sortable({
    items: "tr:not(.child_products)",
    handle: '> .product_move',
    stop: function(event, ui) {
        var i = 1;
        var arr = []
        $('#order_material_Table tbody tr').each(function() {
            if ($(this).closest('tr').attr('material_id')) {
                arr.push({
                    sortVal: i,
                    id: $(this).closest('tr').attr('material_id')
                });
            }
            i++;
        });
        displayBlockUI();
        $.ajax({
            type: "POST",
            url: url + "/UpdateSort",
            data: {
                '_token': token,
                'rest': 'true',
                'data': JSON.stringify(arr),
            },
            success: function(response) {
                decoded_response = $.parseJSON(response);
                $.unblockUI();
            },
            fail: function() {
                $.unblockUI();
            }
        });
    },
    start: function(event, ui) {
        let getChildren = ui.item.siblings(`tr[data-val="${$(ui.item).attr('data-val')}"]`);
        ui.item.push(...getChildren);
        return ui;
    },
});
$(document).on("click", "#save_all_materials", function() {
    setOrder().then((result) => {
        saveOrUpdate().then((result) => {
            showAlertMessage(material_save_msg, 'success');
            hideOrShowPack();
        });
    })
});

function saveOrUpdate() {
    return new Promise((resolve, reject) => {
        $('.updateSortOrder').each(function() {
            $(this).trigger('click');
        });
        $('.save_text').each(function() {
            $(this).trigger('click');
        });
        $('.update_product').each(function() {
            if ($(this).attr('save_val') == 1 && $(this).attr('is_content') != 1) {
                $(this).trigger('click');
                $('#order_material_Table').find('tr').removeClass('bg-color-grey');
            }
        });
        $('.save_product').each(function() {
            if ($(this).attr('save_val') == 1 && $(this).attr('save_id') != 1) {
                $(this).trigger('click');
                $('#order_material_Table').find('tr').removeClass('bg-color-grey');
            }
        });
        resolve();
    });
}

function setOrder() {
    return new Promise((resolve, reject) => {
        var i = 1;
        $('#order_material_Table tbody tr').each(function() {
            $(this).attr('sortorderval', i)
            i++;
        });
        resolve()
    });
}
// approve product checkbox
$("#approve_all_products").click(function() {
    $('.approve_product:visible').prop('checked', this.checked);
    $('.approve_product:visible').trigger('change');
});
// create new row for add product
$(".add_product_material").click(function() {
    var warehouses = $("#hidden_warehouses").html();
    var warehouses = $.parseJSON(warehouses);
    var warehouse_options = "<option value='Select'>" + js_select_text + "</option>";;
    var usertype = $("#hidden_usertype").val().trim();
    var user_warehouse_resposible = $("#hidden_user_warehouse_resposible").val();
    var user_warehouse_resposible_id = $("#hidden_user_warehouse_resposible_id").val();
    var location_options = "<option value='Select'>" + js_select_text + "</option>";
    // var products = $("#hidden_products").val();
    // var products = $.parseJSON(products);
    var product_options = "<option value='Select'>" + js_select_text + "</option>";;
    // $.each(products, function(index, value) {
    //     product_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
    // });
    var user_id = $("#logged_user_id").val();
    var user_name = $("#logged_user_name").val();
    var htmlString = "<tr class='order_material_tr'><input type='hidden' class='stockable' value=''/><td class='product_move'><i class='fa fa-arrows'></i></td>";
    htmlString += "<td class='approve_product_td'></td>";
    htmlString += "<td class='product_td'><select style='width:100% !important;' class='product_number_select2 newRowselect2 form-control product product_number' onchange='productOnchnage(this, \"1\", \"" + usertype + "\",\"" + user_warehouse_resposible + "\", \"" + user_warehouse_resposible_id + "\");'>" + product_options + "'</select><input class='labelProduct form-control hide_div produt_text'/></td>";
    htmlString += "<td class='product__description_td'><input class='product_description form-control' type='text' name='description' /></td>";
    htmlString += "<td class='order_quantity_td'><input type='text' onchange='showSaveButton(this, \"1\", \"" + usertype + "\",\"" + user_warehouse_resposible + "\", \"" + user_warehouse_resposible_id + "\");');' class='form-control text-align-right order_quantity order_quantity_single numberWithSingleComma'/><label class='labelorderQuantity hide_div'>test</label></td>";
    htmlString += "<td class='unit_td'><select class='form-control unit'><option selected='selected' value=''>" + js_select_text + "</option></select></td>";
    htmlString += "<td class='cost_price_td'><input type='text' class='form-control text-align-right cost_price numberWithSingleComma'/></td>";
    htmlString += "<td class='price_td'><input type='text' class='form-control text-align-right price numberWithSingleComma'/></td>";
    htmlString += "<td class='discount_td'><input type='text' class='form-control text-align-right discount numberWithSingleComma'/></td>";
    htmlString += "<td class='sum_ex_td'><input type='text' class='form-control text-align-right sum_ex_vat numberWithSingleComma'/></td>";
    htmlString += "<td class='dg_td'><input type='text' class='form-control dg text-align-right numberWithSingleComma'/></td>";
    htmlString += "<td class='vat_td'><input type='text' class='form-control vat text-align-right numberWithSingleComma'/></td>";
    htmlString += "<td class='delivery_date_td'><div><input type='text' class='delivery_date form-control' style='position: relative !important;'></div></td>"
    htmlString += "<td></td>"; //return qty
    htmlString += "<td class='warehouse_td'><select class='newRowselect2 form-control warehouse' onchange='getProductDetailsForMaterials(this,  \"1\", \"" + usertype + "\",\"" + user_warehouse_resposible + "\", );'>" + warehouse_options + "</select><label class='labelWarehouse hide_div'>test</label></td>";
    htmlString += "<td class='location_td'><select class='newRowselect2 form-control location ' onchange='showPickedQuantity(this, 1);'>" + location_options + "</select><label class='labelLocation hide_div'>test</label>";
    htmlString += "<td class='quantity_td'><input type='text' class='form-control text-align-right quantity hide_div' data-val=1  onchange='getSerialNumberForOrderMaterial(this, 1)'/><label class='labelQuantity hide_div'>test</label></td>";
    htmlString += "<td class='invoice_qty_td'><div class='product_invoice_div' style='display: none;'><input type='text'  class='product_invoice_quantity_text form-control'></div><label class='labelQuantity invoicelabelQuantity hide_div'>test</label></td>";
    htmlString += "<td class='save_td' style='display:none;'><button type='button' class='btn btn-primary form-control save_product' onclick='saveOrderMaterial(this);' style='display:none;'>" + save_text + "</button></td>";
    htmlString += "<td class='update_td' style='display:none;'><button type='button' class='btn btn-primary form-control update_product' onclick='updateOrderMaterialData(this);' style='display:none;'>" + update_text + "</button></td>";
    var unique_id = createUUID();
    htmlString += "<td class='shippingtd'></td><td class='info_td'><a class='stock_info_btn hide_div' type='button' onclick='showStockInfo(this);' unique_id=" + unique_id + "><i class='fa fa-info-circle'></i></a></td>";
    htmlString += "<td class='remove_td'><a type='button' onclick='removeOrderMaterial(this);'><i class='delete-icon fa fa-trash'></i></a></td></tr>";
    $("#order_material_Table tbody").prepend(htmlString);
    setTimeout(function() {
        $('.delivery_date').datetimepicker({
            format: 'DD.MM.YYYY',
            locale: 'en-gb'
        }).on("dp.change", function(e) {
            setContentDate($(this));
        });
        $(".newRowselect2").select2({
            closeOnSelect: true
        });
        $('.product_number_select2').select2({
            formatSelection: formatSelection,
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
                url: '/getSelect2Products/1',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.product_text,
                                id: item.id,
                            }
                        })
                    };
                },
                cache: false,
            },
        });
    }, 100);
});

function formatSelection(val) {
    return val.product_number;
}
/**
 * [productOnchnage description]
 * @return {[type]} [description]
 */
function productOnchnage(obj, type = false, usertype = false, user_warehouse_resposible = false, user_warehouse_resposible_id = false) {
    if ($(obj).closest('tr').find('#is_content').val() != 1) {
        $(obj).parent('td').siblings('.quantity_td').find('.quantity').addClass('hide_div');
        $(obj).parent('td').siblings('.save_td').find('save_product').attr('save_val', 0);
        //Info btn logic
        $(obj).closest('tr').find('.stock_info_btn').addClass('hide_div');
        $(obj).parent('td').siblings('.quantity_td').find('.quantity').val('');
    }
    var selected_product = $(obj).val();
    if (selected_product != '' || selected_product != null || selected_product != 'undefined' || selected_product != 'Select' || select_product != 'velg') {
        $(obj).closest('tr').find('.stock_info_btn').removeClass('hide_div');
        $.ajax({
            type: "POST",
            url: url + "/material/getWarehouseOption",
            data: {
                '_token': token,
                'rest': 'true',
                'product_id': selected_product,
            },
            success: function(response) {
                decoded_response = $.parseJSON(response);
                var warehouses = decoded_response.data;
                var warehouse_options = "<option value='Select'>" + js_select_text + "</option>";;
                var usertype = $("#hidden_usertype").val().trim();
                var user_warehouse_resposible = $("#hidden_user_warehouse_resposible").val();
                var user_warehouse_resposible_id = $("#hidden_user_warehouse_resposible_id").val();
                var warehouseLength = Object.keys(warehouses).length;
                if (usertype == "User" && user_warehouse_resposible == 1) {
                    $.each(warehouses, function(index, value) {
                        if (index == user_warehouse_resposible_id) {
                            warehouse_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                        } else {
                            warehouse_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                        }
                    });
                } else {
                    $.each(warehouses, function(index, value) {
                        warehouse_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                    });
                }
                $(obj).closest('tr').find('.warehouse').html(warehouse_options);
                setTimeout(function() {
                    if (usertype == "User" && user_warehouse_resposible == 1) {
                        $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').val(user_warehouse_resposible_id).trigger('change');
                    } else {
                        $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').val('Select').trigger('change');
                        $(obj).parent('td').siblings('.location_td').find('.location').val('Select').trigger('change');
                    }
                }, 200);
            },
            fail: function() {}
        });
        //adding product details
        if ($(obj).closest('tr').find('.update_product').attr('save_val') != 1) {
            $.ajax({
                type: "get",
                url: url + "/product/getProductDetailForOffer/" + selected_product,
                data: {
                    '_token': token,
                    'rest': 'true',
                    'product_id': selected_product
                },
                success: function(response) {
                    if (response) {
                        $(obj).closest('tr').find('.stock-info-btn').show();
                        var jsonresult = $.parseJSON(response);
                        var product_string = $(obj).find(":selected").text();
                        var product_string_array = product_string.split("-");
                        product_string_array.shift();
                        $(obj).closest('tr').find('.product_description').val(product_string_array.join("-"));
                        if (jsonresult['status'] == 'success') {
                            var units = $("#hidden_units").val();
                            units = $.parseJSON(units);
                            var unit_options = "<option value='Select'>" + js_select_text + "</option>";
                            var product_details = jsonresult['data'];
                            if (product_details['is_package'] == 0) {
                                $.each(units, function(index, value) {
                                    if (product_details['unit'] == index) {
                                        unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                                    } else {
                                        unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                                    }
                                });
                            } else {
                                $(obj).closest('tr').find('.stock-info-btn').hide();
                                $.each(units, function(index, value) {
                                    if (index == 2) {
                                        unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                                    } else {
                                        unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                                    }
                                });
                            }
                            $(obj).closest('tr').find('.unit').html(unit_options);
                            var product_price = parseFloat(product_details['sale_price']);
                            product_price = replaceDot(product_price);
                            $(obj).closest('tr').find('.price').val(product_price);
                            var vat = parseFloat(product_details['tax']);
                            vat = vat.toFixed(2);
                            vat = replaceDot(vat);
                            $(obj).closest('tr').find('.vat').val(vat);
                            var cost_price = parseFloat(product_details['cost_price']);
                            $(obj).closest('tr').find('.cost_price').val(replaceDot(cost_price));
                            $(obj).closest('tr').find('.stockable').val(product_details['stockable']);
                            $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').next(".select2-container").show();
                            $(obj).parent('td').siblings('.location_td').find('.location').next(".select2-container").show();
                            if (product_details['stockable'] == 0) {
                                $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').next(".select2-container").hide();
                                $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').removeClass('newRowselect2');
                                $(obj).parent('td').siblings('.location_td').find('.location').next(".select2-container").hide();
                                $(obj).parent('td').siblings('.location_td').find('.location').removeClass('newRowselect2');
                            }
                            setTimeout(function() {
                                calculateDG($(obj));
                            }, 100)
                        }
                    }
                },
                fail: function(response) {
                    console.log("Something Went Wrong")
                }
            });
        } else {
            $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').next(".select2-container").show();
            $(obj).parent('td').siblings('.location_td').find('.location').next(".select2-container").show();
            if ($(obj).closest('tr').find('.stockable').val() == 0) {
                $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').next(".select2-container").hide();
                $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').removeClass('newRowselect2');
                $(obj).parent('td').siblings('.location_td').find('.location').next(".select2-container").hide();
                $(obj).parent('td').siblings('.location_td').find('.location').removeClass('newRowselect2');
            }
        }
    }
}
$(document).on("change", ".produt_text", function() {
    $(this).parent('td').siblings('.warehouse_td').find('.warehouse').next(".select2-container").show();
    $(this).parent('td').siblings('.location_td').find('.location').next(".select2-container").show();
    if ($(this).closest('tr').find('.stockable').val() == 0) {
        $(this).parent('td').siblings('.warehouse_td').find('.warehouse').next(".select2-container").hide();
        $(this).parent('td').siblings('.location_td').find('.location').next(".select2-container").hide();
    }
});
$(document).on("change", ".order_quantity, .price, .discount, .cost_price", function() {
    var qty = $(this).closest('tr').find('.order_quantity').val();
    var price = $(this).closest('tr').find('.price').val();
    price = replaceComma(price);
    qty = replaceComma(qty);
    var discount = $(this).closest('tr').find('.discount').val();
    if (discount) {
        discount = replaceComma(discount);
    } else {
        discount = 0;
    }
    $(this).closest('tr').find('.sum_ex_vat').val('');
    if (price && qty) {
        total_val = parseFloat(qty) * parseFloat(price);
        if (discount) {
            total_val = (parseFloat(qty) * parseFloat(price)) - (parseFloat(qty) * parseFloat(price) * parseFloat(discount)) / 100;
        }
        total_val = total_val.toFixed(2);
        total_val = replaceDot(total_val);
        $(this).closest('tr').find('.sum_ex_vat').val(total_val);
    }
    calculateDG($(this));
});

function calculateDG(obj) {
    var price = obj.closest('tr').find('.sum_ex_vat').val();
    price = replaceComma(price);
    if (price > 0) {
        var cost_price = obj.closest('tr').find('.cost_price').val();
        cost_price = replaceComma(cost_price);
        var dg = (price - cost_price) / price * 100;
        dg = replaceDot(dg);
        obj.closest('tr').find('.dg').val(dg);
    }
}
/**
 * [showSaveButton description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function showSaveButton(obj, type, usertype = false, user_warehouse_resposible = false, user_warehouse_resposible_id = false) {
    var value = replaceComma($(obj).val());
    if (value > 0) {
        if ($(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == undefined || $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == '') {
            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 1);
        } else {
            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
        }
    } else {
        if ($(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == undefined || $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == '') {
            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 0);
        } else {
            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 0);
        }
    }
}
// get product details
function getProductDetailsForMaterials(obj, type, usertype = false, user_warehouse_resposible = false, location_id = false) {
    if ($(obj).attr('noaction') != 1) {
        $(obj).parent('td').siblings('.quantity_td').find('.quantity').val("");
        $(obj).parent('td').siblings('.location_td').find('select').html('');
        var warehouse_id = $(obj).val();
        var warehouse_product_url = url + "/ordermaterial/getProductDetail";
        var selected_product = $(obj).parent('td').siblings('.product_td').find('select').val();
        if (type == 1) {
            var selected_product = $(obj).parent('td').siblings('.product_td').find('select').val();
            if (selected_product == '' || selected_product == null || selected_product == 'undefined' || selected_product == 'Select' || select_product == 'velg') {
                showAlertMessage(select_product);
                $(obj).val('');
                return false;
            }
        } else if (type == 2) {
            var selected_product = $(obj).parent('td').siblings('.product_td').find('.product_number').val();
        }
        if (warehouse_id != "Select" && warehouse_id != null && warehouse_id != undefined && warehouse_id != "" && warehouse_id != "Select") {
            $.ajax({
                type: "POST",
                url: warehouse_product_url,
                data: {
                    '_token': token,
                    'rest': 'true',
                    'product_id': selected_product,
                    'warehouse_id': warehouse_id,
                    'order_type': '1',
                },
                success: function(response) {
                    if (response) {
                        try {
                            decoded_response = $.parseJSON(response);
                            if (decoded_response['status'] == SUCCESS) {
                                var product_details = decoded_response['data'];
                                var product_location = product_details['serial_numbers'];
                                console.log(product_location[0]);
                                var location_options = "<option value='Select'>" + js_select_text + "</option>";
                                if (product_location.length != 0) {
                                    for (var i = 0; i < product_location.length; i++) {
                                        location_options += "<option value=" + product_location[i]['ID'] + " id=" + product_location[i]['ID'] + ">" + product_location[i]['NAME'] + "</option>";
                                    }
                                }
                                $(obj).parent('td').siblings('.location_td').find('select').html(location_options);
                                if (product_location.length == 1 && product_location[0].qty > 0) {
                                    $(obj).parent('td').siblings('.location_td').find('.location').val(product_location[0].ID).trigger('change');
                                    var order_quantity = replaceComma($(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').val());
                                    if (product_location[0].qty >= order_quantity) {
                                        $(obj).parent('td').siblings('.quantity_td').find('.quantity').val($(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').val());
                                        $(obj).parent('td').siblings('.quantity_td').find('.quantity').focus();
                                    }
                                } else {
                                    $(obj).parent('td').siblings('.location_td').find('.location').val('Select').trigger('change');
                                    $(obj).parent('td').siblings('.location_td').find('.location').focus();
                                }
                                setTimeout(function() {
                                    if (location_id) {
                                        $(obj).parent('td').siblings('.location_td').find('.location').val(location_id).trigger('change');
                                        $(obj).closest('tr').find('.quantity').val($(obj).closest('tr').find('.order_quantity').val()).trigger('change');
                                    }
                                }, 200)
                            }
                        } catch (Exception) {}
                    }
                },
                fail: function() {}
            });
        } else {
            var location_options = "<option value='Select'>" + js_select_text + "</option>";
            $(obj).closest('tr').find('.location').html(location_options);
            $(obj).parent('td').siblings('.location_td').find('.location').val('Select').trigger('change');
        }
    }
}
// get serial numbers
function getSerialNumberForOrderMaterial(obj = false, type = false, order_id = false, order_quantity = false, order_material_id = false, package_product = false) {
    $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 0);
    $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 0);
    var order_quantity_val = replaceComma($(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').val());
    if (order_quantity_val == '' || order_quantity_val == null || order_quantity_val == 'undefined') {
        showAlertMessage(quantity_required);
        $(obj).val('');
        $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').focus();
        return false;
    }
    var quantity = replaceComma($(obj).val());
    if (package_product == 1) {
        if (quantity != order_quantity_val) {
            showAlertMessage(check_package_quantity);
            $(obj).val('');
            $(obj).focus();
            return false;
        }
    }
    if (quantity > order_quantity_val) {
        showAlertMessage(check_picked_quantity);
        if ($(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == undefined || $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == '') {
            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 1);
        } else {
            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
        }
        $(obj).val('');
        $(obj).focus();
        return false;
    }
    var product_url = url + "/ordermaterial/getProductAvailabeQuantity";
    var location = $(obj).parent('td').siblings('.location_td').find('.location').val();
    var product_id = $(obj).parent('td').siblings('.product_td').find('.product').val();
    var warehouse = $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').val();
    $.ajax({
        type: "POST",
        url: product_url,
        data: {
            '_token': token,
            'rest': 'true',
            'product_id': product_id,
            'warehouse_id': warehouse,
            'location_id': location,
            'order_type': '1',
        },
        success: function(response) {
            decoded_response = $.parseJSON(response);
            if (decoded_response['status'] == SUCCESS) {
                var product_avaliable_quantity = decoded_response['data'];
                if (parseInt(product_avaliable_quantity) < parseInt(quantity)) {
                    showAlertMessage(quantity_not_in_stock);
                    $(obj).val('');
                    $(obj).attr('data-val', 0);
                    $(obj).focus();
                    setTimeout(function() {
                        if ($(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == undefined || $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == '') {
                            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 1);
                            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 0);
                        } else {
                            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
                            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 0);
                        }
                    }, 100);
                    return false;
                }
            }
        },
        fail: function() {}
    });
    setTimeout(function() {
        $(obj).attr('data-val', $(obj).val());
        if ($(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == undefined || $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == '') {
            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 1);
            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 0);
        } else {
            $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
            $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 0);
        }
    }, 500);
}
// save order material
function saveOrderMaterial(obj, material_reference_id, type) {
    displayBlockUI();
    var product_id = $(obj).parent('td').siblings('.product_td').find('.product_number').val();
    var location_id = $(obj).parent('td').siblings('.location_td').find('.location').val();
    var warehouse_id = $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').val();
    var order_quantity = replaceComma($(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').val());
    var quantity = replaceComma($(obj).parent('td').siblings('.quantity_td').find('.quantity').val());
    var user_id = '';
    var delivery_date = $(obj).parent('td').siblings('.delivery_date_td').find('.delivery_date').val();
    var id = $(obj).attr('data-val');
    var order_id = $("#product_order_id").val();
    var order_offer_product_id = $(obj).closest('tr').find('.order_offer_product_id').val();
    if (quantity == null || quantity == undefined) {
        quantity = 0;
    }
    if (location_id == 'Select' || location_id == undefined) {
        location_id = null;
    }
    if (warehouse_id == 'Select' || warehouse_id == undefined) {
        warehouse_id = null;
    }
    //code for package
    var is_package = $(obj).closest('tr').find('#is_package').val();
    if (is_package == undefined) {
        is_package = 0;
    }
    var package_quantity = replaceComma($(obj).closest('tr').find('#package_quantity').val());
    if (package_quantity == undefined) {
        package_quantity = '';
    }
    var reference_id = '';
    var sort_number = '';
    if (material_reference_id) {
        reference_id = material_reference_id;
        sort_number = $(obj).closest('tr').find('#sort_number').val();
    }
    $(obj).attr('disabled', 'disabled');
    var product_string = $(obj).parent('td').parent().find('.product_number option:selected').text();
    var product_string_array = product_string.split("-");
    $.ajax({
        url: url + "/ordermaterial/customStore",
        type: "POST",
        async: false,
        data: {
            '_token': token,
            'rest': 'true',
            'product': product_id,
            'location': location_id,
            'order_quantity': order_quantity,
            'quantity': quantity ? quantity : 0,
            'user': user_id,
            'warehouse': warehouse_id,
            'id': id ? id : "",
            'order_id': order_id,
            'is_package': is_package,
            'reference_id': reference_id,
            'sort_number': sort_number,
            'package_quantity': package_quantity,
            'order_offer_product_id': order_offer_product_id ? order_offer_product_id : null,
            'delivery_date': delivery_date,
            'sortorderval': $(obj).closest('tr').attr('sortorderval'),
            'unit': $(obj).closest('tr').find('.unit').val(),
            'sum_ex_vat': $(obj).closest('tr').find('.sum_ex_vat').val(),
            'vat': $(obj).closest('tr').find('.vat').val(),
            'discount': $(obj).closest('tr').find('.discount').val(),
            'price': $(obj).closest('tr').find('.price').val(),
            'cost_price': $(obj).closest('tr').find('.cost_price').val(),
            'product_description': $(obj).closest('tr').find('.product_description').val(),
            'dg': $(obj).closest('tr').find('.dg').val(),
            'stockable': $(obj).closest('tr').find('.stockable').val(),
            'prod_nbr': product_string_array[0]
        },
        success: function(response) {
            decoded_response = $.parseJSON(response);
            if (decoded_response['status'] == SUCCESS) {
                $('.picklist').show();
                //Warehouse
                if (warehouse_id != null && warehouse_id != '' && warehouse_id != 'Select' && warehouse_id != 'undefined') {
                    $(obj).parent('td').siblings('.warehouse_td').find('span').hide();
                    $(obj).parent('td').siblings('.warehouse_td').find('select').removeClass('select2');
                    $(obj).parent('td').siblings('.warehouse_td').find('span').remove();
                    $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').removeClass('newRowselect2');
                    $(obj).parent('td').parent().find('.labelWarehouse').html($(obj).parent('td').parent().find('.warehouse option:selected').text());
                    $(obj).parent('td').parent().find('.labelWarehouse').removeClass('hide_div');
                }
                //Locations
                if (location_id != null && location_id != '' && location_id != 'Select' && location_id != 'undefined') {
                    $(obj).parent('td').siblings('.location_td').find('span').hide();
                    $(obj).parent('td').siblings('.location_td').find('select').removeClass('select2');
                    $(obj).parent('td').siblings('.location_td').find('span').remove();
                    $(obj).parent('td').parent().find('.labelLocation').html($(obj).parent('td').parent().find('.location option:selected').text());
                    $(obj).parent('td').parent().find('.labelLocation').removeClass('hide_div');
                    $(obj).parent('td').siblings('.location_td').find('.location').removeClass('newRowselect2');
                    //Quantity
                    if (quantity > 0) {
                        $(obj).parent('td').siblings('.quantity_td').find('.quantity').hide();
                        $(obj).parent('td').parent().find('.labelQuantity').html($(obj).parent('td').parent().find('.quantity').val());
                        $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                    } else {
                        if (type == 3) {
                            if (quantity > 0) {
                                $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                                $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                                $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                                $(obj).parent('td').siblings('.quantity_td').find('.quantity').show();
                            }
                        } else {
                            $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                            $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                            $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                            $(obj).parent('td').siblings('.quantity_td').find('.quantity').show();
                        }
                    }
                }
                // product
                $(obj).parent('td').siblings('.product_td').find('span').hide();
                $(obj).parent('td').siblings('.product_td').find('select').removeClass('select2');
                $(obj).parent('td').siblings('.product_td').find('span').remove();
                $(obj).parent('td').siblings('.product_td').find('.product_number').removeClass('product_number_select2 newRowselect2 select2-hidden-accessible');
                $(obj).parent('td').siblings('.product_td').find('.product_number').hide();
                $(obj).parent('td').parent().find('.labelProduct').val(product_string_array[0]);
                $(obj).parent('td').parent().find('.labelProduct').removeClass('hide_div');
                $(obj).parent('td').parent().find('.labelProduct').attr('disabled', 'disabled');
                //Set save btn attr val
                $(obj).attr('save_id', 1);
                $(obj).attr('save_val', 0);
                //Set the invoice quantity
                $(obj).parent('td').siblings(".invoice_qty_td").find('.product_invoice_div').find('.product_invoice_quantity_text').val($(obj).parent('td').parent().find('.quantity').val());
                //set the delete icon
                if (type != 3) {
                    var delete_icon = '<a href="' + url + '/ordermaterial/' + decoded_response['data'] + '" data-method="delete" data-modal-text="Are you sure you want to delete this product?" data-csrf="' + token + '"><i class="delete-icon fa fa-trash"></i></a>';
                    $(obj).parent('td').siblings('.remove_td').html(delete_icon);
                }
                if (usertype != "User") {
                    $(obj).parent('td').parent().find('.approve_product').removeClass('hide_div');
                }
                //Set approve product loigc
                $(obj).parent('td').parent().find('.approve_product').val(decoded_response['data']);
                $(obj).closest('tr').attr("material_id", decoded_response['data']);
                $(obj).parent('td').parent().find('.approve_product').attr("id", "approve_product_" + decoded_response['data']);
                var order_material_id = decoded_response['data'];
                if (order_quantity == quantity) {
                    $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                    $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                    $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                    $(obj).parent('td').siblings('.quantity_td').find('.quantity').hide();
                    if (type == 3) {
                        $(obj).parent('td').parent().find('.ContentlabelQuantity').html(0);
                        $(obj).parent('td').parent().find('.ContentlabelQuantity').removeClass('hide_div');
                    } else {
                        $(obj).parent('td').parent().find('.labelQuantity').html($(obj).parent('td').parent().find('.quantity').val());
                        $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                    }
                    $("#approve_selected_products").show();
                }
                $(obj).parent('td').siblings('.approve_product_td').find('.approve_product').addClass('hide_div');
                $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
                $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val', order_material_id);
                $(obj).closest('tr').find('.update_product').attr('data-val', order_material_id);
                $(obj).parent('td').siblings('.update_td').find('.update_product').removeAttr('disabled');
                if (is_package == 1) {
                    savePacakgeProducts(obj, order_material_id)
                }
                bootbox.hideAll();
                setTimeout(function() {
                    laravel.initialize();
                }, 500);
            } else {
                $(obj).closest('tr').find('.warehouse').val('Select').trigger('change');
            }
            $.unblockUI();
        },
        fail: function() {
            showAlertMessage("Something Went Wrong");
            $.unblockUI();
        }
    });
}
$(document).on("change", ".order_quantity_single", function() {
    var order_quantity_val = replaceComma($(this).val());
    var quantity_val = replaceComma($(this).closest('tr').find('.quantity').val());
    if (quantity_val != undefined && quantity_val != '' && quantity_val != null) {
        if (order_quantity_val < quantity_val) {
            showAlertMessage(order_qty_validation_msg);
            $(this).val($(this).closest('tr').find('.quantity').val());
            if ($(this).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == undefined || $(this).parent('td').siblings('.update_td').find('.update_product').attr('data-val') == '') {
                $(this).parent('td').siblings('.save_td').show();
                $(this).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 1);
            } else {
                $(this).parent('td').siblings('.update_td').show();
                $(this).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
            }
        }
    }
});
// save order material
function updateOrderMaterialData(obj, type) {
    displayBlockUI();
    var sn_required = $(obj).parent('td').siblings('.warehouse_td').find('.sn_required').val();
    var product_id = $(obj).parent('td').siblings('.product_td').find('.product_number').val();
    var location_id = $(obj).parent('td').siblings('.location_td').find('.location').val();
    var warehouse_id = $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').val();
    var order_quantity = replaceComma($(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').val());
    var quantity = replaceComma($(obj).parent('td').siblings('.quantity_td').find('.quantity').val());
    var delivery_date = $(obj).parent('td').siblings('.delivery_date_td').find('.delivery_date').val();
    if (type == 2) {
        quantity = replaceComma($(obj).closest('tr').find('.product_invoice_quantity_text').val());
    }
    var is_package = $(obj).closest('tr').find('#is_package').val();
    if (is_package == undefined) {
        is_package = 0;
    }
    var package_quantity = replaceComma($(obj).closest('tr').find('#package_quantity').val());
    if (package_quantity == undefined) {
        package_quantity = '';
    }
    var reference_id = '';
    var sort_number = '';
    if (type == 3) {
        reference_id = $(obj).closest('tr').find('#reference_id').val();
        sort_number = $(obj).closest('tr').find('#sort_number').val();
    }
    var user_id = '';
    var id = $(obj).attr('data-val');
    var order_id = $("#product_order_id").val();
    if (quantity == null || quantity == undefined) {
        quantity = 0;
    }
    if (location_id == 'Select' || location_id == undefined) {
        location_id = null;
    }
    if (warehouse_id == 'Select' || warehouse_id == undefined) {
        warehouse_id = null;
    }
    var serial_numbers_array = [];
    for (var i = 0; i < quantity; i++) {
        serial_numbers_array[i] = $(obj).parent("td").siblings('.serial_number_td').find('#serial_number_' + i).val();
    }
    var order_offer_product_id = $(obj).closest('tr').find('.order_offer_product_id').val();
    $.ajax({
        url: url + "/ordermaterial/customStore",
        type: "POST",
        async: false,
        data: {
            '_token': token,
            'rest': 'true',
            'product': product_id,
            'location': location_id,
            'order_quantity': order_quantity,
            'quantity': quantity ? quantity : 0,
            'sn_required': sn_required,
            'user': user_id,
            'warehouse': warehouse_id,
            'serial_numbers': serial_numbers_array,
            'id': id ? id : "",
            'order_id': order_id,
            'is_package': is_package,
            'reference_id': reference_id,
            'sort_number': sort_number,
            'package_quantity': package_quantity,
            'order_offer_product_id': order_offer_product_id ? order_offer_product_id : null,
            'delivery_date': delivery_date,
            'sortorderval': $(obj).closest('tr').attr('sortorderval'),
            'unit': $(obj).closest('tr').find('.unit').val(),
            'sum_ex_vat': $(obj).closest('tr').find('.sum_ex_vat').val(),
            'vat': $(obj).closest('tr').find('.vat').val(),
            'discount': $(obj).closest('tr').find('.discount').val(),
            'price': $(obj).closest('tr').find('.price').val(),
            'cost_price': $(obj).closest('tr').find('.cost_price').val(),
            'product_description': $(obj).closest('tr').find('.product_description').val(),
            'dg': $(obj).closest('tr').find('.dg').val(),
            'stockable': $(obj).closest('tr').find('.stockable').val(),
        },
        success: function(response) {
            decoded_response = $.parseJSON(response);
            if (decoded_response['status'] == SUCCESS) {
                if (warehouse_id != null && warehouse_id != '' && warehouse_id != 'Select' && warehouse_id != 'undefined') {
                    $(obj).parent('td').siblings('.warehouse_td').find('span').hide();
                    $(obj).parent('td').siblings('.warehouse_td').find('select').removeClass('select2');
                    $(obj).parent('td').siblings('.warehouse_td').find('span').remove();
                    $(obj).parent('td').parent().find('.labelWarehouse').html($(obj).parent('td').parent().find('.warehouse option:selected').text());
                    $(obj).parent('td').parent().find('.labelWarehouse').removeClass('hide_div');
                    $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').removeClass('newRowselect2');
                }
                if (location_id != null && location_id != '' && location_id != 'Select' && location_id != 'undefined') {
                    $(obj).parent('td').siblings('.location_td').find('span').hide();
                    $(obj).parent('td').siblings('.location_td').find('select').removeClass('select2');
                    $(obj).parent('td').siblings('.location_td').find('span').remove();
                    $(obj).parent('td').parent().find('.labelLocation').html($(obj).parent('td').parent().find('.location option:selected').text());
                    $(obj).parent('td').parent().find('.labelLocation').html($(obj).parent('td').parent().find('.location option:selected').text());
                    $(obj).parent('td').parent().find('.labelLocation').removeClass('hide_div');

                    $(obj).parent('td').siblings('.location_td').find('.location').removeClass('newRowselect2');

                    if (quantity > 0) {
                        $(obj).parent('td').siblings('.quantity_td').find('.quantity').hide();
                        $(obj).parent('td').parent().find('.labelQuantity').html($(obj).parent('td').parent().find('.quantity').val());
                        $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                    } else {
                        if (type == 3) {
                            if (quantity > 0) {
                                $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                                $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                                $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                                $(obj).parent('td').siblings('.quantity_td').find('.quantity').show();
                            }
                        } else {
                            $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                            $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                            $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                            $(obj).parent('td').siblings('.quantity_td').find('.quantity').show();
                        }
                    }
                }
                // hide input fields
                $(obj).parent('td').siblings('.product_td').find('span').hide();
                $(obj).parent('td').siblings('.serial_number_td').find('span').hide();
                $(obj).parent('td').siblings('.product_td').find('select').removeClass('select2');
                $(obj).parent('td').siblings('.serial_number_td').find('select').removeClass('select2');
                // remove span to solve the select2 reappend issue
                $(obj).parent('td').siblings('.product_td').find('span').remove();
                $(obj).parent('td').siblings('.serial_number_td').find('span').remove();
                // $(obj).parent('td').parent().find('.labelProduct').val($(obj).parent('td').parent().find('.product_number option:selected').text());
                if (type == 2) {
                    $(obj).parent('td').siblings(".invoice_qty_td").find('.product_invoice_div').find(quantity);
                    $(obj).parent('td').siblings(".invoice_qty_td").find('.product_invoice_div').attr('data-val', quantity);
                } else {
                    $(obj).parent('td').siblings(".invoice_qty_td").find('.product_invoice_div').find('.product_invoice_quantity_text').val($(obj).parent('td').parent().find('.quantity').val());
                }
                if (type != 3) {
                    var delete_icon = '<a href="' + url + '/ordermaterial/' + decoded_response['data'] + '" data-method="delete" data-modal-text="Are you sure you want to delete this product?" data-csrf="' + token + '"><i class="delete-icon fa fa-trash"></i></a>';
                    $(obj).parent('td').siblings('.remove_td').html(delete_icon);
                }
                if (usertype != "User") {
                    $(obj).parent('td').parent().find('.approve_product').removeClass('hide_div');
                }
                $(obj).parent('td').parent().find('.approve_product').val(decoded_response['data']);
                $(obj).parent('td').parent().find('.approve_product').attr("id", "approve_product_" + decoded_response['data']);
                // $(obj).parent('td').parent().find('.labelProduct').removeClass('hide_div');
                $(obj).parent('td').parent().find('.labelSerialNumber').removeClass('hide_div');
                $(obj).parent('td').parent().find('.labelUser').removeClass('hide_div');
                var order_material_id = decoded_response['data'];
                if (order_quantity == quantity) {
                    $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                    $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                    $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                    $(obj).parent('td').siblings('.quantity_td').find('.quantity').hide();
                    //Added on 15.03.2018 For pacakges
                    if (type == 2) {
                        $(obj).closest('tr').find('.product_invoice_quantity_text').hide();
                        $(obj).parent('td').parent().find('.labelQuantity').html(quantity);
                        $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                    } else if (type == 3) {
                        $(obj).parent('td').parent().find('.ContentlabelQuantity').html(0);
                        $(obj).parent('td').parent().find('.ContentlabelQuantity').removeClass('hide_div');
                    } else {
                        $(obj).parent('td').parent().find('.labelQuantity').html($(obj).parent('td').parent().find('.quantity').val());
                        $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                    }
                    $("#approve_selected_products").show();
                    $(obj).hide();
                } else {
                    //This for package added on 15.03.2018
                    if (type == 2 && quantity != undefined && quantity > 0) {
                        if (quantity != undefined && quantity > 0) {
                            $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').hide();
                            $(obj).parent('td').parent().find('.labelorderQuantity').html($(obj).parent('td').parent().find('.order_quantity').val());
                            $(obj).parent('td').parent().find('.labelorderQuantity').removeClass('hide_div');
                            $(obj).parent('td').siblings('.quantity_td').find('.quantity').hide();
                            //Added on 15.03.2018 For pacakges
                            if (type == 2) {
                                $(obj).closest('tr').find('.product_invoice_quantity_text').hide();
                                $(obj).parent('td').parent().find('.labelQuantity').html(quantity);
                                $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                            } else {
                                $(obj).parent('td').parent().find('.labelQuantity').html($(obj).parent('td').parent().find('.quantity').val());
                                $(obj).parent('td').parent().find('.labelQuantity').removeClass('hide_div');
                            }
                            $("#approve_selected_products").show();
                            $(obj).hide();
                        }
                    } else {
                        $(obj).parent('td').siblings('.order_quantity_td').find('.order_quantity').show();
                        $(obj).parent('td').parent().find('.labelorderQuantity').html('');
                    }
                }
                $(obj).parent('td').siblings('.approve_product_td').find('.approve_product').addClass('hide_div');
                $(obj).parent('td').siblings('.update_td').show();
                $(obj).parent('td').siblings('.update_td').find('.update_product').attr('save_val', 1);
                $(obj).parent('td').siblings('.update_td').find('.update_product').attr('data-val', order_material_id);
                $(obj).parent('td').siblings('.update_td').find('.update_product').removeAttr('disabled');
                $(obj).parent('td').siblings('.save_td').hide();
                $(obj).parent('td').siblings('.save_td').find('.save_product').attr('save_val', 0);
                //Logic to enable the pakage product  approved button
                var is_package = $(obj).closest('tr').attr('data-val');
                if (is_package != undefined) {
                    enableInvoiceQunatityForPackage(obj);
                }
                bootbox.hideAll();
                setTimeout(function() {
                    if (type != 3) {
                        laravel.initialize();
                    }
                }, 750);
            } else {
                $(obj).closest('tr').find('.warehouse').val('Select').trigger('change')
            }
            $.unblockUI();
        },
        fail: function() {
            showAlertMessage("Something Went Wrong");
            $.unblockUI();
        }
    });
}
// remove tr
function removeOrderMaterial(obj, type) {
    var confirm_delete_tr = confirm(deletemessage);
    if (confirm_delete_tr) {
        if (type == 1) {
            var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
            $(elements).each(function() {
                $(this).remove();
            });
        } else {
            $(obj).parent().parent().remove();
        }
    }
}
/**
 * [showPickedQuantity description]
 * @return {[type]} [description]
 */
function showPickedQuantity(obj, type) {
    $(obj).parent('td').siblings('.quantity_td').find('.quantity').val(""); //Added on 29.4.2018
    var location = $(obj).val();
    var product_id = $(obj).parent('td').siblings('.product_td').find('.product').val();
    var order_quantity = replaceComma($(obj).parent('td').siblings('.order_quantity').find('.order_quantity').val());
    var warehouse = $(obj).parent('td').siblings('.warehouse_td').find('.warehouse').val();
    var product_url = url + "/ordermaterial/getProductAvailabeQuantity";
    if (location != null && location != 'Select') {
        if (order_quantity != undefined) {
            $(obj).parent('td').siblings('.save_td').show();
            $(obj).parent('td').siblings('.save_td').find('save_product').attr('save_val', 1);
        }
        $(obj).closest('tr').find('.quantity').removeClass('hide_div');
    }
}
$(document).on("change", ".approve_product", function() {
    var currentTD = $(this).parents('tr').find('td');
    if ($(this).is(":checked")) {
        $(currentTD).find(".product_invoice_label").hide();
        $(currentTD).find(".package_invoice_label").hide();
        $(currentTD).find(".invoicelabelQuantity").hide();
        $(currentTD).find(".product_invoice_div").show();
        $(currentTD).find(".product_invoice_quantity_text").show();
    } else {
        $(currentTD).find(".product_invoice_label").show();
        $(currentTD).find(".package_invoice_label").show();
        $(currentTD).find(".invoicelabelQuantity").show();
        $(currentTD).find(".product_invoice_div").hide();
        $(currentTD).find(".product_invoice_quantity_text").hide();
    }
});
$("#approve_selected_products").click(function() {
    var selected_products = [];
    var invoice_quantity = [];
    $.each($(".approve_product:checked"), function() {
        var hourlogg_id = $(this).val();
        if (hourlogg_id != 'on') {
            selected_products.push($(this).val());
            var currentTD = $(this).parents('tr').find('td');
            invoice_quantity.push({
                "product_id": hourlogg_id,
                "material_id": $(this).val(),
                "delivery_date": $(currentTD).find(".delivery_date").val(),
                "invoice_quantity": $(currentTD).find(".product_invoice_quantity_text").val()
            });
        }
    });
    $("#hidden_approved_product_invoice_quantity").val(JSON.stringify(invoice_quantity));
    $("#hidden_approved_product_ids").val(selected_products);
    if (selected_products.length > 0) {
        $("#approve_product_form").submit();
    }
});
/**
 * [getPackageProducts description]
 * @param  {[type]} package_id [description]
 * @return {[type]}            [description]
 */
function getPackageProducts(package_id) {
    if (package_id) {
        var decoded_data = '';
        var product_url = url + "/ordermaterial/getPacakageProduct";
        if (package_id) {
            $.ajax({
                type: "POST",
                url: product_url,
                asyc: true,
                data: {
                    '_token': token,
                    'rest': 'true',
                    'package_id': package_id
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

function generateProductPackageRow(product_package) {
    var usertype = $("#hidden_usertype").val().trim();
    var warehouse_options = "<option value='Select'>" + js_select_text + "</option>";;
    var usertype = $("#hidden_usertype").val().trim();
    var user_warehouse_resposible = $("#hidden_user_warehouse_resposible").val();
    var user_warehouse_resposible_id = $("#hidden_user_warehouse_resposible_id").val();
    var user_id = $("#logged_user_id").val();
    var user_name = $("#logged_user_name").val();
    var warehouses = $("#hidden_warehouses").val();
    var warehouses = $.parseJSON(warehouses);
    $.each(warehouses, function(index, value) {
        warehouse_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
    });
    var location_options = "<option value='Select'>" + js_select_text + "</option>";
    var product_options = "";
    if (product_package) {
        var unique_id = createUUID();
        var htmlString = "<tr class='order_material_tr' data-val='" + unique_id + "'><input type='hidden' class='stockable' value='1'/>";
        htmlString += "<td class='product_move'><i class='fa fa-arrows'></i></td><td class='approve_product_td'><input type='hidden' id='is_package' value='1' /></td>";
        product_options += "<option value=" + product_package['id'] + " selected='selected' id=" + product_package['id'] + ">" + product_package['product_number'] + " - " + product_package['description'] + "</option>";
        htmlString += "<td class='product_td'><select style='width:100% !important;' onchange='updatePackagePrices(this)' class='select2 form-control product product_number' readonly='readonly' disabled='disabled'>" + product_options + "'</select><input class='form-control labelProduct hide_div' /></td>";
        
        htmlString += "<td class='product__description_td'><input class='product_description form-control' type='text' name='description' /></td>";

        htmlString += "<td class='order_quantity_td'><input type='text'  class='form-control order_quantity validateNumbers' onchange='updatePackageProductsQuantity(this);' value='1'/><label class='labelorderQuantity hide_div'>test</label></td>";
        htmlString += "";
        htmlString += "<td class='unit_td'><select class='form-control unit'><option selected='selected' value=''>" + js_select_text + "</option></select></td>";
        htmlString += "<td class='cost_price_td'><input type='text' class='form-control text-align-right cost_price numberWithSingleComma'/></td>";
        htmlString += "<td class='price_td'><input type='text' class='form-control text-align-right price numberWithSingleComma'/></td>";
        htmlString += "<td class='discount_td'><input type='text' class='form-control text-align-right discount numberWithSingleComma'/></td>";
        htmlString += "<td class='sum_ex_td'><input type='text' class='form-control text-align-right sum_ex_vat numberWithSingleComma'/></td>";
        htmlString += "<td class='dg_td'><input type='text' class='form-control dg text-align-right numberWithSingleComma'/></td>";
        htmlString += "<td class='vat_td'><input type='text' class='form-control vat text-align-right numberWithSingleComma'/></td>";
        htmlString += "<td class='delivery_date_td'><div><input type='text' class='delivery_date form-control' style='position: relative !important;'></div></td>"
        htmlString += "<td></td>"; //Retrun qty td
        htmlString += "<td></td>";
        htmlString += "<td></td>";
        htmlString += "<td></td>";
        htmlString += "<td class='invoice_qty_td'><div class='product_invoice_div' style='display: none;'><input type='text' onchange='updatePackageInvoiceQuantity(this);' class='product_invoice_quantity_text form-control validateNumbers'></div><label class='labelQuantity package_invoice_label hide_div'>test</label></td>";
        htmlString += "<td class='save_td' style='display:none;'><button type='button' class='btn btn-primary form-control save_product' style='display:none;' save_val=1 onclick='savePackageOrderMaterial(this);'>" + save_text + "</button></td>";
        htmlString += "<td class='update_td' style='display:none;'><button type='button' class='btn btn-primary form-control update_product' onclick='updatePackageOrderMaterialData(this);' style='display:none;'>" + update_text + "</button></td>";
        htmlString += "<td></td><td></td><td class='remove_td'><a type='button' onclick='removeOrderMaterial(this,1);'><i class='delete-icon fa fa-trash'></i></a></td>";
        htmlString += "</tr>";
        $("#order_material_Table tbody").prepend(htmlString);
        if (product_package['package_products'] != undefined && product_package['package_products'].length > 0) {
            for (var i = 0; i < product_package['package_products'].length; i++) {
                var contents = product_package['package_products'][i];
                var contents_html = "<tr class='order_material_tr child_products' data-val='" + unique_id + "'><input type='hidden' class='stockable' value='1'/><input type='hidden' id='is_content' value='1' /><td></td>";
                var product_options = "";
                var unique_id_info = createUUID();
                product_options += "<option value=" + contents['product_id'] + " selected='selected' id=" + contents['product_id'] + ">" + contents['product_number'] + " - " + contents['description'] + "</option>";
                contents_html += "<td><input type='hidden' id='package_quantity' value='" + contents['qty'] + "'/> <input type='hidden' id='reference_id'/><input type='hidden' value='" + contents['sort_number'] + "' id='sort_number' name='sort_number'/></td>";
                contents_html += "<td class='product_td'><select style='width:100% !important;' class='select2 form-control product product_number' readonly='readonly' disabled='disabled' onchange='productOnchnage(this, \"1\", \"" + usertype + "\",\"" + user_warehouse_resposible + "\", \"" + user_warehouse_resposible_id + "\");'>" + product_options + "'</select><input class='form-control labelProduct hide_div' /></td>";
                contents_html += "<td class='product__description_td'><input class='product_description form-control' type='text' name='description' /></td>";
                contents_html += "<td class='order_quantity_td'><input type='text' onchange='showSaveButton(this, 1);' data-val='" + contents['qty'] + "' value='" + contents['qty'] + "' class='form-control order_quantity validateNumbers' readonly='readonly' disabled='disabled'/><label class='labelorderQuantity hide_div'>test</label></td>";
                contents_html += "<td class='unit_td'></td>";
                contents_html += "<td class='cost_price_td'></td>";
                contents_html += "<td class='price_td'></td>";
                contents_html += "<td class='discount_td'></td>";
                contents_html += "<td class='sum_ex_td'></td>";
                contents_html += "<td class='dg_td'></td>";
                contents_html += "<td class='vat_td'></td>";
                contents_html += "<td class='delivery_date_td'><div><input type='text' class='delivery_date form-control' style='position: relative !important;'></div></td>"
                contents_html += "<td></td>"; //Retrun qty td
                contents_html += "<td class='warehouse_td'><select class='select2 warehouse' style='width:100% !important;'  onchange='getProductDetailsForMaterials(this,  \"1\", \"" + usertype + "\",\"" + user_warehouse_resposible + "\", );'>" + warehouse_options + "</select><label class='labelWarehouse hide_div'>test</label></td>";
                contents_html += "<td class='location_td'><select class='select2 location' style='width:100% !important;' onchange='showPickedQuantity(this, 1);'>" + location_options + "</select><label class='labelLocation hide_div'>test</label>";
                contents_html += "<td class='quantity_td'><input type='text' class='form-control quantity hide_div numberWithSingleComma' data-val=0 onchange='getSerialNumberForOrderMaterial(this, 1, false, false,false,1)'/><label class='labelQuantity hide_div'>test</label></td>";
                contents_html += "<td class='invoice_qty_td'><label class='ContentlabelQuantity hide_div'>test</label></td>";
                contents_html += "<td class='save_content_td' style='display:none;'><button type='button' class='btn btn-primary form-control save_product' onclick='savePackageOrderMaterial(this);'>" + save_text + "</button></td>";
                contents_html += "<td class='update_content_td' style='display:none;'><button type='button' class='btn btn-primary form-control update_product' onclick='updateOrderMaterialData(this);'>" + update_text + "</button></td>";
                contents_html += "<td></td><td class='info_td'><a class='stock_info_btn' type='button' onclick='showStockInfo(this);' unique_id='" + unique_id_info + "'><i class='fa fa-info-circle'></i></a></td><td></td></tr>";
                $("#order_material_Table tbody tr:first").after(contents_html);
            }
        }
    }
    setTimeout(function() {
        $(".select2").select2({
            closeOnSelect: true
        });
        $('.delivery_date').datetimepicker({
            format: 'DD.MM.YYYY',
            locale: 'en-gb'
        }).on("dp.change", function(e) {
            setContentDate($(this));
        });
        $('.product').trigger('change');
    }, 100);
    return false;
}

function updatePackagePrices(obj) {
    var selected_product = $(obj).val();
    $.ajax({
        type: "get",
        url: url + "/product/getProductDetailForOffer/" + selected_product,
        data: {
            '_token': token,
            'rest': 'true',
            'product_id': selected_product
        },
        success: function(response) {
            if (response) {
                $(obj).closest('tr').find('.stock-info-btn').show();
                var jsonresult = $.parseJSON(response);
                console.log(jsonresult)
                if (jsonresult['status'] == 'success') {
                    var units = $("#hidden_units").val();
                    units = $.parseJSON(units);
                    var unit_options = "<option value='Select'>" + js_select_text + "</option>";
                    var product_details = jsonresult['data'];
                    if (product_details['is_package'] == 0) {
                        $.each(units, function(index, value) {
                            if (product_details['unit'] == index) {
                                unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                            } else {
                                unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                            }
                        });
                    } else {
                        $(obj).closest('tr').find('.stock-info-btn').hide();
                        $.each(units, function(index, value) {
                            if (index == 2) {
                                unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                            } else {
                                unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                            }
                        });
                    }

                    var product_string = $(obj).find(":selected").text();
                    var product_string_array = product_string.split("-");
                    product_string_array.shift();
                    $(obj).closest('tr').find('.product_description').val(product_string_array.join("-"));


                    $(obj).closest('tr').find('.unit').html(unit_options);
                    var product_price = parseFloat(product_details['sale_price']);
                    product_price = replaceDot(product_price);
                    $(obj).closest('tr').find('.price').val(product_price);
                    $(obj).closest('tr').find('.cost_price').val(product_price);
                    var vat = parseFloat(product_details['tax']);
                    vat = vat.toFixed(2);
                    vat = replaceDot(vat);
                    $(obj).closest('tr').find('.vat').val(vat);
                    $(obj).closest('tr').find('.order_quantity ').trigger('change');
                }
            }
        },
        fail: function(response) {
            console.log("Something Went Wrong")
        }
    });
}

function savePackageOrderMaterial(obj) {
    var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
    $(elements).each(function() {
        if ($(this).find('#is_package').val() != undefined) {
            saveOrderMaterial($(this).find('.save_product'), '', 2);
        }
    });
}

function updatePackageProductsQuantity(obj) {
    var actual_quantity = replaceComma($(obj).val());
    if (actual_quantity <= 0) {
        showAlertMessage(quantity_alert_messge);
        $(obj).val(1).trigger('change');
        return false;
    }
    var package_product_quantity = 0;
    var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
    $(elements).each(function() {
        if ($(this).find('#is_package').val() == undefined) {
            package_product_quantity = replaceComma($(this).find('.order_quantity').attr('data-val'));
            package_product_quantity = package_product_quantity * actual_quantity;
            $(this).find('.order_quantity').val(replaceDot(package_product_quantity));
            if (replaceComma($(this).find('.quantity').val()) > 0) {
                $(this).find('.quantity').val('');
            }
        }
    });
    $(obj).closest("tr").find('.save_product').attr('save_val', 1);
}
/**
 * [savePacakgeProducts description]
 * @param  {[type]} obj         [description]
 * @param  {[type]} material_id [description]
 * @return {[type]}             [description]
 */
function savePacakgeProducts(obj, material_id) {
    var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
    $(elements).each(function() {
        if ($(this).find('#is_package').val() == undefined) {
            $(this).find('#reference_id').val(material_id);
            saveOrderMaterial($(this).find('.save_product'), material_id, 3);
        }
    });
    enableInvoiceQunatityForPackage(obj);
}
/**
 * [enableInvoiceQunatityForPackage description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function enableInvoiceQunatityForPackage(obj) {
    disablePackageOrderQuantity(obj);
    var enable_value = 1;
    var disable_value = 0;
    var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
    $(elements).each(function() {
        if ($(this).find('#is_package').val() == undefined) {
            if (replaceComma($(this).find('.order_quantity').val()) != replaceComma($(this).find('.quantity').val())) {
                enable_value = 0;
            }
            if (replaceComma($(this).find('.quantity').val()) > 0 || replaceComma($(this).find('.quantity').attr('data-val')) > 0) {
                disable_value = 1;
            }
        }
    });
    if (enable_value == 1) {
        $(elements).each(function() {
            if ($(this).find('#is_package').val() != undefined) {
                $(this).find('.product_invoice_div').show();
                $(this).find('.order_quantity').hide();
                $(this).find('.labelorderQuantity').html($(this).find('.order_quantity').val());
                $(this).find('.labelorderQuantity').removeClass('hide_div');
            }
        });
    }
    if (disable_value == 1) {
        $(elements).each(function() {
            if ($(this).find('#is_package').val() != undefined) {
                $(this).find('.order_quantity').hide();
                $(this).find('.labelorderQuantity').html($(this).find('.order_quantity').val());
                $(this).find('.labelorderQuantity').removeClass('hide_div');
            }
        });
    }
}
/**
 * [disablePackageOrderQuantity description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function disablePackageOrderQuantity(obj) {
    if (replaceComma($(obj).closest('tr').find('.quantity').val()) != undefined && replaceComma($(obj).closest('tr').find('.quantity').val()) != '') {
        var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
        $(elements).each(function() {
            if ($(this).find('#is_package').val() != undefined) {
                $(this).find('.order_quantity').hide();
                $(this).find('.labelorderQuantity').html($(this).find('.order_quantity').val());
                $(this).find('.labelorderQuantity').removeClass('hide_div');
            }
        });
    }
}

function updatePackageOrderMaterialData(obj) {
    var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
    $(elements).each(function() {
        if ($(this).find('#is_package').val() == undefined) {
            // var invoice_quantity = $(this).find('.ContentlabelQuantity').text();
            // if (invoice_quantity != 0) {
            //     if (replaceComma($(this).find('.quantity').val()) > 0) {
            updateOrderMaterialData($(this).find('.update_product'), 3);
            // }
            // }
        } else {
            updateOrderMaterialData($(this).find('.update_product'), 2);
        }
    });
}
/**
 * [updatePackageInvoiceQuantity description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function updatePackageInvoiceQuantity(obj) {
    var invoice_quantity = $(obj).val();
    var order_quantity = $(obj).closest('tr').find('.order_quantity').val();
    if (invoice_quantity == 0) {
        $(obj).val(1);
        showAlertMessage(qty_greater_than_0_text);
        return false;
    } else if (invoice_quantity > order_quantity) {
        showAlertMessage(package_invoice_qty_validation_msg); //Need to apply language
        $(obj).val(order_quantity);
        return false;
    }
}

function showStockInfo(obj) {
    var product_id = $(obj).closest('tr').find('.product_number').val();
    var warehouseStatus = 0;
    if ($(obj).closest('tr').find('.warehouse_td').find('.labelWarehouse').length && $(obj).closest('tr').find('.warehouse_td').find('.labelWarehouse').hasClass('hide_div')) {
        warehouseStatus = 1;
    }
    $.ajax({
        type: 'POST',
        url: stockUrl,
        data: {
            _token: token,
            'stock_id': product_id,
            'warehouse_id': '',
            'unique_id': $(obj).attr('unique_id'),
            'warehouseStatus': warehouseStatus
        },
        async: false,
        success: function(response) {
            if (response) {
                var decoded_data = $.parseJSON(response);
                $(".stockInfoModal").modal("show");
                $('#stockInfoModalContent').html(decoded_data['data']);
            }
            setTimeout($.unblockUI, 200);
        },
        error: function() {
            setTimeout($.unblockUI, 200);
        }
    });
}

function setContentDate(obj) {
    if (obj.closest('tr').find('#is_package').val() == 1) {
        var date = obj.val();
        var elements = $('tr[data-val="' + $(obj).closest('tr').attr('data-val') + '"]');
        $(elements).each(function() {
            if ($(this).find('#is_package').val() == undefined) {
                $(this).closest('tr').find('.delivery_date').val(date);
            }
        });
    }
}
$(document).on("click", ".setWarehouseandLocation", function() {
    var usertype = $("#hidden_usertype").val().trim();
    var location = $(this).attr('location-id')
    var user_warehouse_resposible = $("#hidden_user_warehouse_resposible").val();
    var unique_id = $(this).attr("unique_id");
    $('[unique_id="' + unique_id + '"]').closest('tr').find('.warehouse').attr('noaction', 1);
    $('[unique_id="' + unique_id + '"]').closest('tr').find('.warehouse').val($(this).attr("warehouse-id")).trigger('change');
    setTimeout(function() {
        $('[unique_id="' + unique_id + '"]').closest('tr').find('.warehouse').attr('noaction', 0);
        var type = 1;
        if ($('[unique_id="' + unique_id + '"]').closest('tr').attr('from-index') == 1) {
            type = 2;
        }
        getProductDetailsForMaterials($('[unique_id="' + unique_id + '"]').closest('tr').find('.warehouse'), type, usertype, user_warehouse_resposible, location);
        $(".stockInfoModal").modal("hide");
    }, 500)
});
$(document).on("click", "#picklist_btn", function() {
    $('#form_pick_list_warehouse').val($('#pick_list_warehouse').val());
    $('#form_pick_list_location').val($('#pick_list_location').val());
    $('#picklist-form').submit();
});
$(document).on("click", "#order_material_Table tbody tr", function() {
    if ($(this).closest('tr').find('#is_content').val() != 1 && $(this).closest('tr').find('.update_product').attr('data-val') != null && $(this).closest('tr').find('.update_product').attr('data-val') != '' && $(this).closest('tr').find('.update_product').attr('data-val') != undefined) {
        if ($(this).hasClass('bg-color-grey')) {
            $(this).removeClass('bg-color-grey')
        } else {
            $('#order_material_Table').find('tr').removeClass('bg-color-grey');
            $(this).addClass('bg-color-grey');
        }
    }
});
$(".add_new_text").click(function() {
    var usertype = $("#hidden_usertype").val().trim();
    var htmlString = "<tr class='order_material_tr'><td class='product_move'><i class='fa fa-arrows'></i></td>";
    htmlString += "<td class='approve_product_td'></td>";
    htmlString += "<td colspan='15'><input type='text' class='product_text form-control' style='position: relative !important;'></td>";
    htmlString += "<td class='save_content_td' style='display:none;'><button type='button' class='btn btn-primary form-control save_text' data-val='-1' onclick='saveText(this);'>" + save_text + "</button></td>"
    htmlString += "<td colspan='2'></td><td class='remove_td'><a type='button' onclick='removeOrderMaterial(this);'><i class='delete-icon fa fa-trash'></i></a></td></tr>";
    var reference_id = -1;
    $('#order_material_Table tr').each(function() {
        if ($(this).hasClass('bg-color-grey')) {
            reference_id = $(this).closest('tr');
        }
    });
    if (reference_id == -1) {
        $("#order_material_Table tbody").prepend(htmlString);
    } else {
        reference_id.after(htmlString);
    }
});

function saveText(obj) {
    displayBlockUI();
    var reference_id = -1;
    $('#order_material_Table tr').each(function() {
        if ($(this).hasClass('bg-color-grey')) {
            reference_id = $(this).closest('tr').find('.update_product').attr('data-val');
        }
    });
    $.ajax({
        type: "POST",
        url: url + "/storeText",
        data: {
            '_token': token,
            'rest': 'true',
            'id': $(obj).attr('data-val'),
            'order_id': $("#product_order_id").val(),
            'reference_id': reference_id,
            'text': $(obj).closest('tr').find('.product_text').val(),
            'sortorderval': $(obj).closest('tr').attr('sortorderval'),
        },
        success: function(response) {
            decoded_response = $.parseJSON(response);
            $(obj).attr('data-val', decoded_response['data']);
            $(obj).closest('tr').attr('material_id', decoded_response['data'])
            $.unblockUI();
        },
        fail: function() {
            $.unblockUI();
        }
    });
}

function updateSortOrder(obj, id) {
    displayBlockUI();
    $.ajax({
        type: "get",
        url: url + "/updateMaterialSortOrder/" + id + '/' + $(obj).closest('tr').attr('sortorderval'),
        data: {
            '_token': token,
            'rest': 'true',
        },
        success: function(response) {
            $.unblockUI();
        },
        fail: function() {
            $.unblockUI();
        }
    });
}
$(document).on("click", "#shippment", function() {
    $(".shippmentModal").modal("show");
});
$(document).on("change", ".volume", function() {
    if ($('#height').val() > 0 && $('#length').val() > 0 && $('#width').val() > 0) {
        height = ($('#height').val() / 10)
        height = parseFloat(height).toFixed(2);
        length = ($('#length').val() / 10)
        length = parseFloat(length).toFixed(2);
        width = ($('#width').val() / 10)
        width = parseFloat(width).toFixed(2);
        total = height * length * width
        total = parseFloat(total).toFixed(2);
        $('#volume').val(total)
    }
});
$(document).on("click", ".saveShipment", function() {
    obj = $(this);
    id = $(this).attr('id');
    $(this).closest('tr').find('.customerprice').attr("readonly", false);
    $(this).closest('tr').addClass('save')
    $('.saveShipment').each(function() {
        if (id != $(this).attr('id')) {
            $(this).closest('tr').find('.customerprice').attr("readonly", true);
            $(this).prop("checked", false);
        }
    });
});
$(document).on("click", "#getprices", function() {
    if ($('#volume').val() <= 0 || $('#weight').val() <= 0) {
        showAlertMessage("Check the inputs weight and volume are required", "error");
        return false;
    }
    $('#shippingTable tbody tr').remove();
    displayBlockUI();
    $.ajax({
        type: "POST",
        url: getPriceUrl,
        data: {
            '_token': token,
            'rest': 'true',
            'volume': $('#volume').val(),
            'weight': $('#weight').val(),
            'sender': $('#sender').val(),
            'order_id': order_id,
            'printer': $('#printer').val(),
            'type': 'offer'
        },
        success: function(response) {
            decoded_response = $.parseJSON(response);
            if (decoded_response.type == "success") {
                $("#shippingTable tbody").append(decoded_response.content);
            } else {
                showAlertMessage(decoded_response.message, 'error')
            }
            $.unblockUI();
        },
        fail: function() {
            $.unblockUI();
        }
    });
});
$(document).on("click", "#pickup", function() {
    $('#shippingTable tbody tr').remove();
    var htmlString = "<tr class='save' pickup='1'>"
    htmlString += '<td id="carrier_name" sender_id="null"  printer="null" identifier="Ingen">Ingen</td>';
    htmlString += '<td id="product_name" identifier="Hentes">Hentes</td>';
    htmlString += '<td><input name="list_price" class="numberWithSingleComma pickupPrice netprice form-control" value="" id="list_price"></td>';
    htmlString += '<td><input name="customerprice" class="numberWithSingleComma customerpickprice form-control" value="" id="customerprice_1"></td>';
    htmlString += "<td></td></tr>";
    $("#shippingTable tbody").prepend(htmlString);
});
$(document).on("change", "#sender, #printer", function() {
    $('#getprices').attr('disabled', 'disabled')
    var printer_val = $('#printer').val();
    var sender_val = $('#sender').val();
    if (printer_val != 'Select' && printer_val != 'velg' && printer_val != '' && sender_val != 'Select' && sender_val != 'velg' && sender_val != '') {
        $('#getprices').removeAttr('disabled')
    }
});
$(document).on("click", "#add_shipment", function() {
    var obj = $('#shippingTable tbody').find('.save').find('#product_name');
    if (obj.length > 0) {
        displayBlockUI();
        let payload = {};
        payload.customerprice = obj.closest('tr').find('.customerprice').val();
        payload.height = $('#height').val();
        payload.width = $('#width').val();
        payload.length = $('#length').val();
        payload.weight = $('#weight').val();
        payload.volume = $('#volume').val();
        payload.sender_id = obj.closest('tr').find('#carrier_name').attr('sender_id');
        payload.printer = obj.closest('tr').find('#carrier_name').attr('printer');
        payload.product_name = obj.closest('tr').find('#product_name').html();
        payload.product_identifier = obj.closest('tr').find('#product_name').attr('identifier');
        payload.carrier_name = obj.closest('tr').find('#carrier_name').html();
        payload.carrier_identifier = obj.closest('tr').find('#carrier_name').attr('identifier');
        if ($(obj).closest('tr').attr('pickup') == 1) {
            payload.customerprice = obj.closest('tr').find('.customerpickprice').val();
            payload.grossprice = obj.closest('tr').find('.pickupPrice').val();
            payload.netprice = obj.closest('tr').find('.pickupPrice').val();
            payload.estimatedcost = obj.closest('tr').find('.pickupPrice').val();
        } else {
            payload.customerprice = obj.closest('tr').find('.customerprice').val();
            payload.grossprice = obj.closest('tr').find('#amounTd').attr('grossprice');
            payload.netprice = obj.closest('tr').find('#amounTd').attr('netprice');
            payload.estimatedcost = obj.closest('tr').find('#estimatedcost').attr('estimatedcost');
        }
        payload.order_id = order_id;
        $(".shippmentModal").modal("hide");
        $.ajax({
            type: "POST",
            url: storeShippingUrl,
            data: {
                '_token': token,
                'rest': 'true',
                'shipmentData': payload,
                'type': '2',
            },
            success: function(response) {
                location.reload();
            },
            fail: function() {
                $.unblockUI();
            }
        });
    } else {
        showAlertMessage(select_product);
    }
});
/**
 * [hideOrShowPack description]
 * @return {[type]} [description]
 */
function hideOrShowPack() {
    displayBlockUI();
    $.ajax({
        type: "get",
        url: url + "/getPacklistBtnStatus/" + order_id,
        data: {
            '_token': token,
            'rest': 'true',
        },
        success: function(response) {
            decoded_response = $.parseJSON(response);
            console.log(decoded_response);
            $('#picklist').hide();
            $('#packlist').hide();
            $('#download_last_packlist').hide();
            if (decoded_response.data.picked_product >= 1) {
                $('#packlist').show();
                $('#packlist').attr('shipment', 1);
            }
            if (decoded_response.data.pick_product >= 1) {
                $('#picklist').show();
            }
            if (decoded_response.data.pack_list_history >= 1) {
                $('#download_last_packlist').show();
            }
            if (decoded_response.data.non_stockable_product >= 1) {
                $('#packlist').show();
                $('#packlist').attr('shipment', 0);
            }
            $.unblockUI();
        },
        fail: function() {
            $.unblockUI();
        }
    });
}