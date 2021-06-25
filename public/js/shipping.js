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
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/shipping.js":
/*!**********************************!*\
  !*** ./resources/js/shipping.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on("change", ".volume", function () {
  if ($('#height').val() > 0 && $('#length').val() > 0 && $('#width').val() > 0) {
    height = $('#height').val() / 10;
    height = parseFloat(height).toFixed(2);
    length = $('#length').val() / 10;
    length = parseFloat(length).toFixed(2);
    width = $('#width').val() / 10;
    width = parseFloat(width).toFixed(2);
    total = height * length * width;
    total = parseFloat(total).toFixed(2);
    $('#volume').val(total);
  }
});
$(document).on("click", ".saveShipment", function () {
  obj = $(this);
  id = $(this).attr('id');
  $(this).closest('tr').find('.customerprice').attr("readonly", false);
  $('.saveShipment').each(function () {
    if (id != $(this).attr('id')) {
      $(this).closest('tr').find('.customerprice').attr("readonly", true);
      $(this).prop("checked", false);
      $(this).closest('tr').removeClass('saved');
      $(this).closest('tr').addClass('unSaved');
      $(this).closest('tr').removeAttr('shipment_id');
    }
  });
  saveShipment(obj);
});
$(document).on("click", "#getprices", function () {
  if ($('#volume').val() <= 0 || $('#weight').val() <= 0) {
    showAlertMessage("Check the inputs weight and volume are required", "error");
    return false;
  }

  $('#shippingTable tr[shippment-status="0"]').remove();
  $('.unSaved').remove();
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
      'type': 'order'
    },
    success: function success(response) {
      decoded_response = $.parseJSON(response);
      console.log(decoded_response);

      if (decoded_response.type == "success") {
        $("#shippingTable tbody").append(decoded_response.content);
      } else {
        showAlertMessage(decoded_response.message, 'error');
      }

      $.unblockUI();
    },
    fail: function fail() {
      $.unblockUI();
    }
  });
});
$(document).on("change", ".customerprice", function () {
  displayBlockUI();
  $.ajax({
    type: "POST",
    url: updateShippingUrl,
    data: {
      '_token': token,
      'rest': 'true',
      'id': $(this).closest('tr').attr('shipment_id'),
      'customerprice': $(this).val(),
      "type": 1
    },
    success: function success(response) {
      $.unblockUI();
    },
    fail: function fail() {
      $.unblockUI();
    }
  });
});
$(document).on("change", ".pickupPrice", function () {
  displayBlockUI();
  $.ajax({
    type: "POST",
    url: updateShippingUrl,
    data: {
      '_token': token,
      'rest': 'true',
      'id': $(this).closest('tr').attr('shipment_id'),
      'netprice': $(this).closest('tr').find('.netprice').val(),
      'customerprice': $(this).closest('tr').find('.customerpickprice').val(),
      'type': 2
    },
    success: function success(response) {
      $.unblockUI();
    },
    fail: function fail() {
      $.unblockUI();
    }
  });
});
$(document).on("click", "#pickup", function () {
  $('#shippingTable tr[shippment-status="0"]').remove();
  $('.unSaved').remove();
  var payload = {};
  payload.customerprice = '';
  payload.height = null;
  payload.width = null;
  payload.length = null;
  payload.weight = null;
  payload.volume = null;
  payload.sender_id = null;
  payload.printer = null;
  payload.product_name = "Hentes";
  payload.product_identifier = "Hentes";
  payload.carrier_name = "Ingen";
  payload.carrier_identifier = "Ingen";
  payload.customerprice = "";
  payload.grossprice = "";
  payload.netprice = "";
  payload.estimatedcost = "";
  payload.order_id = $('#shippmentContainer').attr('orderid');
  $.ajax({
    type: "POST",
    url: storeShippingUrl,
    data: {
      '_token': token,
      'rest': 'true',
      'shipmentData': payload,
      'type': '1'
    },
    success: function success(response) {
      decoded_response = $.parseJSON(response);
      var id = decoded_response.shipping.id;
      var htmlString = "<tr class='saved' shipment_id='" + id + "' shippment-status='0'>";
      htmlString += '<td id="carrier_name" sender_id="null" printer="null" identifier="Ingen">Ingen</td>';
      htmlString += '<td id="product_name" identifier="Hentes">Hentes</td>';
      htmlString += '<td><input name="list_price" class="numberWithSingleComma pickupPrice netprice form-control" value="" id="list_price"></td>';
      htmlString += '<td><input name="customerprice" class="numberWithSingleComma pickupPrice customerpickprice form-control" value="" id="customerprice_1"></td>';
      htmlString += "<td></td><td></td><td></td><td></td></tr>";
      $("#shippingTable tbody").prepend(htmlString);
      $.unblockUI();
    },
    fail: function fail() {
      $.unblockUI();
    }
  });
});

function saveShipment(obj) {
  $('#customCheck').removeAttr('disabled');
  $('#customCheck').removeAttr('readonly');
  var payload = {};
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
  payload.customerprice = obj.closest('tr').find('.customerprice').val();
  payload.grossprice = obj.closest('tr').find('#amounTd').attr('grossprice');
  payload.netprice = obj.closest('tr').find('#amounTd').attr('netprice');
  payload.estimatedcost = obj.closest('tr').find('#estimatedcost').attr('estimatedcost');
  payload.order_id = $('#shippmentContainer').attr('orderid');
  $.ajax({
    type: "POST",
    url: storeShippingUrl,
    data: {
      '_token': token,
      'rest': 'true',
      'shipmentData': payload,
      'type': '1'
    },
    success: function success(response) {
      decoded_response = $.parseJSON(response);
      obj.closest('tr').addClass('saved');
      obj.closest('tr').removeClass('unSaved');
      obj.closest('tr').attr('shipment_id', decoded_response.shipping.id);
      $.unblockUI();
    },
    fail: function fail() {
      $.unblockUI();
    }
  });
}

$(document).on("change", "#sender, #printer", function () {
  $('#getprices').attr('disabled', 'disabled');
  var printer_val = $('#printer').val();
  var sender_val = $('#sender').val();

  if (printer_val != 'Select' && printer_val != 'velg' && printer_val != '' && sender_val != 'Select' && sender_val != 'velg' && sender_val != '') {
    $('#getprices').removeAttr('disabled');
  }
});

/***/ }),

/***/ 5:
/*!****************************************!*\
  !*** multi ./resources/js/shipping.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Apache24\htdocs\code\gantic-erp\resources\js\shipping.js */"./resources/js/shipping.js");


/***/ })

/******/ });