    <?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//Comman routes
Route::get('/', function () {
    if (Auth::user()) {
        return Redirect::to('home');
    } else {
        return Redirect::to('login');
    }
});
Auth::routes(['register' => false]);
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ln/{locale}', function ($locale = 'en') {
    session(['language' => $locale]);
    return Redirect::back();
});
Route::get('user/images/{filename}', function ($filename) {
    $img = Image::make(storage_path() . "/uploads/user/" . $filename);
    return $img->response('jpg');
});

// Auth routes
Route::group(['middleware' => 'auth', 'as' => 'main.'], function () {
    $controllers = array(
        'user'              => 'UserController',
        'usertype'          => 'UsertypeController',
        'department'        => 'DepartmentController',
        'accplan'           => 'AccPlanController',
        'activities'        => 'ActivitiesController',
        'equipmentcategory' => 'EquipmentCategoryController',
        'currency'          => 'CurrencyController',
        'company'           => 'CompanyController',
        'warehouse'         => 'WarehouseController',
        'location'          => 'LocationController',
        'offerpermission'   => 'OfferPermissionController', // Used for all permission
        'group'             => 'GroupController',
        'customer'          => 'CustomerController',
        'supplier'          => 'SupplierController',
        'warehousedetails'  => 'WarehouseDetailsController',
        'product'           => 'ProductController',
        'productpackage'    => 'ProductPackageController',
        'warehouseorder'    => 'WarehouseOrderController',
        'whs_history'       => 'WarehouseHistoryController',
        'ccsheet'           => 'CCSheetController',
        'order'             => 'OrderController',
        'ordermaterial'     => 'OrderMaterialController',
        'equipment'         => 'EquipmentController',
        'country'           => 'CountryController',
        'shipping'          => 'ShippingController',
        'offersettings'     => 'OfferSettingsController',
        'logistraDetails'   => 'LogistraDetailsController',
        'printer_detail'    => 'LogistraPrinterController',
        'productGroup'      => 'ProductGroupController',
        'grossMargin'       => 'GrossMarginController',
        'offer'             => 'OfferController',
    );
    foreach ($controllers as $key => $controller) {
        //Will generates Crud functions (index,create, edit, delete, update, store)
        Route::get($key . '/index', array('as' => $key . '.index', 'uses' => $controller . '@index'));
        Route::resource($key, $controller);
        Route::post($key . '/index', array('as' => $key . '.search', 'uses' => $controller . '@index'));
        Route::post($key . '/rest', array('as' => $key . '.rest')); // here applied rest route
    }

    //pagination
    Route::get('changePagination/{paginate_size}/{user_id}', 'AjaxController@updateUserPagination');

    //Customer
    Route::get('getCustomers', array('as' => 'customer.getCustomers', 'uses' => 'CustomerController@getCustomers'));

    Route::get('contact/loadContactView', array('as' => 'contact.loadContactView', 'uses' => 'ContactPersonController@loadContactView'));

    Route::get('customerAddress/loadCustomerAddressView', array('as' => 'customerAddress.loadCustomerAddressView', 'uses' => 'CustomerAddressController@loadCustomerAddressView'));

    Route::post('contact/createOrUpdateContact', array('as' => 'contact.createOrUpdateContact', 'uses' => 'ContactPersonController@createOrUpdateContact'));

    Route::delete('contact/{id}', array('as' => 'contact.delete', 'uses' => 'ContactPersonController@delete'));

    Route::post('customerAddress/createOrUpdateCustomerAddress', array('as' => 'customerAddress.createOrUpdateCustomerAddress', 'uses' => 'CustomerAddressController@createOrUpdateCustomerAddress'));

    Route::delete('customerAddress/{id}', array('as' => 'customerAddress.delete', 'uses' => 'CustomerAddressController@delete'));

    Route::get('contactAddressUpdateMainAddress/{id}/{customer_id}', array('as' => 'contact_address.updateMainAddress', 'uses' => 'CustomerAddressController@updateMainAddress'));

    Route::post('validateuseremail', array('as' => 'user.validateuseremail', 'uses' => 'AjaxController@validateuseremail'));

    Route::post('group/loadUsers', array('as' => 'group.loadUsers', 'uses' => 'AjaxController@loadUsers'));
    //Customer ends

    // Product starts
    Route::post('getCurrencyDetails', array('as' => 'currencyController.getCurrencyDetails', 'uses' => 'CurrencyController@getCurrencyDetails'));

    Route::post('export', array('as' => 'export', 'uses' => 'GanticController@exportCSV'));

    Route::post('exportProduct', array('as' => 'product.export', 'uses' => 'ProductController@exportProduct'));

    Route::post('product/import', array('as' => 'product.import', 'uses' => 'ProductController@importProduct'));

    // Product ends

    // Supplier order
    Route::get('createSupplierOrder', array('as' => 'warehouseorder.createSupplierOrder', 'uses' => 'WarehouseOrderController@createSupplierOrder'));

    Route::get('editSupplierOrder/{id}', array('as' => 'warehouseorder.editSupplierOrder', 'uses' => 'WarehouseOrderController@editSupplierOrder'));

    Route::post('product/getProductDetailFromOrderType', array('as' => 'product.getProductDetailFromOrderType', 'uses' => "ProductController@getProductDetailFromOrderType"));

    Route::post('warehouseOrder/SupplierOrder', array('as' => 'warehouseSupplierOrder.store', 'uses' => "WarehouseOrderController@storeSupplierOrder"));

    Route::put('warehouseOrder/SupplierOrder/{id}', array('as' => 'warehouseSupplierOrder.update', 'uses' => "WarehouseOrderController@updateSupplierOrder"));

    Route::post('ordermaterial/getPacakageProduct', array('as' => 'productpackage.getProducts', 'uses' => 'ProductPackageController@getPacakgeProducts'));

    Route::get('location/getlocationbywarehouse/{warehouse_id}', array('as' => 'warehouse.getLocationsByWarehouse', 'uses' => 'LocationController@getLocationsByWarehouse'));

    //Supplier order ends

    // Adjustment order
    Route::get('createAdjustmentOrder', array('as' => 'warehouseorder.createAdjustmentOrder', 'uses' => 'WarehouseOrderController@createAdjustmentOrder'));

    Route::get('editAdjustmentOrder/{id}', array('as' => 'warehouseorder.editAdjustmentOrder', 'uses' => 'WarehouseOrderController@editAdjustmentOrder'));

    Route::post('warehouseOrder/AdjustmentOrder', array('as' => 'warehouseAdjustmentOrder.store', 'uses' => "WarehouseOrderController@storeAdjustmentOrder"));

    Route::put('warehouseOrder/AdjustmentOrder/{id}', array('as' => 'warehouseAdjustmentOrder.update', 'uses' => "WarehouseOrderController@updateAdjustmentOrder"));
    //Adjustment order ends

    //Transfer order
    Route::get('createTransferOrder', array('as' => 'warehouseorder.createTransferOrder', 'uses' => 'WarehouseOrderController@createTransferOrder'));

    Route::post('product/getProductDetailFromId', array('as' => 'product.getProductDetailFromId', 'uses' => "ProductController@getProductDetailFromId"));

    Route::post('warehouseinventory/getProductActualQuantity', array('as' => 'warehouseinventory.getProductActualQuantity', 'uses' => "WarehouseDetailsController@getProductActualQuantity"));

    Route::post('warehouseorder/updateStatusToArchive', array('as' => 'warehousedetails.updateStatusToArchive', 'uses' => "WarehouseOrderController@updateStatusToArchive"));

    Route::get('warehouseorder/editTransferOrder/{type}', array('as' => 'warehouseorder.editTransferOrder', 'uses' => 'WarehouseOrderController@editTransferOrder'));
    //Transfer ends

    // Return order start
    Route::get('warehouseorder/returnOrder/{type}', array('as' => 'warehouseorder.editReturnOrder', 'uses' => 'WarehouseOrderController@editReturnOrder'));

    Route::get('returnOrder/getlocationbywarehouse/{warehouse_id}', array('as' => 'returnOrder.getLocationsByWarehouse', 'uses' => 'LocationController@getLocationsByWarehouseForReturnOrder'));
    // Return order ends

    // Warehouse Order starts
    Route::get('warehouseorder/downloadWarehouseReport/{id}', array('as' => 'warehouseorder.downloadWarehouseReport', 'uses' => 'WarehouseOrderController@downloadWarehouseReport'));

    // Warehouse Order ends

    //Stock starts
    Route::post('getLocations', array('as' => 'warehouseDetailsController.getLocations', 'uses' => 'WarehouseDetailsController@getLocations'));

    Route::post('getOnstockDetails', array('as' => 'warehousedetails.getOnstockDetails', 'uses' => 'WarehouseDetailsController@getOnstockDetails'));

    Route::get('warehousedetails/viewStock/{product_id}', array('as' => 'warehousedetails.viewStock', 'uses' => 'WarehouseDetailsController@index'));

    Route::get('getcustomerorder/product/{product_id}', array('as' => 'order.getCustomerOrder', 'uses' => 'OrderController@getCustomerOrderByProduct'));

    Route::get('getcustomerorders/bywarehouse/{product_id}/{location_id}/{warehouse_id}', array('as' => 'order.getCustomerOrderByWarehouse', 'uses' => 'OrderController@getCustomerOrderByWarehouse'));
    //Stock ends

    //ccsheet starts
    Route::get('ccsheetDetails/{id}', array('as' => 'ccsheet.ccsheetDetails', 'uses' => 'CCSheetController@ccsheetDetails'));

    Route::post('setCounted', array('as' => 'ccsheet.setCounted', 'uses' => 'CCSheetController@setCounted'));

    Route::get('updateCCSheetStatus/{id}', array('as' => 'ccsheet.updateCCSheetStatus', 'uses' => 'CCSheetController@updateCCSheetStatus'));

    Route::get('recountCCSheetDetails/{id}', array('as' => 'ccsheet.recountCCSheetDetails', 'uses' => 'CCSheetController@recountCCSheetDetails'));

    Route::get('createCCSheetReport/{id}', array('as' => 'ccsheet.createCCSheetReport', 'uses' => 'CCSheetController@createCCSheetReport'));

    Route::get('createAdjustmentOrder/{id}/{whs_id}', array('as' => 'ccsheet.createAdjustmentOrder', 'uses' => 'CCSheetController@createAdjustmentOrder'));

    Route::get('products/getproduct/{product_name}/{warehouse}/{ccsheet_id}', array('as' => 'product.getproducts', 'uses' => 'ProductController@getProductsByName'));

    Route::post('ccsheetdetails/saverecord', array('as' => 'ccsheet.saverecord', 'uses' => 'CCSheetController@saveCCSheetRecord'));

    Route::post('ccsheetdetails/checkserialnumber', array('as' => 'ccsheet.checkserialnumber', 'uses' => 'CCSheetController@checkSerialNumber'));

    Route::post('ccsheetdetails/deleteccsheetproduct', array('as' => 'ccsheet.deleteccsheetproduct', 'uses' => 'CCSheetController@deleteCCSheetProduct'));

    Route::get('ccsheet/products/getproduct/{product_id}/{warehouse}', array('as' => 'product.getProductDetail', 'uses' => 'ProductController@getProductDetailForCCsheet'));

    Route::get('scannerView/{ccsheet_id}', array('as' => 'ccsheet.scannerView', 'uses' => 'CCSheetController@scannerView'));

    Route::post('checkLocationByWarehouse', array('as' => 'location.checkLocationByWarehouse', 'uses' => 'LocationController@checkLocationByWarehouse'));

    Route::get('getProductDetail/{product_number}', array('as' => 'ccsheet.getProductDetail', 'uses' => 'ProductController@getProductDetailByNumber'));

    Route::post('ccsheet/storeScannedProduct', array('as' => 'ccsheet.storeScannedProduct', 'uses' => 'CCSheetController@storeScannedProduct'));

    Route::get('checkLocationCounted/{location_name}/{ccsheet_id}/{warehouse_id}', array('as' => 'ccsheet.checkLocationCounted', 'uses' => 'CCSheetController@checkLocationCounted'));

    Route::get('completeCounting/{ccsheet_id}', array('as' => 'ccsheet.completeCounting', 'uses' => 'CCSheetController@completeCounting'));

    Route::post('resetScannedProduct', array('as' => 'ccsheet.resetScannedProduct', 'uses' => 'CCSheetController@resetScannedProduct'));
    //ccsheet starts

    //customer order
    Route::get('orderindex/{order_status}', array('as' => 'order.orderindex', 'uses' => 'OrderController@index'));

    Route::post('orderindex/{order_status}', array('as' => 'order.orderindex', 'uses' => 'OrderController@index'));

    Route::post('getUsersByDepartment', array('as' => 'orders.getUsersByDepartment', 'uses' => 'AjaxController@getUsersByDepartment'));

    Route::post('getContactPersonsAndUsers', array('as' => 'orders.getContactPersonsAndUsers', 'uses' => 'AjaxController@getContactPersonsAndUsers'));

    Route::post('customer/contact_inline_store', array('as' => 'contact.contact_inline_store', 'uses' => 'ContactPersonController@contactInlineStore'));

    Route::get('product/getProductDetailForOffer/{product_id}', array('as' => 'product.getProductDetailForOffer', 'uses' => 'ProductController@getProductDetailForOffer'));

    Route::get('orders/getDeliverAddressDetails/{id}', array('as' => 'orders.getDeliverAddressDetails', 'uses' => 'CustomerAddressController@getCustomerAddressDetails'));

    Route::post('sendOrDownloadOrder', array('as' => 'order.sendOrDownloadOrder', 'uses' => 'OrderController@sendOrDownloadOrder'));

    Route::post('sendOfferOrderMail', array('as' => 'order.sendOfferOrderMail', 'uses' => 'OrderController@sendOfferOrderMail'));

    //Customr order ends

    // Order material
    Route::post('order/listOrderMaterials/{id}', array('as' => 'ordermaterial.listOrderMaterials', 'uses' => 'OrderMaterialController@listOrderMaterials'));

    Route::get('order/listOrderMaterials/{id}', array('as' => 'ordermaterial.listOrderMaterials', 'uses' => 'OrderMaterialController@listOrderMaterials'));

    Route::get('order/getReturnProduct/{order_id}', array('as' => 'order.getReturnProduct', 'uses' => 'OrderMaterialController@getReturnProduct'));

    Route::post('ordermaterial/approveOrderMaterials', array('as' => 'ordermaterial.approveOrderMaterials', 'uses' => 'OrderMaterialController@approveOrderMaterials'));

    Route::post('ordermaterial/createReturnOrder', array('as' => 'ordermaterial.createReturnOrder', 'uses' => 'OrderMaterialController@createReturnOrder'));

    Route::get('order/billingData/{order_id}', array('as' => 'order.billingData', 'uses' => 'OrderMaterialController@openBillingDataView'));

    Route::post('ordermaterial/getProductDetail', array('as' => 'ordermaterial.getProductDetail', 'uses' => "OrderMaterialController@productDetails"));

    Route::post('ordermaterial/customStore', array('as' => 'ordermaterial.customStore', 'uses' => 'OrderMaterialController@customStore'));

    Route::post('ordermaterial/getProductAvailabeQuantity', array('as' => 'ordermaterial.getProductAvailabeQuantity', 'uses' => "OrderMaterialController@getProductAvailabeQuantity"));

    Route::post('storeBilllingData', array('as' => 'ordermaterial.storeBilllingData', 'uses' => 'OrderMaterialController@storeBilllingData'));
    //Order material ends

    //Equipment
    Route::get('equipment/getchildequipments/{customer_id}', array('as' => 'equipment.getchildequipments', 'uses' => 'EquipmentController@getChildEquipments'));

    Route::get('order/createOrderFromEquipment/{equipment_id}/{customer_id}', array('as' => 'order.createOrderFromEquipment', 'uses' => 'OrderController@createOrderFromEquipment'));
    //Equipment ends

    Route::get('order/shipping/{id}', array('as' => 'order.shipping', 'uses' => 'ShippingController@listOrderShipping'));

    Route::post('order/shipping/getPrices', array('as' => 'order.getPrices', 'uses' => 'ShippingController@getPrices'));

    Route::post('order/storeShipping', array('as' => 'order.storeShipping', 'uses' => 'ShippingController@storeShipping'));

    Route::post('order/updateShipping', array('as' => 'order.updateShipping', 'uses' => 'ShippingController@updateShipping'));

    Route::post('order/picklist', array('as' => 'order.picklist', 'uses' => 'OrderController@picklist'));

    Route::post('order/packlist', array('as' => 'order.packlist', 'uses' => 'OrderController@packlist'));

    Route::post('product/getOnstockDetails', array('as' => 'product.getOnstockDetails', 'uses' => 'OrderMaterialController@getOnstockDetails'));

    Route::post('material/getWarehouseOption', array('as' => 'product.getWarehouseOption', 'uses' => 'OrderMaterialController@getWarehouseOption'));

    Route::get('getCustomersFromUni/{vat}', array('as' => 'customer.UniCustomers', 'uses' => 'CustomerController@getCustomersFromUni'));

    Route::get('recalculate_prices', array('as' => 'product.recalculate_prices', 'uses' => 'ProductController@recalculatePrices'));

    Route::get('downloadShipmentLabel/{consignment_id}', array('as' => 'downloadShipmentLabel', 'uses' => 'ShippingController@downloadShipmentLabel'));

    Route::get('getSupplierCurrency/{id}', array('as' => 'supplier.getSupplierCurrency', 'uses' => 'SupplierController@getSupplierCurrency'));

    Route::get('downloadFile', array('as' => 'order.downloadFile', 'uses' => 'OrderController@downloadFile'));

    Route::post('storeText', array('as' => 'product.storeText', 'uses' => 'OrderMaterialController@storeText'));

    Route::post('createOrderinUNI', array('as' => 'order.createOrderinUNI', 'uses' => 'OrderController@createOrderinUNI'));

    Route::get('productSupplier/loadview', array('as' => 'productSupplier.loadview', 'uses' => 'ProductController@productSupplierView'));

    Route::post('productSupplier/createOrUpdate', array('as' => 'productSupplier.createOrUpdate', 'uses' => 'ProductController@createOrUpdate'));

    Route::delete('productSupplier/{id}', array('as' => 'productSupplier.delete', 'uses' => 'ProductController@deleteProductSupplier'));

    Route::get('updateMaterialSortOrder/{id}/{sortOrder}', array('as' => 'material.UpdateSingleRec', 'uses' => 'OrderMaterialController@UpdateSingleRecSort'));

    Route::post('UpdateSort', array('as' => 'material.UpdateSort', 'uses' => 'OrderMaterialController@UpdateSort'));

    Route::get('getOnOrderDetails/{id}', array('as' => 'warehouseorder.getOnOrderDetails', 'uses' => 'WarehouseDetailsController@getOnOrderDetails'));

    Route::get('addLocation/product', array('as' => 'product.addLocation', 'uses' => 'ProductController@loadAddLocationForm'));

    Route::post('storeProductLocation', array('as' => 'product.storeProductLocation', 'uses' => 'ProductController@storeProductLocation'));

    Route::delete('productLocation/destroy/{id}', array('as' => 'productLocation.destroy', 'uses' => 'ProductController@deleteLocation'));

    Route::get('offer/lists', array('as' => 'offer.lists', 'uses' => 'OrderController@offerIndex'));

    Route::post('offer/listOfferMaterials/{id}', array('as' => 'ordermaterial.listOfferMaterials', 'uses' => 'OfferController@listOfferMaterials'));

    Route::get('offer/listOfferMaterials/{id}', array('as' => 'ordermaterial.listOfferMaterials', 'uses' => 'OfferController@listOfferMaterials'));

    Route::post('offermaterial/customStore', array('as' => 'offermaterial.customStore', 'uses' => 'OfferController@customStore'));

    Route::get('getSaleOrderDetails/{id}', array('as' => 'customer.getSaleOrderDetails', 'uses' => 'WarehouseDetailsController@getSaleOrderDetails'));

    Route::get('getPacklistBtnStatus/{order_id}', array('as' => 'order.getPacklistBtnStatus', 'uses' => 'OrderController@getPacklistBtnStatus'));

    Route::get('sendSupplierOrderMail/{order_id}', array('as' => 'warehouseorder.sendSupplierOrderMail', 'uses' => 'WarehouseOrderController@sendSupplierOrderMail'));

    Route::get('downloadLastPackList/{order_id}', array('as' => 'order.downloadLastPackList', 'uses' => 'OrderController@downloadLastPackList'));

    Route::get('apisetup/companies/list', array('as' => 'uni.getCompanyDetails', 'uses' => 'HomeController@getCompanyDetails'));

    Route::get('getSelect2Products/{type}', array('as' => 'orderMaterial.getSelect2Products', 'uses' => 'OrderMaterialController@getSelect2Products'));

    Route::get('syncUNIAccounts', array('as' => 'accplan.syncUNIAccounts', 'uses' => 'AccPlanController@syncUNIAccounts'));

    Route::get('syncSellers', array('as' => 'user.syncSellers', 'uses' => 'UserController@syncSellers'));

    Route::get('syncDepartment', array('as' => 'department.syncDepartment', 'uses' => 'DepartmentController@syncDepartment'));

    Route::get('syncCustomers', array('as' => 'customer.syncCustomers', 'uses' => 'CustomerController@syncCustomers'));

    Route::get('reporting', array('as' => 'reporting.createReport', 'uses' => 'ReportingController@createReport'));

    Route::get('reporting/{warehouse_id}', array('as' => 'reporting.getccsheetdates', 'uses' => 'ReportingController@getCCsheetDates'));

    Route::post('downloadReport', array('as' => 'reporting.downloadReport', 'uses' => 'ReportingController@downloadReport'));

    Route::post('storeInvoiceNumber', array('as' => 'ordermaterial.storeInvoiceNumber', 'uses' => 'OrderMaterialController@storeInvoiceNumber'));

    
});
Route::get('getCodeUni', array('as' => 'getCodeUni', 'uses' => 'HomeController@getCode'));