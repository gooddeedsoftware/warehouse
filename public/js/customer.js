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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/customer.js":
/*!**********************************!*\
  !*** ./resources/js/customer.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$("#customerForm").validate();
$(".customer_submit_btn").click(function (event) {
  $('#customer_submit_btn_hidden').val($(this).val());
});
$(".openModal").click(function (event) {
  $(".subPanelModel").modal("show");
  var form_name = $(this).attr('form-name');
  $('#subPanelContent').load($(this).attr('data-href') + "?customer_id=" + customer_id + "&id=" + $(this).attr('data-id'), function () {
    $("#" + form_name).validate();
  });
});
$(".main_radio").change(function (event) {
  var id = $(this).val();
  $(".main_radio").parents('tr').find('td .delete_contact').show();
  $(this).parents('tr').find('td .delete_contact').hide();
  $.ajax({
    url: url + "/contactAddressUpdateMainAddress/" + id + "/" + customer_id,
    method: "GET",
    data: {},
    success: function success(response) {},
    fail: function fail(response) {}
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
  data: getCustomerUrl + "?searchValue="
}).on('select:flexdatalist', function (event, items) {
  displayBlockUI();
  var settings = {
    "async": true,
    "crossDomain": true,
    "url": "https://data.brreg.no/enhetsregisteret/api/enheter?navn=" + $(this).val() + "&size=1",
    "method": "GET",
    "headers": {}
  };
  $.ajax(settings).done(function (response) {
    $('.customer_vat').val(response['_embedded']['enheter'][0]['organisasjonsnummer']);
    $('#uni_id_hidden').val('');
    getUNICustomerNumber();
    $('.shortname').focus();
    $.unblockUI();
  });
});
$(document).on('click', '#customer_search_btn', function (e) {
  displayBlockUI();
  $('.customer_name').flexdatalist('data', []);
  $.ajax({
    type: "get",
    url: getCustomerUrl + "?searchValue=" + $('.customer_name').val(),
    data: {
      '_token': token,
      'rest': 'true'
    },
    success: function success(response) {
      if (response.length != 0) {
        var jsonresult = $.parseJSON(response);
        $('.customer_name').flexdatalist('data', jsonresult);
        $('.customer_name').trigger('keyup');
      }

      $.unblockUI();
    },
    fail: function fail(response) {
      console.log("Something Went Wrong");
    }
  });
});
$(document).on('change', '#customer_vat', function (e) {
  $('#uni_id_hidden').val('');
  var vatService = {
    "async": true,
    "crossDomain": true,
    "url": "https://data.brreg.no/enhetsregisteret/api/enheter/" + $(this).val(),
    "method": "GET"
  };
  var res = $.ajax(vatService).done(function (vatResponse) {
    if (vatResponse['navn']) {
      $('.customer_name').val(vatResponse['navn']);
    }
  });
  getUNICustomerNumber();
});
$(".view_orders").click(function () {
  displayBlockUI();
  $("#hidden_customer_id").val(customer_id);
  $("#view_customer_order_form").submit();
}); // view customer orders

$(".view_equipment").click(function () {
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
        'rest': 'true'
      },
      success: function success(response) {
        decoded_response = $.parseJSON(response);
        $('#uniCustomerNo').html(decoded_response.dropdown_options);
        setTimeout(function () {
          $('#uniCustomerNo').trigger('change');
          $.unblockUI();
        }, 100);
      },
      fail: function fail() {
        $.unblockUI();
      }
    });
  }
}

$(document).on('change', '#zip', function (e) {
  var zipService = {
    "async": true,
    "crossDomain": true,
    "url": "https://api.bring.com/shippingguide/api/postalCode.json?clientUrl=%22asis.avalia.no%22&country=NO&pnr=" + $(this).val(),
    "method": "GET"
  };
  var res = $.ajax(zipService).done(function (zipResponse) {
    if (!zipResponse.valid) {
      showAlertMessage(invalid_zip, 'error');
    }
  });
});

/***/ }),

/***/ 3:
/*!****************************************!*\
  !*** multi ./resources/js/customer.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Apache24\htdocs\code\gantic-erp\resources\js\customer.js */"./resources/js/customer.js");


/***/ })

/******/ });