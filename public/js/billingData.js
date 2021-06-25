$('.invoice_quantity').trigger('blur');
$('.material_class_1').each(function() {
    $(this).find('.description').attr('disabled', 'disabled');
    $(this).find('.unit').attr('disabled', 'disabled');
    $(this).find('.sale_price').attr('disabled', 'disabled');
    $(this).find('.invoice_quantity').attr('disabled', 'disabled');
    $(this).find('.discount').attr('disabled', 'disabled');
    $(this).find('.vat').attr('disabled', 'disabled');
    var current_val = $(this).find('.ex_vat').val();
    current_val = current_val.toString().replace(",", ".");
    invoiced_hours = parseFloat(invoiced_hours) + parseFloat(current_val);
});
invoiced_hours = invoiced_hours.toFixed(2);
invoiced_hours = replaceSpace(invoiced_hours);
invoiced_hours = invoiced_hours.toString().replace(".", ",");
$('#invoiced_ex_vat').text(invoiced_hours);
$('#materials_tab').click(function() {
    window.localStorage.removeItem('billing_data_state');
    window.location.href = $(this).attr('data-href');
});

function calculateMVAValue(obj) {
    var hours = 0;
    var hours = $(obj).closest('tr').find('.invoice_quantity').val();
    var price = $(obj).closest('tr').find('.sale_price').val();
    var discount = $(obj).closest('tr').find('.discount').val();
    discount = discount.toString().replace(",", ".");
    if (discount > 100) {
        $(obj).closest('tr').find('.discount').val('100');
        calculateMVAValue(obj);
        return false;
    }
    hours = hours.toString().replace(",", ".");
    price = price.toString().replace(",", ".");
    var calculate_total_val = (hours * price) - (hours * price * discount) / 100;
    calculate_total_val = calculate_total_val.toFixed(2);
    hours = replaceSpace(hours);
    $(obj).closest('tr').find('.ex_vat').val(calculate_total_val.toString().replace(".", ","));
    calculateTotalPageValue();
}

function calculateTotalPageValue() {
    var hours = 0
    $('.ex_vat').each(function() {
        var current_val = $(this).val();
        current_val = current_val.toString().replace(",", ".");
        hours = parseFloat(hours) + parseFloat(current_val);
    });
    hours = hours.toFixed(2);
    hours = replaceSpace(hours);
    hours = hours.toString().replace(".", ",");
    $('#billing_ex_vat').text(hours);
}

function replaceSpace(num) {
    if (num !== '' && typeof num !== 'undefined') {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ')
    } else {
        return num
    }
}
$('#save_all_billing_data').click(function() {
    $('.save_billing_data').each(function() {
        if ($(this).attr('data-invoiced') != 1) {
            var ordermaterial_id = $(this).attr('data-id');
            var description = $(this).closest('tr').find('.description').val();
            var unit = $(this).closest('tr').find('.unit').val();
            var sale_price = $(this).closest('tr').find('.sale_price').val();
            var invoice_quantity = $(this).closest('tr').find('.invoice_quantity').val();
            var discount = $(this).closest('tr').find('.discount').val();
            var vat = $(this).closest('tr').find('.vat').val();
            var product_number = $(this).closest('tr').find('.product_number').val();
            $.ajax({
                type: "post",
                url: url + "/storeBilllingData",
                data: {
                    '_token': token,
                    'rest': 'true',
                    'ordermaterial_id': ordermaterial_id,
                    'product_number': product_number,
                    'description': description,
                    'unit': unit,
                    'sale_price': sale_price,
                    'invoice_quantity': invoice_quantity,
                    'discount': discount,
                    'vat': vat,
                    'order_id': order_id,
                },
                async: false,
                success: function(response) {
                    if (response) {
                        console.log(response, "response");
                    }
                },
                fail: function(response) {
                    console.log("Something Went Wrong")
                }
            });
        }
    });
    $('#updated_user').text(user_name);
    $('#updated_time').text(date);
    new PNotify({
        title: message,
        text: billing_data_store_msg,
        type: "success",
        delay: 2500,
    });
});
$(document).on("click", "#send_to_uni", function() {
    var material_array = [];
    $.each($(".approve_product:checked"), function() {
        material_array.push({
            "id": $(this).attr("value"),
        });
    });
    if (material_array.length > 0) {
        $('#send_to_uni_form').find('#materials').val(JSON.stringify(material_array));
        $('#send_to_uni_form').submit();
    } else {
        showAlertMessage(select_atleat_one);
    }
});
$("#select_all_products").click(function() {
    $('.approve_product:visible').prop('checked', this.checked);
    $('.approve_product:visible').trigger('change');
});
$(document).on("click", "#update_invoice_number", function() {
    $(".invoiceModal").modal("show");
});
$(document).on("click", "#save_number", function() {
    if ($('#invoice_number').val()) {
        $.ajax({
            type: "post",
            url: url + "/storeInvoiceNumber",
            data: {
                '_token': token,
                'rest': 'true',
                'invoice_number': $('#invoice_number').val(),
                'order_id': order_id,
            },
            async: false,
            success: function(response) {
                $(".invoiceModal").modal("hide");
                $('#invoice_number_label').text($('#invoice_number').val());
                showAlertMessage(invoice_no_added, 'success');
            },
            fail: function(response) {
                $(".invoiceModal").modal("hide");
                console.log("Something Went Wrong")
            }
        });
    }
});