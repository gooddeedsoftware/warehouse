 $('.delivery_date').datetimepicker({
     format: 'DD.MM.YYYY',
     locale: 'en-gb'
 });
 $("tbody").sortable({
     items: "tr:not(.child_products)",
     handle: '> .product_move',
     stop: function(event, ui) {
         var i = 1;
         var arr = []
         $('#offer_material_Table tbody tr').each(function() {
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
         showAlertMessage(material_save_msg, 'success');
         saveOrUpdate()
     })
 });

 function saveOrUpdate() {
     $('.save_text').each(function() {
         $(this).trigger('click');
     });
     $('.save_product').each(function() {
         $(this).trigger('click');
         $('#offer_material_Table').find('tr').removeClass('bg-color-grey');
     });
 }

 function setOrder() {
     return new Promise((resolve, reject) => {
         var i = 1;
         $('#offer_material_Table tbody tr').each(function() {
             $(this).attr('sortorderval', i)
             i++;
         });
         resolve()
     })
 }
 $(".add_product_material").click(function() {
     // var products = $("#hidden_products").val();
     // var products = $.parseJSON(products);
     var product_options = "<option value='Select'>" + js_select_text + "</option>";;
     // $.each(products, function(index, value) {
     //     product_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
     // });
     var htmlString = "<tr class='order_material_tr'><td class='product_move'><i class='fa fa-arrows'></i></td>";
     htmlString += "<td class='product_td'><select style='width:100% !important;' onchange='loadProductDetails(this)' class='product_number_select2 select2 form-control product product_number'>" + product_options + "'</select><label class='labelProduct hide_div'>test</label></td>";
     htmlString += "<td class='order_quantity_td'><input type='text' class='form-control order_quantity text-align-right numberWithSingleComma'/></td>";
     htmlString += "<td class='unit_td'><select class='form-control unit'><option selected='selected' value=''>" + js_select_text + "</option></select></td>";
     htmlString += "<td class='price_td'><input type='text' class='form-control text-align-right price numberWithSingleComma'/></td>";
     htmlString += "<td class='discount_td'><input type='text' class='form-control text-align-right discount numberWithSingleComma'/></td>";
     htmlString += "<td class='sum_ex_td'><input type='text' class='form-control text-align-right sum_ex_vat numberWithSingleComma'/></td>";
     htmlString += "<td class='vat_td'><input type='text' class='form-control vat text-align-right numberWithSingleComma'/></td>";
     htmlString += "<td class='delivery_date_td'><div><input type='text' class='delivery_date form-control' style='position: relative !important;'></div></td>"
     htmlString += "<td class='save_td' style='display:none;'><button type='button' class='btn btn-primary form-control save_product' onclick='saveOrderMaterial(this);' style='display:none;'>test</button></td>";
     htmlString += "<td><a type='button' class='stock-info-btn' onclick='showStockInformation(this);'><i class='fa fa-info-circle'></i></a></td>";
     htmlString += "<td class='remove_td'><a type='button' onclick='removeOrderMaterial(this);'><i class='delete-icon fa fa-trash'></i></a></td></tr>";
     $("#offer_material_Table tbody").prepend(htmlString);
     setTimeout(function() {
         $('.delivery_date').datetimepicker({
             format: 'DD.MM.YYYY',
             locale: 'en-gb'
         });
         $(".select2").select2({
             closeOnSelect: true
         });
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
                 url: '/getSelect2Products/2',
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
 });
 /**
  * [removeOrderMaterial description]
  * @param  {[type]} obj  [description]
  * @param  {[type]} type [description]
  * @return {[type]}      [description]
  */
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
  * [loadProductDetails description]
  * @param  {[type]} obj [description]
  * @return {[type]}     [description]
  */
 function loadProductDetails(obj) {
     try {
         var product_id = $(obj).val();
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
                     $(obj).closest('tr').find('.stock-info-btn').show();
                     var jsonresult = $.parseJSON(response);
                     if (jsonresult['status'] == 'success') {
                         var units = $("#hidden_units").val();
                         units = $.parseJSON(units);
                         var unit_options = "<option value='Select'>" + js_select_text + "</option>";
                         var product_details = jsonresult['data'];
                         if (product_details['is_package'] == 0) {
                             $(obj).closest('tr').attr('is_package', 0);
                             $.each(units, function(index, value) {
                                 if (product_details['unit'] == index) {
                                     unit_options += "<option value=" + index + " selected='selected' id=" + index + ">" + value + "</option>";
                                 } else {
                                     unit_options += "<option value=" + index + " id=" + index + ">" + value + "</option>";
                                 }
                             });
                         } else {
                             $(obj).closest('tr').attr('is_package', 1);
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
 $(document).on("change", ".order_quantity, .price, .discount", function() {
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
 });

 function showStockInformation(obj) {
     var product_id = $(obj).closest('tr').find('.product').val();
     var warehouseStatus = 0;
     $.ajax({
         type: 'POST',
         url: stockUrl,
         data: {
             _token: token,
             'stock_id': product_id,
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
 // save order material
 function saveOrderMaterial(obj) {
     displayBlockUI();
     var product_id = $(obj).parent('td').siblings('.product_td').find('.product_number').val();
     if (product_id) {
         $.ajax({
             url: url + "/offermaterial/customStore",
             type: "POST",
             async: false,
             data: {
                 '_token': token,
                 'rest': 'true',
                 'product': product_id,
                 'order_quantity': $(obj).closest('tr').find('.order_quantity').val(),
                 'id': $(obj).closest('tr').attr('material_id'),
                 'order_id': $("#product_order_id").val(),
                 'delivery_date': $(obj).closest('tr').find('.delivery_date').val(),
                 'unit': $(obj).closest('tr').find('.unit').val(),
                 'sum_ex_vat': $(obj).closest('tr').find('.sum_ex_vat').val(),
                 'vat': $(obj).closest('tr').find('.vat').val(),
                 'discount': $(obj).closest('tr').find('.discount').val(),
                 'price': $(obj).closest('tr').find('.price').val(),
                 'sortorderval': $(obj).closest('tr').attr('sortorderval'),
                 'is_package': $(obj).closest('tr').attr('is_package'),
             },
             success: function(response) {
                 decoded_response = $.parseJSON(response);
                 if (decoded_response['status'] == SUCCESS) {
                     var delete_icon = '<a href="' + url + '/ordermaterial/' + decoded_response['data'] + '" data-method="delete" data-modal-text="Are you sure you want to delete this product?" data-csrf="' + token + '"><i class="delete-icon fa fa-trash"></i></a>';
                     $(obj).parent('td').siblings('.remove_td').html(delete_icon);
                     $(obj).closest('tr').attr('material_id', decoded_response['data']);
                     bootbox.hideAll();
                     setTimeout(function() {
                         laravel.initialize();
                     }, 500);
                 }
                 $.unblockUI();
             },
             fail: function() {
                 showAlertMessage("Something Went Wrong");
                 $.unblockUI();
             }
         });
     }
 }
 //  $(document).on("click", "#order_material_Table tbody tr", function() {
 //     if ($(this).closest('tr').find('#is_content').val() != 1 && $(this).closest('tr').find('.update_product').attr('data-val') != null && $(this).closest('tr').find('.update_product').attr('data-val') != '' && $(this).closest('tr').find('.update_product').attr('data-val') != undefined) {
 //         if ($(this).hasClass('bg-color-grey')) {
 //             $(this).removeClass('bg-color-grey')
 //         } else {
 //             $('#order_material_Table').find('tr').removeClass('bg-color-grey');
 //             $(this).addClass('bg-color-grey');
 //         }
 //     }
 // });
 $(".add_new_text").click(function() {
     var usertype = $("#hidden_usertype").val().trim();
     var htmlString = "<tr class='order_material_tr'><td class='product_move'><i class='fa fa-arrows'></i></td>";
     htmlString += "<td colspan='8'><input type='text' class='product_text form-control' style='position: relative !important;'></td>";
     htmlString += "<td class='save_content_td' style='display:none;'><button type='button' class='btn btn-primary form-control save_text' data-val='-1' onclick='saveText(this);'>s</button></td>"
     htmlString += "<td class='remove_td'><a type='button' onclick='removeOrderMaterial(this);'><i class='delete-icon fa fa-trash'></i></a></td></tr>";
     var reference_id = -1;
     $('#offer_material_Table tr').each(function() {
         if ($(this).hasClass('bg-color-grey')) {
             reference_id = $(this).closest('tr');
         }
     });
     if (reference_id == -1) {
         $("#offer_material_Table tbody").prepend(htmlString);
     } else {
         reference_id.after(htmlString);
     }
 });

 function saveText(obj) {
     displayBlockUI();
     var reference_id = -1;
     $('#offer_material_Table tr').each(function() {
         if ($(this).hasClass('bg-color-grey')) {
             reference_id = $(this).closest('tr').find('.update_product').attr('data-val');
         }
     });
     $.ajax({
         type: "POST",
         url: url + "/storeText",
         async: false,
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
             'printer': $('#printer').val(),
             'order_id': order_id,
             'type': 'offer'
         },
         success: function(response) {
             decoded_response = $.parseJSON(response);
             console.log(decoded_response);
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
     htmlString += '<td id="carrier_name" sender_id="null" printer="null" identifier="Ingen">Ingen</td>';
     htmlString += '<td id="product_name" identifier="Hentes">Hentes</td>';
     htmlString += '<td><input name="list_price" class="numberWithSingleComma pickupPrice netprice form-control" value="" id="list_price"></td>';
     htmlString += '<td><input name="customerprice" class="numberWithSingleComma pickupPrice customerpickprice form-control" value="" id="customerprice_1"></td>';
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
     $('.save_text').each(function() {
         $(this).trigger('click');
     });
     $('.save_product').each(function() {
         $(this).trigger('click');
         $('#offer_material_Table').find('tr').removeClass('bg-color-grey');
     });
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
             async: false,
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
 $(document).on("click", "#offer_material_Table tbody tr", function() {
     if ($(this).find(".save_text").length == 0) {
         if ($(this).hasClass('bg-color-grey')) {
             $(this).removeClass('bg-color-grey')
         } else {
             $('#order_material_Table').find('tr').removeClass('bg-color-grey');
             $(this).addClass('bg-color-grey');
         }
     }
 });