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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/ccsheet.js":
/*!*********************************!*\
  !*** ./resources/js/ccsheet.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on("click", "#start_count", function () {
  if ($(this).attr('data-val') == 1) {
    $('#close_scanner_view').addClass('hide-div');
    $('#complete_counting').removeClass('hide-div');
    $('#location').attr('disabled', 'disabled');
    $('#start_count').addClass('hide-div');
    $('#next_location').removeClass('hide-div');
    $('#product_div').removeClass('hide-div');
    addTableBodyRow();
  }
});
$(document).on("change", "#location", function () {
  if ($(this).val()) {
    if (checkLocationExists($(this).val()) == 1 && checkLocationCounted($(this).val()) == 1) {
      $('#start_count').attr('data-val', 1);
      $('#start_count').removeClass('disabled');
    }
  } else {
    $('#start_count').attr('data-val', 0);
    $('#start_count').addClass('disabled', 'disabled');
  }
});
$(document).on("click", "#next_location", function () {
  $("table[id='product_table'] tbody tr:first").remove();
  $('#start_count').attr('data-val', 0);
  $('#start_count').addClass('disabled');
  $('#location').removeAttr('disabled');
  $('#location').val('');
  $('#start_count').removeClass('hide-div');
  $('#next_location').addClass('hide-div');
});

function checkLocationCounted(location) {
  if (location) {
    $.ajax({
      type: "get",
      url: url + "/checkLocationCounted/" + location + "/" + ccsheet_id + "/" + warhouse_id,
      data: {
        '_token': token,
        'rest': 'true'
      },
      async: false,
      success: function success(response) {
        if (response) {
          var parseData = JSON.parse(response);

          if (parseData.data == 1) {
            $('#open_modal_btn').trigger('click');
            return false;
          } else {
            return 1;
          }
        }
      },
      fail: function fail(response) {
        console.log("Something Went Wrong");
      }
    });
  } else {
    console.log("Something Went Wrong");
  }

  return 1;
}

function checkLocationExists(location) {
  var return_val = 0;

  if (location && warhouse_id) {
    $.ajax({
      type: "post",
      url: url + "/checkLocationByWarehouse",
      data: {
        '_token': token,
        'rest': 'true',
        'warhouse_id': warhouse_id,
        'location': location
      },
      async: false,
      success: function success(response) {
        if (response) {
          var parseData = JSON.parse(response);

          if (parseData['location_value'] == 1) {
            return_val = 1;
          } else if (parseData['location_value'] == 2) {
            return_val = 2;
          } else if (parseData['location_value'] == 3) {
            new PNotify({
              title: message_text,
              text: lcoation_not_found,
              type: "error"
            });
            return_val = 0;
          }
        }
      },
      fail: function fail(response) {
        console.log("Something Went Wrong");
      }
    });
  } else {
    console.log("Something Went Wrong");
    return_val = 0;
  }

  return return_val;
}

function addTableBodyRow() {
  var product_unique_id = createUUID();
  var product_td = "<td><input type='text' name='product' class='form-control product' id='product_" + product_unique_id + "'></td>";
  var description_td = "<td><input type='text' name='product_description' class='form-control product_description' id='product_description_" + product_unique_id + "' disabled=disabled></td>";
  var location_td = "<td><label class=custom-form-control>" + $('#location').val() + "</label></td>";
  var qty_td = "<td class='qty_td'><input type='text' name='qty' class='form-control qty' id='qty_" + product_unique_id + "' disabled></td>";
  var html_string = "<tr id='product_tr_" + product_unique_id + "' product_unique_id=" + product_unique_id + " sn_required=''>" + product_td + description_td + location_td + qty_td + "</tr>";
  $('#product_table tbody:first').prepend(html_string);
  $('#product_' + product_unique_id).focus();
}

$(document).on("change", ".product", function () {
  var self = $(this);
  self.closest('tr').find('.product_description').val('');
  self.closest('tr').find('.qty').val('');

  if ($(this).val()) {
    $.ajax({
      type: "get",
      url: url + "/getProductDetail/" + $(this).val(),
      data: {
        '_token': token,
        'rest': 'true'
      },
      async: false,
      success: function success(response) {
        if (response) {
          var parseData = JSON.parse(response);

          if (parseData.product_detail) {
            var product_detail = parseData.product_detail;
            self.closest('tr').find('.product_description').val(product_detail.description);
            self.closest('tr').find('.qty').removeAttr('disabled');
            self.closest('tr').find('.qty').addClass('numberWithSingleComma');
            self.closest('tr').find('.qty').focus();
          } else {
            new PNotify({
              title: message,
              text: product_not_found,
              type: "error"
            });
            setTimeout(function () {
              self.focus();
            }, 50);
          }
        }
      },
      fail: function fail(response) {
        console.log("Something Went Wrong");
      }
    });
  } else {
    console.log("Something Went Wrong");
  }
});
$(document).on("change", ".qty", function () {
  var self = $(this);
  var product_number = self.closest('tr').find('.product').val();
  var product_description = self.closest('tr').find('.product_description').val();
  var qty = self.val();
  qty = replaceComma(qty);
  var product_td = "<td>" + product_number + "</td>";
  var description_td = "<td class='hidden-xs'>" + product_description + "</td>";
  var location_td = "<td><label class='counted_location'>" + $('#location').val() + "<label></td>";
  var qty_td = "<td>" + replaceDot(qty) + "</td>";
  saveProduct(product_number, product_description, $('#location').val(), null, qty, 0);
  var html_string = "<tr class=" + $('#location').val() + ">" + product_td + description_td + location_td + qty_td + "</tr>";
  $('#product_table tbody:first').prepend(html_string);
  self.closest('tr').remove();
  addTableBodyRow();
});

function saveProduct(product_number, product_description, location, serial_numbers, qty, sn_required) {
  $.ajax({
    type: "post",
    url: url + "/ccsheet/storeScannedProduct",
    data: {
      '_token': token,
      'rest': 'true',
      'product_number': product_number,
      'location_name': location,
      'qty': qty,
      'warehouse': warhouse_id,
      'ccsheet_id': ccsheet_id
    },
    async: false,
    success: function success(response) {
      if (response) {
        var jsonresult = $.parseJSON(response);

        if (jsonresult['status'] == 'success') {}
      }
    },
    fail: function fail(response) {
      console.log("Something Went Wrong");
    }
  });
}

$(document).on("click", "#back", function () {
  $('#location').val('').trigger('change');
  $("#recount").modal("hide");
});
$(document).on("click", "#continue_counting", function () {
  $("#recount").modal("hide");
  $('#start_count').attr('data-val', 1);
  $('#start_count').trigger('click');
});
$(document).on("click", "#clear_and_recount", function () {
  $('.counted_location').each(function () {
    if ($(this).text() == $('#location').val()) {
      $(this).closest('tr').remove();
    }
  });
  $.ajax({
    type: "post",
    url: url + "/resetScannedProduct",
    data: {
      '_token': token,
      'rest': 'true',
      'ccsheet_id': ccsheet_id,
      'location_name': $('#location').val()
    },
    async: false,
    success: function success(response) {
      if (response) {
        var jsonresult = $.parseJSON(response);

        if (jsonresult['status'] == 'success') {}
      }
    },
    fail: function fail(response) {
      console.log("Something Went Wrong");
    }
  });
  $("#recount").modal("hide");
  $('#start_count').attr('data-val', 1);
  $('#start_count').trigger('click');
});

/***/ }),

/***/ 4:
/*!***************************************!*\
  !*** multi ./resources/js/ccsheet.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Apache24\htdocs\code\gantic-erp\resources\js\ccsheet.js */"./resources/js/ccsheet.js");


/***/ })

/******/ });