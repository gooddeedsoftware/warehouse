$(document).on("change", ".return_qty", function() {
    var avaliable_qty = replaceComma($(this).attr('avaliable_qty'));
    if (replaceComma($(this).val()) > avaliable_qty) {
        showAlertMessage(return_qty_alert_msg);
        $(this).val($(this).attr('avaliable_qty'));
    }
});

function getLocationsByWarehouse(obj, type) {
    $('#picklist_btn').attr('disabled', 'disabled');
    var seleted_warehouse = $(obj).val();
    var location_url = url + "/returnOrder/getlocationbywarehouse/" + seleted_warehouse;
    if (seleted_warehouse) { // if product is serial number required product
        $('#picklist_btn').removeAttr('disabled');
        $.ajax({
            type: "GET",
            url: location_url,
            asyc: true,
            data: {},
            success: function(response) {
                if (response) {
                    var jsonresult = $.parseJSON(response);
                    if (jsonresult['status'] == 'success') {
                        if (type == 1) {
                            setLocations(jsonresult['location_details'], obj);
                        } else {
                            $('.pick_list_location').html(jsonresult['location_details']);
                            $('.pick_list_location').val('').trigger('change');
                        }
                    }
                }
            },
            fail: function() {
                console.log("Something Went Wrong");
            }
        });
    } else {
    }
}

function setLocations(locations, obj) {
    try {
        $(obj).closest('tr').find('.material_location').val('').trigger('change');
        $(obj).closest('tr').find('.material_location').html('');
        $(obj).closest('tr').find('.material_location').html(locations);
        $.unblockUI();
    } catch (Exception) {
       $.unblockUI();
        console.log("Unexpected Error")
    }
}
$(document).on("change", ".select_material", function() {
    var sn_type = $(this).attr('sn_type');
    if (sn_type != 1) {
        var return_qty = $(this).closest('tr').find('.return_qty').val();
        if (return_qty == "" || return_qty == undefined || replaceComma(return_qty) == 0) {
            var avaliable_qty = $(this).closest('tr').find('.return_qty').attr('avaliable_qty');
            $(this).closest('tr').find('.return_qty').val(avaliable_qty);
        }
    }
});
$(document).on("change", ".select_all_materials", function() {
    $('.select_material:visible').prop('checked', this.checked);
    $('.select_material').trigger('change');
});
$(document).on("click", "#create_return_order_btn", function() {
    var material_array = [];
    var submit_val = 1;
    $.each($(".select_material:checked"), function() {
        var id = $(this).val();
        if ($(this).closest('tr').find('.material_warehouse').val() == "" || $(this).closest('tr').find('.material_warehouse').val() == "Select" || $(this).closest('tr').find('.material_warehouse').val() == undefined) {
            submit_val = 0;
            showAlertMessage(fill_warhouse);
            return false;
        }
        if ($(this).closest('tr').find('.material_location').val() == "" || $(this).closest('tr').find('.material_location').val() == "Select" || $(this).closest('tr').find('.material_location').val() == undefined) {
            submit_val = 0;
            showAlertMessage(fill_location);
            return false;
        }
        material_array.push({
            "id": id,
            "mateial_id": $(this).attr('material_id'),
            "warehouse": $(this).closest('tr').find('.material_warehouse').val(),
            "location": $(this).closest('tr').find('.material_location').val(),
            "return_qty": replaceComma($(this).closest('tr').find('.return_qty').val()),
            "sn_requierd": $(this).attr('sn_type'),
            "inventory_id": $(this).attr('inventory_id'),
            "product_id": $(this).attr('product_id'),
            "serial_number": $(this).attr('serial_number'),
        });
    });
    if (submit_val == 1) {
        if (material_array.length > 0) {
            var confirm_msg = confirm(return_order_create_confirm_msg);
            if (confirm_msg) {
                displayBlockUI();
                var decoded_material_array = JSON.stringify(material_array);
                $('#selected_materials').val(decoded_material_array);
                $("#return_order_form").submit();
            }
        } else {
            showAlertMessage(select_atleat_one);
        }
    }
});