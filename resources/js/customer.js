$("#customerForm").validate();
$(".customer_submit_btn").click(function(event) {
    $('#customer_submit_btn_hidden').val($(this).val());
});
$(".openModal").click(function(event) {
    $(".subPanelModel").modal("show");
    var form_name = $(this).attr('form-name');
    $('#subPanelContent').load($(this).attr('data-href') + "?customer_id=" + customer_id + "&id=" + $(this).attr('data-id'), function() {
        $("#" + form_name).validate();
    });
});
$(".main_radio").change(function(event) {
    var id = $(this).val();
    $(".main_radio").parents('tr').find('td .delete_contact').show();
    $(this).parents('tr').find('td .delete_contact').hide();
    $.ajax({
        url: url + "/contactAddressUpdateMainAddress/" + id + "/" + customer_id,
        method: "GET",
        data: {},
        success: function(response) {},
        fail: function(response) {}
    });
});
$('.customer_name').flexdatalist({
    cache: false,
    relatives: '.customer_name',
    minLength: 0,
    searchContain: true,
    visibleProperties: ["navn"],
    searchIn: ["navn"],
    textProperty: '{navn}',
    noResultsText: '',
    data: getCustomerUrl + "?searchValue=",
}).on('select:flexdatalist', function(event, items) {
    displayBlockUI();
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://data.brreg.no/enhetsregisteret/api/enheter?navn=" + $(this).val() + "&size=1",
        "method": "GET",
        "headers": {}
    }
    $.ajax(settings).done(function(response) {
        $('.customer_vat').val(response['_embedded']['enheter'][0]['organisasjonsnummer']);
        $('#uni_id_hidden').val('');
        getUNICustomerNumber();
        $('.shortname').focus();
        $.unblockUI();
    });
});
$(document).on('click', '#customer_search_btn', function(e) {
    displayBlockUI();
    $('.customer_name').flexdatalist('data', [])
    $.ajax({
        type: "get",
        url: getCustomerUrl + "?searchValue=" + $('.customer_name').val(),
        data: {
            '_token': token,
            'rest': 'true',
        },
        success: function(response) {
            if (response.length != 0) {
                var jsonresult = $.parseJSON(response);
                $('.customer_name').flexdatalist('data', jsonresult)
                $('.customer_name').trigger('keyup')
            }
            $.unblockUI();
        },
        fail: function(response) {
            console.log("Something Went Wrong")
        }
    });
});
$(document).on('change', '#customer_vat', function(e) {
    $('#uni_id_hidden').val('');
    var vatService = {
        "async": true,
        "crossDomain": true,
        "url": "https://data.brreg.no/enhetsregisteret/api/enheter/" + $(this).val(),
        "method": "GET",
    }
    var res = $.ajax(vatService).done(function(vatResponse) {
        if (vatResponse['navn']) {
            $('.customer_name').val(vatResponse['navn']);
        }
    });
    getUNICustomerNumber();
});
$(".view_orders").click(function() {
    displayBlockUI();
    $("#hidden_customer_id").val(customer_id);
    $("#view_customer_order_form").submit();
});
// view customer orders
$(".view_equipment").click(function() {
    displayBlockUI();
    $(".hidden_customer_id").val(customer_id);
    $("#view_customer_equipment_form").submit();
});

function getUNICustomerNumber() {
    if ($('.customer_vat').val()) {
        displayBlockUI();
        $.ajax({
            type: "get",
            url: url + "/getCustomersFromUni/" + $('.customer_vat').val(),
            data: {
                '_token': token,
                'rest': 'true',
            },
            success: function(response) {
                decoded_response = $.parseJSON(response);
                $('#uniCustomerNo').html(decoded_response.dropdown_options);
                setTimeout(function() {
                    $('#uniCustomerNo').trigger('change');
                    $.unblockUI();
                }, 100)
            },
            fail: function() {
                $.unblockUI();
            }
        });
    }
}
$(document).on('change', '#zip', function(e) {
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