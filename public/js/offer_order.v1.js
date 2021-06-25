$("form :input").change(function() {
    $(this).closest('form').data('changed', true);
});
/**
 * Onclick event add product
 */
$(document).on("click", ".addProducts", function() {
    var row_count = parseInt($('#row_count').val()) + 1;
    if (row_count == 1) { //To construct the product row with label
        constructProductRow(row_count, 1);
    } else { //To construct the product row without label
        constructProductRow(row_count, 2);
    }
    $('#row_count').val(row_count);
});
$(document).on("click", ".add_next_text", function() {
    var row_count = parseInt($('#row_count').val()) + 1;
    $('#row_count').val(row_count);
    var contents_html = "";
    contents_html += "<div class='row product_row_" + row_count + " offer_product' data-val='" + row_count + "' id='disableDiv'>";
    contents_html += "<div class='col-sm-10 col-md-11'><label style='display:none;' class='productlabel'>product</label><br>"
    contents_html += "<input type='hidden' name='is_text_" + row_count + "'  value='1'><input type='text' name='text_" + row_count + "'  id='text_val_" + row_count + "' class='form-control'/></div>";
    contents_html += "<div class='col-sm-2  col-md-1 col-md-delete'><br><a class='btn' data-val='" + row_count + "' Onclick='deleteProductRow(this);'><i class='fa fa-minus'></i></a></div></div>";
    $("#product_row").append(contents_html);
});

function constructProductRow(row_count, type) {
    var product_options = '';
    var discount = $('#product_discount').val();
    product_options = getProductOption();
    var contents_html = "";
    contents_html += "<div class='row product_row_" + row_count + " offer_product' data-val='" + row_count + "' id='disableDiv'>";
    //Prooduct
    contents_html += "<div class='col-sm-6 col-md-4 cus-md-product'>";
    contents_html += "<input type='hidden' name='save_val_" + row_count + "' id='save_val_" + row_count + "' value='1' class='form-control save_val'/>";
    if (type == 1) {
        contents_html += "<input type='hidden' name='lable_val' id='lable_val_" + row_count + "' value='1' class='form-control lable_val'/>";
        contents_html += "<label>" + product + "</label>";
    } else {
        contents_html += "<input type='hidden' name='lable_val' id='lable_val_" + row_count + "' value='0' class='form-control lable_val'/>";
        contents_html += "<label style='display:none;' class='productlabel'>" + product + "</label><br>";
    }
    contents_html += "<select class='select2 product' data-val='" + row_count + "' id='product_" + row_count + "' onchange='loadProductDetails(this);' name='product_" + row_count + "'>" + product_options + "'</select></div>";
    //Qty
    contents_html += "<div class='col-sm-3 col-md-3 col-md-qty'>";
    if (type == 1) {
        contents_html += "<label>" + qty + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + qty + "</label><br>";
    }
    contents_html += "<input type='tel' data-val=" + row_count + " name='qty_" + row_count + "' id='qty_" + row_count + "' maxlength='5' class='form-control text-align-right qty validateNumbers width-75p'></div>";
    //Unit
    contents_html += "<div class='col-sm-3 col-md-3 col-md-unit'>";
    if (type == 1) {
        contents_html += "<label>" + unit + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + unit + "</label><br>";
    }
    contents_html += "<select class='form-control width-110p' id='unit_" + row_count + "' name='unit_" + row_count + "'><option selected='selected' value=''>Select</option></select></div>";
    //price
    contents_html += "<div class='col-sm-4 col-md-3 col-md-price'>";
    if (type == 1) {
        contents_html += "<label>" + price + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + price + "</label><br>";
    }
    contents_html += "<input type='tel' data-val=" + row_count + " onchange='calcuateTotal(this);' name='price_" + row_count + "' id='price_" + row_count + "' maxlength='10' class='text-align-right  form-control price numberWithSingleComma width-120p'></div>";
    //discount
    contents_html += "<div class='col-sm-4 col-md-3 col-md-discount'>";
    if (type == 1) {
        contents_html += "<label>" + discount_text + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + discount_text + "</label><br>";
    }
    contents_html += "<input type='tel' data-val=" + row_count + "  name='discount_" + row_count + "' id='discount_" + row_count + "'  maxlength='10' class='form-control text-align-right  discount numberWithSingleComma width-120p' value='" + discount + "'></div>";
    //sum_ex_vat
    contents_html += "<div class='col-sm-4 col-md-3 cus-md-sum_ex_vat'>";
    if (type == 1) {
        contents_html += "<label>" + sum_ex_vat + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + sum_ex_vat + "</label><br>";
    }
    contents_html += "<input type='tel' name='sum_ex_vat_" + row_count + "' id='sum_ex_vat_" + row_count + "'  maxlength='15' class='form-control text-align-right  sum_ex_vat numberWithSingleComma width-80p'></div>";
    //Vat
    contents_html += "<div class='col-sm-4 col-md-3 col-md-vat'>";
    if (type == 1) {
        contents_html += "<label>" + vat + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + vat + "</label><br>";
    }
    contents_html += "<input type='tel' data-val='" + row_count + "' name='vat_" + row_count + "' id='vat_" + row_count + "'  maxlength='10' class='text-align-right form-control vat numberWithSingleComma'></div>";
    //hidden fields
    contents_html += "<input type='hidden' name='product_text_" + row_count + "' id='product_text_" + row_count + "' class='form-control product_text'/>";
    contents_html += "<div class='col-sm-4 col-md-3 cus-md-delivery_date'>";
    if (type == 1) {
        contents_html += "<label>" + delivery_date + "</label>";
    } else {
        contents_html += "<label style='display:none;' class='productlabel'>" + vat + "</label><br>";
    }
    contents_html += "<input type='text' data-val='" + row_count + "' name='delivery_date_" + row_count + "' id=delivery_date_" + row_count + "' class='form-control delivery_date'></div>";
    //hidden fields
    contents_html += "<input type='hidden' name='product_text_" + row_count + "' id='product_text_" + row_count + "' class='form-control product_text'/>";
    //Delete Option
    contents_html += "<div class='col-sm-3 col-md-1 col-md-delete'>";
    if (type == 1) {
        contents_html += "<label style='visibility:  hidden;'>DeleteDeleteDeleteDelete</label>";
    } else {
        contents_html += "<br>";
    }
    contents_html += "<a type='button' data-val='" + row_count + "' Onclick='deleteProductRow(this);'><i class='fa fa-minus'></i></a>&nbsp</div>";
    //info option
    contents_html += "<div class='col-sm-1 col-md-1 col-md-info'>";
    if (type == 1) {
        contents_html += "<label style='visibility:  hidden;'>DeleteDeleteDeleteDelete</label>";
    } else {
        contents_html += "<br>";
    }
    var unique_id = createUUID();
    contents_html += "<a data-val='" + row_count + "' type='button' onclick='showStockInformation(this);' unique_id=" + unique_id + "><i class='fa fa-info-circle'></i></a></div>";
    setTimeout(function() {
        $(".select2").select2();
        $('.delivery_date').datetimepicker({
            format: 'DD.MM.YYYY',
            locale: 'en-gb'
        });
    }, 100);
    $("#product_row").append(contents_html);
}

function showStockInformation(obj) {
    var product_id = $(obj).closest('.row').find('.product').val();
    var warehouseStatus =0;
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
/**
 * [deleteProductRow description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function deleteProductRow(obj) {
    var div_data_val = $(obj).attr('data-val');
    confirm_msg = confirm(confirm_delete);
    if (confirm_msg) {
        var lable_val = $('#lable_val_' + div_data_val).val();
        var next_product_row_val = $('.product_row_' + div_data_val).next().attr('data-val');
        $('.br_' + div_data_val).remove();
        $('.product_row_' + div_data_val).remove();
        var div_count = $('.order_offer_product').length;
        if (div_count == 0) {
            $('#row_count').val(0);
            setEmptyValues();
        } else {
            if (lable_val == 1) {
                $('#lable_val_' + next_product_row_val).val(1);
                $('.product_row_' + next_product_row_val).find('.productlabel').removeAttr('style');
            }
            setTimeout(function() {
                $('.sum_ex_vat').trigger('change');
            }, 100);
        }
    }
}
/**
 * [getProductOption description]
 * @return {[type]} [description]
 */
function getProductOption() {
    var products = $("#hidden_products").val();
    var products = $.parseJSON(products);
    var product_options = "<option value='Select'>Select</option>";
    $.each(products, function(index, value) {
        product_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
    });
    return product_options;
}
/**
 * [loadProductDetails description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function loadProductDetails(obj) {
    try {
        var product_id = $(obj).val();
        var row_count_val = $(obj).attr('data-val');
        $.ajax({
            type: "get",
            url: url + "/product/getProductDetailForOffer/" + product_id,
            data: {
                '_token': token,
                'rest': 'true',
                'product_id': product_id
            },
            success: function(response) {
                if (response) {
                    var jsonresult = $.parseJSON(response);
                    if (jsonresult['status'] == 'success') {
                        if (jsonresult['data']['sn_required'] == 1 || jsonresult['data']['is_package'] == 1) {
                            $('#qty_' + row_count_val).addClass('validateNumbers');
                            $('#qty_' + row_count_val).removeClass('numberWithSingleComma');
                        } else {
                            $('#qty_' + row_count_val).addClass('numberWithSingleComma');
                            $('#qty_' + row_count_val).removeClass('validateNumbers');
                        }
                        setProductDetails(jsonresult['data'], row_count_val);
                    }
                }
            },
            fail: function(response) {
                console.log("Something Went Wrong")
            }
        });
    } catch (Exception) {
        console.log("Unexpected error ");
    }
}
/**
 * [setProductDetails description]
 * @param {[type]} product_details [description]
 */
function setProductDetails(product_details, row_count_val) {
    try {
        //Setting the unit
        var units = $("#hidden_units").val();
        var units = $.parseJSON(units);
        var unit_options = "<option value='Select'>Select</option>";
        if (product_details['is_package'] == 0) {
            $.each(units, function(index, value) {
                if (product_details['unit'] == index) {
                    unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                } else {
                    unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                }
            });
        } else {
            $.each(units, function(index, value) {
                if (index == 2) {
                    unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                } else {
                    unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                }
            });
        }
        $('#unit_' + row_count_val).html(unit_options);
        //Setting the price'
        var product_price = parseFloat(product_details['sale_price']);
        product_price = product_price.toFixed(2);
        product_price = replaceDot(product_price);
        $('#price_' + row_count_val).val(product_price);
        $('#price_' + row_count_val).trigger('change');
        //Setting the sn required value
        $('#sn_required_' + row_count_val).val(product_details['sn_required']);
        // Setting the Product text
        var product_text = product_details['product_number'] + '-' + product_details['description'];
        $('#product_text_' + row_count_val).val(product_text);
        //Setting the vat
        var vat = parseFloat(product_details['tax']);
        vat = vat.toFixed(2);
        vat = replaceDot(vat);
        $('#vat_' + row_count_val).val(vat);
        $('#vat_' + row_count_val).trigger('change');
    } catch (Exception) {
        console.log("Unexpected error");
    }
}
/**
 * [description]
 * @param  {[type]} ) { calcuateTotal($(this));} [description]
 * @return {[type]}   [description]
 */
$(document).on("change", ".qty, .price, .discount", function() {
    calcuateTotal($(this));
});

function calcuateTotal(obj) {
    try {
        //Calucating Summ ex vat
        var total_val = 0;
        var row_count_val = $(obj).attr('data-val');
        var qty = $('#qty_' + row_count_val).val();
        var price = $('#price_' + row_count_val).val();
        price = replaceComma(price);
        qty = replaceComma(qty);
        var discount = $('#discount_' + row_count_val).val();
        if (discount) {
            discount = replaceComma(discount);
        } else {
            discount = 0;
        }
        if (price && qty) {
            if (discount) {
                total_val = (parseFloat(qty) * parseFloat(price)) - (parseFloat(qty) * parseFloat(price) * parseFloat(discount)) / 100;
            } else {
                total_val = parseFloat(qty) * parseFloat(price);
            }
            total_val = total_val.toFixed(2);
            total_val = replaceDot(total_val);
            $('#sum_ex_vat_' + row_count_val).val(total_val);
        } else {
            $('#sum_ex_vat_' + row_count_val).val('');
        }
        $('.sum_ex_vat').trigger('change');
        $('.vat').trigger('change');
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}
/**
 * [description]
 * @param  {[type]} ) { calcuateSum($(this));} [description]
 * @return {[type]}   [description]
 */
$(document).on("change", ".sum_ex_vat", function() {
    calcuateSum();
    $('.vat').trigger('change');
});
/**
 * [calcuateSum description]
 * @return {[type]} [description]
 */
function calcuateSum() {
    try {
        var total_sum = 0;
        $('.sum_ex_vat').each(function() {
            var sum = 0;
            sum = $(this).val();
            if (sum) {
                sum = replaceComma(sum);
                total_sum = parseFloat(total_sum) + parseFloat(sum);
            }
        });
        if (total_sum == 0) {
            $('#sum').val(total_sum);
            $('#sum_label').text('');
        } else {
            total_sum = total_sum.toFixed(2);
            $('#sum').val(total_sum);
            total_sum = replaceDot(total_sum);
            $('#sum_label').text(total_sum);
        }
        calcuateInvoiceTotal();
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}
/**
 * [description]
 * @param  {[type]} ) { }         [description]
 * @return {[type]}   [description]
 */
$(document).on("change", ".vat", function() {
    calcuateMVA();
});
/**
 * [calcuateMVA description]
 * @return {[type]} [description]
 */
function calcuateMVA() {
    try {
        var mva = 0;
        $('.vat').each(function() {
            var vat_val = $(this).val();
            if (vat_val) {
                var row_count_val = $(this).attr('data-val');
                var sum_ex_vat = $('#sum_ex_vat_' + row_count_val).val();
                if (sum_ex_vat) {
                    vat_val = replaceComma(vat_val);
                    sum_ex_vat = replaceComma(sum_ex_vat);
                    mva = parseFloat(mva) + ((parseFloat(vat_val) * parseFloat(sum_ex_vat)) / 100);
                }
            }
        });
        if (mva == 0) {
            $('#mva').val(mva);
            $('#mva_label').text('');
        } else {
            mva = mva.toFixed(2);
            $('#mva').val(mva);
            mva = replaceDot(mva);
            $('#mva_label').text(mva);
        }
        calcuateInvoiceTotal();
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}

function calcuateInvoiceTotal() {
    try {
        var sum = 0;
        var mva = 0;
        var invoice_total = 0;
        sum = $('#sum').val();
        mva = $('#mva').val();
        sum = replaceComma(sum);
        mva = replaceComma(mva);
        invoice_total = parseFloat(sum) + parseFloat(mva);
        invoice_total = invoice_total.toFixed(2);
        var splited_invoice_total = invoice_total.split('.');
        var final_invoice_total = splited_invoice_total[0];
        final_invoice_total = parseFloat(final_invoice_total);
        final_invoice_total = final_invoice_total.toFixed(2);
        $('#invoice_total').val(final_invoice_total);
        final_invoice_total = replaceDot(final_invoice_total);
        $('#invoice_total_lable').text(final_invoice_total);
        var round_down = '.' + splited_invoice_total[1];
        round_down = parseFloat(round_down);
        round_down = round_down.toFixed(2);
        $('#round_down').val(round_down);
        round_down = replaceDot(round_down);
        $('#round_down_lable').text(round_down);
    } catch (Exception) {
        console.log("Unexpected Error in calcuateInvoiceTotal")
    }
}
/**
 * [setEmptyValues description]
 */
function setEmptyValues() {
    try {
        $('#invoice_total_lable').text('');
        $('#round_down_lable').text('');
        $('#sum_label').text('');
        $('#mva_label').text('');
        $('#sum').val(0);
        $('#invoice_total').val(0);
        $('#round_down').val(0);
        $('#mva').val(0);
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}
// Validate Numbers
$(document).on("keypress", ".validateNumbers", function(e) {
    validateNumbers(e);
});
/**
 * [validateNumbers description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function validateNumbers(obj) {
    var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!obj.charCode ? obj.which : obj.charCode);
    if (regex.test(str)) {
        return true;
    } else {
        obj.preventDefault();
        return false;
    }
}
//Added by david-To allow only one comma in price fields
$(document).on('keypress keyup blur', '.numberWithSingleComma', function(event) {
    var regex = new RegExp("^[0-9,]+$");
    var str = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (regex.test(str)) {} else {
        event.preventDefault();
        return false;
    }
    //this.value = this.value.replace(/[^0-9\.]/g,'');
    $(this).val($(this).val().replace(/[^0-9\,]/g, ''));
    if ((event.which != 46 && event.which == 188 || $(this).val().indexOf('.') != -1 && (event.which < 48 || event.which > 57) || $(this).val().indexOf(',') != -1) && event.which != 188 && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});
//Sending the offer order mail
$(".offer_email_button").click(function() {
    if ($('#orderform').data('changed')) {
        $('#mail_update_button').trigger('click');
        return false;
    } else {
        sendOfferOrderMail($(this).attr('dataval'));
    }
});
$("#mail_update_yes_btn").click(function() {
    $('#update_mail_btn_val').val(1);
    $('.order_update_btn').trigger('click');
});
$("#mail_update_no_btn").click(function() {
    sendOfferOrderMail($(this).attr('dataval'));
});
/**
 * [sendOfferOrderMail description]
 * @return {[type]} [description]
 */
function sendOfferOrderMail(order_id) {
    try {
        $("#order_id_for_mail").val(order_id);
        $("#order_status_hidden").val($('.order_status').val());
        $("#send_offer_order_mail_form").submit();
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}