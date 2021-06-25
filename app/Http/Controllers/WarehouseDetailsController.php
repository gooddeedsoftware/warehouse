<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\WarehouseRequest;
use App\Models\Customer;
use App\Models\DropdownHelper;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseDetails;
use App\Models\WarehouseInventory;
use App\Models\WarehouseOrder;
use Illuminate\Http\Request;
use Redirect;
use Session;

class WarehouseDetailsController extends Controller
{

    protected $route     = 'main.warehouse.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.warehouse.notfound';
    protected $createmsg = 'main.warehouse.createsuccess';
    protected $updatemsg = 'main.warehouse.updatesuccess';
    protected $deletemsg = 'main.warehouse.deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($product_id = false)
    {
        $data = @Session::get('warehousedetails_search') ? @Session::get('warehousedetails_search') : [];
        @\Request::input() ? Session::put('warehousedetails_search', array_merge($data, @\Request::input())) : '';
        $warehouse_id = false;
        if (@Session::get('warehousedetails_search')['search_by_warehouse']) {
            $data['search_by_warehouse'] = $warehouse_id = @Session::get('warehousedetails_search')['search_by_warehouse'];
        }
        $data['suppliers'] = Customer::orderby('name', 'asc')->pluck('name', 'id');
        $data['warehouse'] = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
        $stock_sortorder   = "product_number";
        $stock_sortby      = "asc";
        // Stock details
        $orders         = WarehouseOrder::orderby('order_number', 'asc')->get();
        $product_result = WarehouseDetails::getStocks(@Session::get('warehousedetails_search')['stock_search'], $stock_sortorder, $stock_sortby, $warehouse_id, $orders, $product_id);
        $stocks         = $product_result['warehouse_stock_details'];
        $data['stock']  = array();
        if (@Session::get('warehousedetails_search')['show_on_ordered'] == 1) {
            foreach ($stocks as $key => $value) {
                if (@$value->on_order && $value->on_order > 0) {
                    $data['stock'][] = $value;
                }
            }
        } else {
            if (@$data['search_by_warehouse']) {
                foreach ($stocks as $key => $value) {
                    if (@$value->on_stock > 0) {
                        $data['stock'][] = $value;
                    }
                }
            } else {
                $data['stock'] = $stocks;
            }
        }
        $data['stocks']                 = WarehouseDetails::paginateWarehouseDetails($data['stock'], (\Request::get('page')) ? \Request::get('page') : 1, $orders);
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['priorities']             = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        $data['warehouse_order_status'] = DropdownHelper::where('groupcode', '=', '013')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        $data['sale_orders']            = $product_result['sale_orders'];
        return view('warehousedetails.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['warehouse']           = new Warehouse();
        $language                    = Session::get('language') ? Session::get('language') : 'no';
        $data['warehousemain_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return view('warehousedetails.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(WarehouseRequest $request)
    {
        $input       = $request->all();
        $input['id'] = GanticHelper::gen_uuid();
        Warehouse::create($input);
        return Redirect::route($this->route)->with($this->success, trans($this->createmsg));
    }

    public function show($id)
    {

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data['warehouse']           = Warehouse::findorFail($id);
        $language                    = Session::get('language') ? Session::get('language') : 'no';
        $data['warehousemain_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return view('warehouse.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(WarehouseRequest $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $input     = $request->all();
        $warehouse->fill($input);
        $warehouse->save();
        return Redirect::route($this->route)->with($this->success, trans($this->updatemsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        if (!$warehouse) {
            return Redirect::route($this->route)->with($this->error, trans($this->notfound));
        }
        $warehouse->delete();
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

    /*
     *   Get the location and serial number
     *   when qunatity is selected in stock
     *
     */
    public function getLocations()
    {
        try {
            $warehouse_id = \Request::get('warehouse_id');
            $product_id   = \Request::get('product_id');
            $location_id  = \Request::get('location_id');
            if ($warehouse_id) {
                echo WarehouseDetails::getLocationAndSerialNumber($warehouse_id, $product_id, $location_id);
                exit();
            } else {
                echo json_encode(array("status" => "error", "message" => "Invalid Warehouse"));
                exit();
            }
        } catch (Exception $e) {
            return json_encode(array("status" => "error", "message" => trans('main.something_went_wrong')));
        }
    }

    // get serial numbers fro selected locations
    public function getSerialNumbers()
    {
        try {
            $location_id           = Request::get('location_id');
            $product_id            = Request::get('product_id');
            $picked_quantity       = Request::get('picked_quantity');
            $serail_number_details = WarehouseInventory::getSerialNumbersFromLocation($location_id, $product_id, $picked_quantity);
            if ($serail_number_details) {
                echo json_encode(array("status" => 'success', 'data' => $serail_number_details));
                exit();
            } else {
                echo json_encode(array('status' => 'error', 'message' => trans('main.something_went_wrong')));
                exit();
            }
        } catch (Exception $e) {
            return json_encode(array("status" => "error", "message" => trans('main.something_went_wrong')));
        }
    }

    /**
     *   Get onstock details
     *   @return object
     **/
    public function getOnstockDetails()
    {
        $stock_id                  = \Request::get('stock_id');
        $warehouse_id              = \Request::get('warehouse_id');
        $orders                    = WarehouseOrder::orderby('order_number', 'asc')->get();
        $warehouse_product_details = WarehouseDetails::getProductWarehouses($stock_id, $warehouse_id);
        if ($warehouse_product_details) {
            echo json_encode(array("status" => 'success', 'data' => $warehouse_product_details));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => trans('main.something_went_wrong')));
            exit();
        }
    }

    /**
     * getSerialNumbersByLocation
     * @return json object
     */
    public function getSerialNumbersByLocation()
    {
        $product_id            = Request::get('product_id');
        $warehouse_id          = Request::get('warehouse_id');
        $location_id           = Request::get('location_id');
        $product_serial_number = WarehouseDetails::getProductSerialNumber($product_id, $warehouse_id, $location_id);
        if ($product_serial_number) {
            echo json_encode(array("status" => 'success', 'data' => $product_serial_number));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => trans('main.something_went_wrong')));
            exit();
        }
    }

    // Added by David
    /**
     * getProductActualQuantity
     * @return [type] [description]
     */
    public function getProductActualQuantity()
    {
        $product_id   = \Request::get('product_id');
        $warehouse_id = \Request::get('warehouse_id');
        $location_id  = \Request::get('location_id');
        $product_qty  = WarehouseDetails::getProductActualQuantityByLocation($product_id, $warehouse_id, $location_id);
        if ($product_qty) {
            echo json_encode(array("status" => 'success', 'data' => $product_qty));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => trans('main.product.notfound')));
            exit();
        }

    }

    /**
     * [getOnOrderDetails description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getOnOrderDetails($id)
    {
        $data['records'] = WarehouseDetails::constructOnOrderData($id);
        return view('warehousedetails.on_order_info', $data);
    }

    /**
     * [getSaleOrderDetails description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getSaleOrderDetails($id)
    {
        try {
            $data['customer_orders'] = WarehouseDetails::constructSaleOrderData($id);
            return view('warehousedetails.customer_order', $data);
        } catch (\Exception $e) {
            echo $e;
            exit();
        }
    }

}
