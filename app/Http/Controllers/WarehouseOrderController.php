<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\WarehouseOrderRequest;
use App\Jobs\WarehouseNotification;
use App\Models\CCSheet;
use App\Models\DropdownHelper;
use App\Models\OfferSettings;
use App\Models\Product;
use App\Models\WarehouseInventory;
use App\Models\WarehouseOrder;
use App\Models\WarehouseOrderDetails;
use Input;
use Redirect;
use Request;
use Response;
use Session;

class WarehouseOrderController extends GanticController
{

    protected $route     = 'main.warehouseorder.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.warehouseorder_createsuccess';
    protected $updatemsg = 'main.warehouseorder_updatesuccess';
    protected $deletemsg = 'main.warehouseorder_deletesuccess';
    private $warehouseOrderObj;

    public function __construct(WarehouseOrder $warehouseOrderObj)
    {
        $this->warehouseOrderObj = $warehouseOrderObj;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('warehousedetails_order_search') ? @Session::get('warehousedetails_order_search') : [];
        @\Request::input() ? Session::put('warehousedetails_order_search', array_merge($data, @\Request::input())) : '';
        $warehouse_order_sort_by        = Request::get('order') && Request::get('warehouse') ? Request::get('order') : 'desc';
        $data['orders']                 = WarehouseOrder::getWarehouseOrderDetails(@Session::get('warehousedetails_order_search'), 'order_number', $warehouse_order_sort_by, @Session::get('warehousedetails_order_search')['search_by_order_type'], @Session::get('warehousedetails_order_search')['search_by_order_status']);
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['warehouse_order_types']  = DropdownHelper::where('groupcode', '=', '014')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        $data['warehouse_order_status'] = DropdownHelper::where('groupcode', '=', '013')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        return view('warehousedetails/order/index', $data);

    }

    /**
     * [createSupplierOrder description]
     * @return [type] [description]
     */
    public function createSupplierOrder()
    {
        $data                  = WarehouseOrder::getDatasForSupplierOrder();
        $data['standard_text'] = OfferSettings::whereType(3)->select('data')->first();
        return view('warehousedetails/order/partials/supplier_order', $data);
    }

    /**
     * [editSupplierOrder description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function editSupplierOrder($id)
    {
        $data        = WarehouseOrder::getDatasForSupplierOrder($id);
        $data['btn'] = trans('main.update');
        return view('warehousedetails/order/partials/supplier_order', $data);
    }

    /**
     * [storeSupplierOrder description]
     * @param  WarehouseOrderRequest $request [description]
     * @return [type]                         [description]
     */
    public function storeSupplierOrder(WarehouseOrderRequest $request)
    {
        $input                 = $request->all();
        $input['id']           = GanticHelper::gen_uuid();
        $input['order_number'] = WarehouseOrder::getWarehouseOrderNumber();
        $input['order_date']   = GanticHelper::formatDate($input['order_date'], 'Y-m-d');
        $input['added_by']     = Session::get('currentUserID');
        $warehouse_order       = WarehouseOrder::create($input);
        if ($input['order_status'] == 2) {
            WarehouseInventory::saveSupplierInventory($input['product_details'], $input['order_type'], $input['id']);
            $language = Session::get('language') ? Session::get('language') : 'no';
            $this->dispatch(new WarehouseNotification($language, 3, $input['id'], Session::get('currentUserID')));
        }
        return Redirect::action('WarehouseOrderController@editSupplierOrder', [$warehouse_order->id])->with($this->success, trans($this->createmsg));
    }

    /**
     * [updateSupplierOrder description]
     * @param  WarehouseOrderRequest $request [description]
     * @param  [type]                $id      [description]
     * @return [type]                         [description]
     */
    public function updateSupplierOrder(WarehouseOrderRequest $request, $id)
    {
        $warehouseOrder = WarehouseOrder::find($id);
        $input          = $request->all();
        if ($input['order_status'] != 5) {
            if (isset($input['order_date'])) {
                $input['order_date'] = GanticHelper::formatDate($input['order_date'], 'Y-m-d');
            }
            $input['updated_by'] = Session::get('currentUserID');
            $warehouseOrder->fill($input);
            $warehouse = isset($input['warehouse']) ? $input['warehouse'] : "";
            if ($input['order_status'] >= 2) {
                WarehouseInventory::saveSupplierInventory($input['product_details'], $input['order_type'], $id);
            }
            $warehouseOrder->save();
            if ($input['order_status'] >= 3) {
                WarehouseInventory::compareOrderAndReceiveQty($input['product_details'], $input['order_type'], $id);
            }
            if ($input['order_status'] == 2) {
                $language = Session::get('language') ? Session::get('language') : 'no';
                $this->dispatch(new WarehouseNotification($language, 3, $id, Session::get('currentUserID')));
            }
        }
        if ($input['submit_button_value'] == 0) {
            return Redirect::back()->with($this->success, trans($this->updatemsg));
        } else {
            return Redirect::route('main.warehouseorder.index')->with($this->success, trans($this->updatemsg));
        }
    }

    /**
     * [createAdjustmentOrder description]
     * @return [type] [description]
     */
    public function createAdjustmentOrder()
    {
        $data = WarehouseOrder::getDatasForAdjustmentOrder();
        return view('warehousedetails/order/partials/_form', $data);

    }

    /**
     * [storeAdjustmentOrder description]
     * @param  WarehouseOrderRequest $request [description]
     * @return [type]                         [description]
     */
    public function storeAdjustmentOrder(WarehouseOrderRequest $request)
    {
        $input                 = $request->all();
        $input['id']           = GanticHelper::gen_uuid();
        $input['order_number'] = WarehouseOrder::getWarehouseOrderNumber();
        $input['order_date']   = GanticHelper::formatDate($input['order_date'], 'Y-m-d');
        $input['added_by']     = Session::get('currentUserID');
        if ($input['order_status'] == 3) {
            $input['order_status'] = 5;
        }
        $warehouse       = isset($input['warehouse']) ? $input['warehouse'] : "";
        $warehouse_order = WarehouseOrder::create($input);
        $warehouse_order->order_status == 5 ? WarehouseInventory::saveAdjustmentnventory($warehouse_order) : '';
        if (@$request->get('ccsheet_id') && $request->get('ccsheet_id') != '') {
            CCSheet::updateOrderNumberInCCSheet($request->get('ccsheet_id'), $input['id']);
        }

        return Redirect::action('WarehouseOrderController@editAdjustmentOrder', [$warehouse_order->id])->with($this->success, trans($this->createmsg));

    }

    /**
     * [editAdjustmentOrder description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function editAdjustmentOrder($id)
    {
        $data        = WarehouseOrder::getDatasForAdjustmentOrder($id);
        $data['btn'] = trans('main.update');
        return view('warehousedetails/order/partials/_form', $data);
    }

    /**
     * [updateAdjustmentOrder description]
     * @param  WarehouseOrderRequest $request [description]
     * @param  [type]                $id      [description]
     * @return [type]                         [description]
     */
    public function updateAdjustmentOrder(WarehouseOrderRequest $request, $id)
    {
        $warehouseOrder      = WarehouseOrder::find($id);
        $input               = $request->all();
        $input['order_date'] = isset($input['order_date']) ? GanticHelper::formatDate($input['order_date'], 'Y-m-d') : null;
        if ($input['order_status'] == 3) {
            $input['order_status'] = 5;
        }
        $input['updated_by'] = Session::get('currentUserID');
        $warehouseOrder->fill($input);
        $warehouseOrder->save();
        $warehouseOrder->order_status == 5 ? WarehouseInventory::saveAdjustmentnventory($warehouseOrder) : '';
        if ($input['submit_button_value'] == 0) {
            return Redirect::back()->with($this->success, trans($this->updatemsg));
        } else {
            return Redirect::route('main.warehouseorder.index')->with($this->success, trans($this->updatemsg));
        }
    }

    /*  transfer order create
     *
     */
    public function createTransferOrder()
    {
        $data = WarehouseOrder::getDatasToCreateOrders();
        return view('warehousedetails/order/partials/transfer_order', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(WarehouseOrderRequest $request)
    {
        $input                 = $request->all();
        $input['id']           = GanticHelper::gen_uuid();
        $input['order_number'] = WarehouseOrder::getWarehouseOrderNumber();
        $input['order_date']   = GanticHelper::formatDate($input['order_date'], 'Y-m-d');
        $input['added_by']     = Session::get('currentUserID');
        $warehouse_order       = WarehouseOrder::create($input);
        $warehouse             = isset($input['warehouse']) ? $input['warehouse'] : "";
        if ($input['order_status'] == 7) {
            $language = Session::get('language') ? Session::get('language') : 'no';
            $this->dispatch(new WarehouseNotification($language, 1, $input['id'], Session::get('currentUserID')));
        }
        return Redirect::action('WarehouseOrderController@editTransferOrder', [$warehouse_order->id])->with($this->success, trans($this->createmsg));
    }

    /**
     * [editTransferOrder description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function editTransferOrder($id)
    {
        $data        = WarehouseOrder::getDatasToCreateOrders($id, 1);
        $data['btn'] = trans('main.update');
        return view('warehousedetails/order/partials/transfer_order', $data);
    }

    /**
     * [returnOrder description]
     * @return [type] [description]
     */
    public function editReturnOrder($id)
    {
        try {
            $data                       = WarehouseOrder::getDatasToCreateOrders($id);
            $data['product_details']    = $data['warehouseorder']->product_details ? json_decode($data['warehouseorder']->product_details) : [];
            $data['product_drop_downs'] = Product::productDetails();
            $data['btn']                = trans('main.update');
            return view('warehousedetails/order/partials/return_order', $data);
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(WarehouseOrderRequest $request, $id)
    {
        $warehouseOrder = WarehouseOrder::find($id);
        $input          = $request->all();
        if ($input['order_status'] != 5) {
            if (isset($input['order_date'])) {
                $input['order_date'] = GanticHelper::formatDate($input['order_date'], 'Y-m-d');
            }
            $input['updated_by'] = Session::get('currentUserID');
            $warehouseOrder->fill($input);
            $warehouse = isset($input['warehouse']) ? $input['warehouse'] : "";
            if ($input['order_status'] >= 2 && $input['order_status'] != "7") {
                WarehouseInventory::saveInventory($input['product_details'], $warehouse, @$input['source_warehouse'], @$input['destination_warehouse'], $input['order_type'], $id);
            }
            $warehouseOrder->save();
            if ($input['order_status'] >= 3 && $input['order_status'] != "7") {
                WarehouseInventory::compareOrderAndReceiveQty($input['product_details'], $input['order_type'], $id);
            }
            $language = Session::get('language') ? Session::get('language') : 'no';
            $usertype = Session::get('usertype');
        }
        if ($input['order_status'] == 7 || $input['order_status'] == 9) {
            $language = Session::get('language') ? Session::get('language') : 'no';
            $this->dispatch(new WarehouseNotification($language, 1, $id, Session::get('currentUserID')));
        }
        if ($input['submit_button_value'] == 0) {
            return Redirect::back()->with($this->success, trans($this->updatemsg));
        } else {
            return Redirect::route('main.warehouseorder.index')->with($this->success, trans($this->updatemsg));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $warehouseOrder = WarehouseOrder::find($id);
        if (!$warehouseOrder) {
            return Redirect::route($this->route)->with($this->error, trans($this->notfound));
        }
        WarehouseOrderDetails::where('whs_order_id', $id)->forceDelete();
        $warehouseOrder->delete();
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

    /**
     *   download warehouse report
     *   @param id string
     *   @return file
     **/
    public function downloadWarehouseReport($id)
    {
        if ($id) {
            $warehouseOrder = new WarehouseOrder();
            $file           = $warehouseOrder->createWarehouseReport($id);
            if ($file['pdf']) {
                $headers = array('Content-Type: application/pdf');
                return Response::download($file['pdf'], 'warehouse_' . $file['fileName'] . '.pdf', $headers);
            } else {
                return Redirect::back()->with($this->error, $this->notfound);
            }
        } else {
            return Redirect::back()->with($this->error, $this->notfound);
        }
    }

    /**
     * updateStatusToArchive description
     * @return [type] [description]
     */
    public function updateStatusToArchive()
    {
        $order_id_val  = Request::get('order_id_val');
        $update_status = WarehouseOrder::where('id', $order_id_val)->update(array('order_status' => '6'));
        if ($update_status) {
            echo json_encode(array("status" => 'success', 'data' => $order_id_val));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => trans('main.something_went_wrong')));
            exit();
        }
    }

    /**
     * [sendSupplierOrderMail description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function sendSupplierOrderMail($id)
    {
        try {
            $this->warehouseOrderObj->sendSupplierOrderMailToUser($id);
            return Redirect::back()->with($this->success, __('main.emailsent'));
        } catch (\Exception $e) {
            return Redirect::back()->with($this->error, __('main.something_went_wrong'));
        }
    }

}
