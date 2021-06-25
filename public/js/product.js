/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/product.js":
/*!*********************************!*\
  !*** ./resources/js/product.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

if (id) {
  showStockInfo();
}

$("#vendor_price").keyup(function (event) {
  $('#hidden_nok_price').val($(this).val());
  $("#calculated_vendor_price").text($(this).val());
  calculatCostPrice();
});
$("#cost").keyup(function (event) {
  calculatCostPrice($(this));
});

if (!id) {
  setTimeout(function () {
    $('.setDecimal').trigger('change');
  }, 500);
}

function showStockInfo() {
  $.ajax({
    type: 'POST',
    url: stockUrl,
    data: {
      _token: token,
      'stock_id': product_id,
      'warehouse_id': ''
    },
    async: false,
    success: function success(response) {
      if (response) {
        var decoded_data = $.parseJSON(response);
        $('#stockView').html(decoded_data['data']);
        setTimeout(function () {
          if ($('.stockTable tr').length > 1) {
            $('.stockCollapse').trigger('click');
          }
        }, 100);
      }
    },
    error: function error() {}
  });
}

$("#cost_factor").keyup(function (event) {
  calculatCostPrice();
});

function calculatCostPrice() {
  var cost = $('#cost').val();
  cost = replaceComma(cost ? cost : '0');
  var vendor_price = $('#hidden_nok_price').val();
  vendor_price = replaceComma(vendor_price);
  var costprice = cost;

  if (vendor_price || cost) {
    costprice = parseFloat(cost) + parseFloat(vendor_price);
  }

  var costfactor = $('#cost_factor').val();
  costfactor = replaceComma(costfactor);
  costfactor = 100 + parseFloat(costfactor);
  costprice = costprice * costfactor / 100;
  costprice = replaceDot(costprice);
  $('#cost_price').val(costprice);
  $('#profit_percent').trigger('change');
}

$("#cost_price").change(function (event) {
  var costprice = $(this).val();
  costprice = replaceComma(costprice);
  var cost = $('#cost').val();
  cost = replaceComma(cost ? cost : '0');
  var vendor_price = $('#hidden_nok_price').val();
  vendor_price = replaceComma(vendor_price);
  cost = parseFloat(cost) + parseFloat(vendor_price);
  var cost_factor = (costprice / cost - 1) * 100;
  $('#cost_factor').val(replaceDot(cost_factor));
  calculateSalePrice();
});
$(".setDecimal").change(function (event) {
  var deciVal = $(this).val();
  deciVal = replaceComma(deciVal);
  deciVal = replaceDot(deciVal);
  $(this).val(deciVal);
});
$("#profit_percent").change(function (event) {
  var profit_percent = $(this).val();
  profit_percent = replaceComma(profit_percent);
  var costprice = $('#cost_price').val();
  costprice = replaceComma(costprice);
  var profit = costprice * profit_percent / 100;
  profit = replaceDot(profit);
  $('#profit').val(profit);
  calculateSalePrice();
});
$("#profit").change(function (event) {
  var profit = $(this).val();
  profit = replaceComma(profit);
  var costprice = $('#cost_price').val();
  costprice = replaceComma(costprice);
  var profit_percent = profit / costprice * 100;
  profit_percent = replaceDot(profit_percent);
  $('#profit_percent').val(profit_percent);
  calculateSalePrice();
});
$("#sale_price").change(function (event) {
  var sale_price = $(this).val();
  sale_price = replaceComma(sale_price);
  var costprice = $('#cost_price').val();
  costprice = replaceComma(costprice);
  var profit = sale_price - costprice;
  profit = replaceDot(profit);
  $('#profit').val(profit).trigger('change');
});

function calculateSalePrice() {
  var costprice = $('#cost_price').val();
  costprice = replaceComma(costprice);
  var profit = $('#profit').val();
  profit = replaceComma(profit);
  var saleprice = parseFloat(profit) + parseFloat(costprice);
  saleprice = Math.round(saleprice);
  saleprice = replaceDot(saleprice);
  $('#sale_price').val(saleprice);
  calculateSalePriceIncVat();
  calculateDG();
}

$("#tax").change(function (event) {
  calculateSalePriceIncVat();
});

function calculateSalePriceIncVat() {
  var saleprice = $('#sale_price').val();
  saleprice = replaceComma(saleprice);
  var vat = $('#tax').val();
  vat = replaceComma(vat);
  vat = 100 + parseFloat(vat);
  var salepriceIncVat = saleprice * vat / 100;
  salepriceIncVat = replaceDot(salepriceIncVat);
  $('.sale_price_inc_vat').val(salepriceIncVat);
}

function calculateDG() {
  var saleprice = $('#sale_price').val();
  saleprice = replaceComma(saleprice);
  var costprice = $('#cost_price').val();
  costprice = replaceComma(costprice);
  var dg = (saleprice - costprice) / saleprice * 100;
  dg = replaceDot(dg);
  $('.dg').val(dg);
}

$(document).on("change", "#supplier", function () {
  if ($(this).val()) {
    $.ajax({
      type: "get",
      url: url + "/getSupplierCurrency/" + $(this).val(),
      data: {
        '_token': token,
        'rest': 'true'
      },
      success: function success(response) {
        if (response) {
          var jsonresult = $.parseJSON(response);

          if (jsonresult.currency_details && jsonresult.currency_details.currency) {
            $('#curr_iso_name').val(jsonresult.currency_details.currency).trigger('change');
          }
        }
      },
      fail: function fail(response) {
        console.log("Something Went Wrong");
      }
    });
  } else {
    $('#curr_iso_name').val('NOK').trigger('change');
  }
}); //For Supplier cal

$(document).on("change", "#supplier_price", function () {
  calculateRealCost();
});
$(document).on("change", "#supplier_currency", function () {
  calculateRealCostNok();
});
$(document).on("change", "#supplier_discount", function () {
  calculateRealCost();
});
$(document).on("change", "#addon", function () {
  calculateRealCostWithOutDis();
});
$(document).on("change", "#realcost", function () {
  calculateRealCostNok();
});
$(document).on("change", "#other", function () {
  calculateRealCostWithOutDis();
});
$(document).on("change", "#discounted", function () {
  calculateRealCostWithOutDis();
});

function calculateRealCostWithOutDis() {
  var discounted = $('#discounted').val();
  discounted = replaceComma(discounted);
  var shipmentaddon = $('#addon').val();
  shipmentaddon = replaceComma(shipmentaddon);
  var other = $('#other').val();
  other = replaceComma(other);
  var getTotal = parseFloat(discounted) * (100 + parseFloat(shipmentaddon) + parseFloat(other)) / 100;
  $('#realcost').val(replaceDot(getTotal));
  calculateRealCostNok();
}

function calculateRealCost() {
  var supplier_price = $('#supplier_price').val();
  var supplier_discount = $('#supplier_discount').val();
  var discountAmount = replaceComma(supplier_price) * (replaceComma(supplier_discount) / 100);
  var discounted = replaceComma(supplier_price) - discountAmount;
  $('#discounted').val(replaceDot(discounted));
  var shipmentaddon = $('#addon').val();
  shipmentaddon = replaceComma(shipmentaddon);
  var other = $('#other').val();
  other = replaceComma(other);
  var getTotal = parseFloat(discounted) * (100 + parseFloat(shipmentaddon) + parseFloat(other)) / 100;
  $('#realcost').val(replaceDot(getTotal));
  calculateRealCostNok();
}

function calculateRealCostNok() {
  var realcost = $('#realcost').val();
  var currency_name = $("#supplier_currency").val();
  var converted_realcost = replaceComma(realcost);
  var fields_array = [];
  fields_array.push({
    "name": "curr_iso_name",
    "value": currency_name
  });

  if (currency_name == "NOK") {
    $('#realcost_nok').val(replaceDot(converted_realcost));
    setTimeout(function () {
      updateDeciemal();
    }, 50);
  } else {
    $.ajax({
      type: 'POST',
      url: getCurrencyDetailUrl,
      data: {
        _token: token,
        "data": JSON.stringify(fields_array)
      },
      success: function success(response) {
        if (response) {
          var decoded_data = $.parseJSON(response);

          if (decoded_data['status'] == SUCCESS) {
            var new_vendor_rate = Number(decoded_data['data']['exch_rate']) * converted_realcost;
            new_vendor_rate = replaceDot(new_vendor_rate);
            $('#realcost_nok').val(replaceDot(new_vendor_rate));
          } else {
            $('#realcost_nok').val(replaceDot(converted_realcost));
          }
        }

        setTimeout(function () {
          updateDeciemal();
        }, 50);
      },
      fail: function fail(response) {
        $('#realcost_nok').val(replaceDot(converted_realcost));
        setTimeout(function () {
          updateDeciemal();
        }, 50);
      }
    });
  }
}

function updateDeciemal() {
  $('#supplier_price').val(replaceComma($('#supplier_price').val()));
  $('#supplier_discount').val(replaceComma($('#supplier_discount').val()));
  $('#discounted').val(replaceComma($('#discounted').val()));
  $('#addon').val(replaceComma($('#addon').val()));
  $('#other').val(replaceComma($('#other').val()));
  $('#realcost').val(replaceComma($('#realcost').val()));
  $('#supplier_price').val(replaceDot($('#supplier_price').val()));
  $('#supplier_discount').val(replaceDot($('#supplier_discount').val()));
  $('#discounted').val(replaceDot($('#discounted').val()));
  $('#addon').val(replaceDot($('#addon').val()));
  $('#other').val(replaceDot($('#other').val()));
  $('#realcost').val(replaceDot($('#realcost').val()));
}

$(document).on("change", "#product_warehouse", function () {
  var seleted_warehouse = $(this).val();
  var location_url = url + "/returnOrder/getlocationbywarehouse/" + seleted_warehouse + "?type=1";

  if (seleted_warehouse) {
    $('#picklist_btn').removeAttr('disabled');
    $.ajax({
      type: "GET",
      url: location_url,
      asyc: true,
      data: {},
      success: function success(response) {
        if (response) {
          var jsonresult = $.parseJSON(response);

          if (jsonresult['status'] == 'success') {
            $('#product_location').html(jsonresult['location_details']);
            setTimeout(function () {
              if ($("#product_location option").length == 2) {
                $("#product_location").val($("#product_location option:last").val());
              }
            }, 100);
            $.unblockUI();
          }
        }
      },
      fail: function fail() {
        $.unblockUI();
        console.log("Something Went Wrong");
      }
    });
  } else {
    $.unblockUI();
  }
});

/***/ }),

/***/ 6:
/*!***************************************!*\
  !*** multi ./resources/js/product.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Apache24\htdocs\code\gantic-erp\resources\js\product.js */"./resources/js/product.js");


/***/ })

/******/ });