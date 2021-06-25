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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/productpackage.js":
/*!****************************************!*\
  !*** ./resources/js/productpackage.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var pacakage_product_list = $("#pacakage_product_list").text();

if (pacakage_product_list) {
  listGroupedDetails(pacakage_product_list);
} // product delete


$(document).on('click', '.product_package_delete', function () {
  var delete_confirm = confirm(confirm_delete);

  if (delete_confirm) {
    window.location.href = $(this).attr('data-href');
  } else {
    return false;
  }
});
$("#cloneProductTableBtn").click(function (event) {
  createProductPackageTableRow();
}); // create new row in product detasil table

function createProductPackageTableRow() {
  var rowCount = $('.hidden_product_package_table_row_count').val();
  var totalRow = rowCount && typeof rowCount != "undefined" ? rowCount : 0;
  $("#hidden_warehouse_table_row_count").val("");
  var i = totalRow;
  var product_options = getProductForGroupped("");
  var products = "<td><select style='width: 100%;' class='select2 form-control product_lists' id='product_" + i + "'  name='product_" + i + "' required='required'>" + product_options + "</select>";
  var qty = "<td><input style='width: 50%;' type=text class='qty_class form-control' maxlength='8' id='qty_" + i + "' value='' name='qty_" + i + "' required='required'></td>";
  var delete_td = "<td class='product_delete_td'><i class='fas fa-minus delete_product_orders' id='delete_product_orders" + i + "'></i></td>";
  var html_string = "<tr id= product_tr_" + i + ">" + products + qty + delete_td + "</tr>";
  $('#product_package_table tbody:last').append(html_string);
  i++;
  $(".hidden_product_package_table_row_count").val(i);
  window.localStorage.setItem("group_product_table_row_count", i);
  setTimeout(function () {
    $(".select2").select2();
  }, 100);
}

$(document).on('click', '.delete_product_orders', function () {
  deleteProductRow($(this));
}); // Product options

function getProductForGroupped(product_id) {
  var product_options = "<option value=''>" + select_text + "</option>";
  var product_array = $("#product_lists").val(); // product detail options

  if (product_array) {
    product_array = $.parseJSON(product_array);
    $.each(product_array, function (index, value) {
      if (product_id == index) {
        product_options += "<option selected class='group_product_id_remove' id=" + index + " value=" + index + " >" + value + "</option>";
      } else {
        product_options += "<option class='group_product_id_remove' id=" + index + " value=" + index + " >" + value + "</option>";
      }
    });
  }

  return product_options;
}

function listGroupedDetails(data) {
  var parsed_data = $.parseJSON(data);
  var product_id_dom = "";
  var quantity_dom = "";
  var group_product_id_dom = "";
  var delete_dom = "";
  var j = 0;

  for (var i = 0; i < parsed_data.length; i++) {
    var product_options = getProductForGroupped(parsed_data[i]['content']);
    removeSelectedProductOption(parsed_data[i]['content']);
    product_id_dom = "<tr><td><select  style='width: 100%;' class='select2 form-control product_lists' id='product_" + i + "'  name='product_" + i + "'>" + product_options + "</select>";
    product_id_dom += "<input type='hidden'  name='group_product_list_id_" + i + "' id='group_product_list_id_" + i + "' value='" + parsed_data[i]['id'] + "'></td>";
    quantity_dom = "<td><input style='width: 50%;'  type='text' class='form-control qty_class' maxlength='8' id='qty_" + i + "' name='qty_" + i + "' value='" + parsed_data[i]['qty'] + "'></td>";
    delete_dom = "<td class='product_delete_td'><i class='fas fa-minus delete_product_orders' id='delete_product_orders" + i + "'></i></td></tr>";
    html_string = product_id_dom + quantity_dom + delete_dom;
    $('#product_package_table tbody:last').append(html_string);
    window.localStorage.setItem("group_product_table_row_count", i);
    j = i + 1;
    $(".hidden_product_package_table_row_count").val(j);
  }

  setTimeout(function () {
    $(".select2").select2();
    $('.product_lists').trigger('change');
  }, 100);
} // remove the option if it is already selected.


$(document).on("change", ".product_lists", function () {
  var current_selected_product = $(this).val();
  var self = $(this);
  removeSelectedProductOption(current_selected_product);
  var product_url = url + "/product/getProductDetailFromId";
  $.ajax({
    type: "POST",
    url: product_url,
    async: false,
    data: {
      '_token': token,
      'rest': 'true',
      'product_id': $(this).val(),
      'order_type': "",
      'warehouse_id': ""
    },
    success: function success(response) {
      if (response) {
        try {
          decoded_response = $.parseJSON(response);

          if (decoded_response['status'] == SUCCESS) {
            product_details = decoded_response['data'];

            if (product_details['sn_required'] == 1) {
              self.closest('tr').find('.qty_class').addClass('validateNumbers');
              self.closest('tr').find('.qty_class').removeClass('numberWithSingleComma');
            } else {
              self.closest('tr').find('.qty_class').removeClass('validateNumbers');
              self.closest('tr').find('.qty_class').addClass('numberWithSingleComma');
            }
          }
        } catch (Exception) {}
      }
    },
    fail: function fail() {
      alert(something_went_wrong);
    }
  });
}); // remove the option if it is already selected.

function removeSelectedProductOption(current_selected_product) {
  var product_array = $("#product_lists").val();

  if (product_array) {
    product_array = $.parseJSON(product_array);
    var i = 0;
    $.each(product_array, function (index, value) {
      if (current_selected_product == index) {
        delete product_array[index];
      }

      i++;
    });
    $("#product_lists").val(JSON.stringify(product_array));
  }
} // delete order_product


function deleteProductRow(obj) {
  confirm_msg = confirm(confirm_delete);

  if (confirm_msg) {
    $(obj).closest('tr').remove();
  }
}
/* warehouse order script logic end */

/***/ }),

/***/ 2:
/*!**********************************************!*\
  !*** multi ./resources/js/productpackage.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Apache24\htdocs\code\gantic-erp\resources\js\productpackage.js */"./resources/js/productpackage.js");


/***/ })

/******/ });