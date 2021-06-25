var height = $(window).height();
var width = $(window).width();
if (width <= 700 || height <= 700) {
    $(".datepicker").attr('readonly', true);
}
/**** signature-pad for customer sign ****/
var wrapper1 = document.getElementById("customer-signature-pad");
var signaturePad1, resizeCanvas, width, height;
var customer_sign_canvas;
if (wrapper1) {
    customer_sign_canvas = wrapper1.querySelector("canvas");
    signaturePad1 = new SignaturePad(customer_sign_canvas);
    window.onresize = resizeCanvas;
    resizeCanvas1(signaturePad1);
}
// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas1(signaturePad1) {
    //signaturePad1.clear();
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio = Math.max(window.devicePixelRatio || 1, 1);
    width = customer_sign_canvas.offsetWidth;
    height = customer_sign_canvas.offsetHeight;
    if (width < 1) {
        width = 500;
        height = 250;
    }
    customer_sign_canvas.width = width * ratio;
    customer_sign_canvas.height = height * ratio;
    customer_sign_canvas.getContext("2d").scale(ratio, ratio);
    if ($(".customer_signature").val()) signaturePad1.fromDataURL($(".customer_signature").val());
}
$('#customer-signature-pad').on('shown.bs.modal', function(e) {
    resizeCanvas1(signaturePad1);
});
$('#custsign_clear').on('click', function() {
    signaturePad1.clear();
});
$('#custsign_save').on('click', function() {
    if (signaturePad1.isEmpty()) {
        alert("Please fill the signature!");
    } else {
        $(".customer_signature").val(signaturePad1.toDataURL());
        var cust_sign = $("#customer_signature").val();
        if ($(".customerSignature").length > 0) {
            document.getElementById("customerSignature").src = cust_sign;
        } else {
            var img = $('<img id="customerSignature" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">');
            img.attr('src', cust_sign);
            img.appendTo('#custsigndiv');
        }
        $('#customer-signature-pad').modal('toggle');
    }
});
$('#order_date').datetimepicker({
    format: 'DD.MM.YYYY',
    locale: 'en-gb'
});
$('#date_completed, #offer_due_date').datetimepicker({
    format: 'DD.MM.YYYY',
    locale: 'en-gb'
});
$("#customer_id").change(function() {
    $(".contact_quick_create").hide();
    var customer_id = $(this).val();
    $('.deliveraddress1').val('');
    $('.deliveraddress2').val('');
    $('.deliveraddress_zip').val('');
    $('.deliveraddress_city').val('');
    $('.visitingAddress1').val('');
    $('.visitingAddress2').val('');
    $('.visitingAddressZip').val('');
    $('.visitingAddressCity').val('');
    $("#equipment_id").val("");
    $("#contact_person").html("").trigger('change');
    if (customer_id) {
        $(".contact_quick_create").show();
        getContactPersonAndEquipment(customer_id, order_contact_person_route, token, '');
        getCustomerAddress(customer_id, order_contact_person_route, token);
    }
    $("#invoice_customer").val(customer_id).trigger("change");
});
$("#invoice_customer").change(function() {
    $(".order_by_quick_create").hide();
    var customer_id = $(this).val();
    $("#ordered_by").html("");
    if (customer_id) {
        $(".order_by_quick_create").show();
        getCustomerEmailAndUsers(customer_id, order_contact_person_route, token, '');
    }
});
$("#department_id").change(function() {
    getAssignedBy(user_department_route, token);
});
$("#contact_submit").click(function() {
    var type = $(this).attr('submit_type');
    if (type == 2) {
        storeContactPerson(url, token, $("#invoice_customer").val(), order_contact_person_route, type);
    } else {
        storeContactPerson(url, token, $("#customer_id").val(), order_contact_person_route, type);
    }
});
$(".order_submit_btn").click(function(event) {
    $('#order_submit_btn_hidden').val($(this).val());
});
$("#orderform").submit(function(event) {
    if ($('.order_submit_btn').hasClass('disabled')) {
        return false;
    }
    $('.order_submit_btn').attr('disabled', 'disabled');
    displayBlockUI();
});
// get email and user for invoices customer
function getCustomerEmailAndUsers(customer_id, url, token, current_id) {
    $("#ordered_by").html("");
    $.post(url, {
        _token: token,
        "customer_id": customer_id,
    }, function(data) {
        if (data) {
            var parse_data = JSON.parse(data);
            if (parse_data) {
                $("#ordered_by").html(parse_data['contact_person']);
                if (current_id) {
                    $('#ordered_by').val(current_id).trigger('change');
                }
            }
        }
    });
}
// get contact persons and equipment for selected customer
function getContactPersonAndEquipment(customer_id, url, token, current_id) {
    $("#contact_person").html("").trigger('change');
    $.post(url, {
        _token: token,
        "customer_id": customer_id,
    }, function(data) {
        if (data) {
            var parse_data = JSON.parse(data);
            if (parse_data) {
                $('.pmt_term').val(parse_data.customer_detail.pmt_terms).trigger('change');
                $("#contact_person").html(parse_data['contact_options_with_mobile']);
                if ($("#equipment_id").val() == "") {
                    $("#equipment_id").html(parse_data['equipments']);
                    $('#equipment_id').select2('val', null);
                }
                if (current_id) {
                    $('#contact_person').val(current_id).trigger('change');
                }
            }
        }
    });
}
//get assigned by
function getAssignedBy(url, token, user_id) {
    var department_id = $("#department_id").val();
    $("#order_users").html('');
    $("#order_users").val('').trigger('change');
    if (department_id.length > 0) {
        $.post(url, {
            _token: token,
            "department_id": department_id,
        }, function(data) {
            if (data) {
                try {
                    var parse_data = JSON.parse(data);
                    $("#order_users").html(parse_data['all_users']);
                } catch (Exception) {}
            }
        });
    }
}
$(".addnew_contact_btn").click(function() {
    if ($(this).attr('value') == 1) {
        $('#contact_submit').attr('customer_id', $('#customer_id').val());
        $('#contact_submit').attr('submit_type', $(this).attr('value'));
    } else {
        $('#contact_submit').attr('customer_id', $('#invoice_customer').val());
        $('#contact_submit').attr('submit_type', $(this).attr('value'));
    }
});
// contact person submit(contact person modal)
function storeContactPerson(url, token, customer_id, order_contact_person_route, type) {
    $.ajax({
        type: "POST",
        url: url + "/customer/contact_inline_store",
        data: {
            '_token': token,
            'rest': 'true',
            'customer_id': customer_id,
            'name': $("#contact_name").val(),
            'email': $("#contact_email").val(),
            'mobile': $("#contact_mobile").val(),
            'phone': $("#contact_phone").val()
        },
        async: false,
        success: function(data) {
            var jsonresult = $.parseJSON(data);
            console.log(jsonresult['id'], "jsonresult")
            if (jsonresult['result'] == 'success') {
                $("#addnew_contact").modal("hide");
                $('#contact-form')[0].reset();
                $("#addnew_contact").removeData('bs.modal');
                if (type == 1) {
                    getContactPersonAndEquipment(customer_id, order_contact_person_route, token, jsonresult.id);
                } else {
                    getCustomerEmailAndUsers(customer_id, order_contact_person_route, token, jsonresult.id);
                }
            }
        },
        error: function(data) {
            var response = data.responseJSON;
            var errors = response.errors;
            $.each(errors, function(index, value) {
                new PNotify({
                    title: message_text,
                    text: value[0],
                    type: "error",
                    delay: 1500,
                });
            });
        }
    });
}
/**
 * [getCustomerAddress description]
 * @param  {[type]} customer_id [description]
 * @param  {[type]} url         [description]
 * @param  {[type]} token       [description]
 * @return {[type]}             [description]
 */
function getCustomerAddress(customer_id, url, token, new_created_id) {
    try {
        $.post(url, {
            _token: token,
            "customer_id": customer_id,
            "contact_person_id": ''
        }, function(data) {
            if (data) {
                var parse_data = JSON.parse(data);
                if (parse_data) {
                    $("#deliveraddress").html(parse_data['customer_address_options']);
                    $("#visitingAddress").html(parse_data['customer_address_options']);
                    setTimeout(function() {
                        if ($("#deliveraddress option").length == 2) {
                            $("#deliveraddress").val($("#deliveraddress option:last").val());
                            $("#visitingAddress").val($("#visitingAddress option:last").val());
                        }
                        $("#deliveraddress").trigger('change');
                        $("#visitingAddress").trigger('change');
                    }, 100);
                }
            } else {
                $("#deliveraddress").html("");
            }
        });
    } catch (Exception) {
        console.log("Unexpected Error")
    }
}
/**
 * [description]
 * @param  {[type]} ) { alert("herer");} [description]
 * @return {[type]}   [description]
 */
$("#deliveraddress").change(function() {
    var deliveraddress_id = $(this).val();
    $('.deliveraddress1').val('');
    $('.deliveraddress2').val('');
    $('.deliveraddress_zip').val('');
    $('.deliveraddress_city').val('');
    if (deliveraddress_id) {
        $.ajax({
            type: "GEt",
            url: url + "/orders/getDeliverAddressDetails/" + deliveraddress_id,
            data: {
                '_token': token,
                'rest': 'true',
            },
            async: false,
            success: function(response) {
                if (response) {
                    var decoded_response = $.parseJSON(response);
                    if (decoded_response['result'] == SUCCESS) {
                        $('.deliveraddress1').val(decoded_response['data']['address1']);
                        $('.deliveraddress2').val(decoded_response['data']['address2']);
                        $('.deliveraddress_zip').val(decoded_response['data']['zip']);
                        $('.deliveraddress_city').val(decoded_response['data']['city']);
                    }
                }
            }
        });
    }
});
$("#visitingAddress").change(function() {
    var visitingAddressId = $(this).val();
    $('.visitingAddress1').val('');
    $('.visitingAddress2').val('');
    $('.visitingAddressZip').val('');
    $('.visitingAddressCity').val('');
    if (visitingAddressId) {
        $.ajax({
            type: "GEt",
            url: url + "/orders/getDeliverAddressDetails/" + visitingAddressId,
            data: {
                '_token': token,
                'rest': 'true',
            },
            async: false,
            success: function(response) {
                if (response) {
                    var decoded_response = $.parseJSON(response);
                    if (decoded_response['result'] == SUCCESS) {
                        $('.visitingAddress1').val(decoded_response['data']['address1']);
                        $('.visitingAddress2').val(decoded_response['data']['address2']);
                        $('.visitingAddressZip').val(decoded_response['data']['zip']);
                        $('.visitingAddressCity').val(decoded_response['data']['city']);
                    }
                }
            }
        });
    }
});

 $(document).on('change', '.visitingAddressZip, .deliveraddress_zip', function(e) {
    var zipService = {
      "async": true,
      "crossDomain": true,
      "url": "https://api.bring.com/shippingguide/api/postalCode.json?clientUrl=%22asis.avalia.no%22&country=NO&pnr=" + $(this).val(),
      "method": "GET",
    }
    var res = $.ajax(zipService).done(function(zipResponse) {
        if (!zipResponse.valid) {
            showAlertMessage(invalid_zip, 'error');
        }
    });
});
