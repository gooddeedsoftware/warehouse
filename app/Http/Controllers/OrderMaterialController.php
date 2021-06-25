<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Models\BillingData;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseDetails;
use Redirect;
use Request;
use Session;

class OrderMaterialController extends Controller
{

    protected $folder    = 'order_material';
    protected $route     = 'main.product.index';
    protected $title     = 'main.product';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.product_createsuccess';
    protected $updatemsg = 'main.product_updatesuccess';
    protected $deletemsg = 'main.product_deletesuccess';
    protected $product   = 'main.product';

    /**
     * listOrderMaterials
     * @param  string $order_id
     * @return object
     */
    public function listOrderMaterials($order_id)
    {
        $data = @Session::get('product_search') ? @Session::get('product_search') : [];
        @\Request::input() ? Session::put('product_search', array_merge($data, @\Request::input())) : '';
        $data = OrderMaterial::getOrderMaterials(Session::get('product_search'), $order_id);
        return view('ordermaterial.index', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $ordermaterial = OrderMaterial::where('id', '=', $id)->first();
        if (!$ordermaterial) {
            return Redirect::back()->with($this->error, __($this->notfound));
        }
        if ($ordermaterial->is_package == 1) {
            $package_ordermaterial = OrderMaterial::where('reference_id', '=', $ordermaterial->id)->get();
            foreach ($package_ordermaterial as $key => $value) {
                OrderMaterial::deleteOrderMaterial($value); //this function does the work of reseting the quantity in warehouse
                OrderMaterial::where('id', '=', $value->id)->delete();
            }
        } else {
            OrderMaterial::deleteOrderMaterial($ordermaterial); //this function  does the work of reseting the quantity in warehouse
        }
        $ordermaterial->delete();
        return Redirect::back()->with($this->success, __($this->deletemsg));
    }

    // update group of products(It is not yet approved)
    public function approveOrderMaterials()
    {
        $material_ids              = Request::get('approved_product_ids');
        $material_invoice_quantity = Request::get('approved_product_invoice_quantity');
        $change_invoice_quantity   = 1;
        if ($material_ids) {
            OrderMaterial::updateOrderMaterials($material_ids, $material_invoice_quantity, $change_invoice_quantity, 1);
        }
        return Redirect::back();
    }

    /**
     * [customStore description]
     * @return [type] [description]
     */
    public function customStore()
    {
        try {
            $input                  = Request::all();
            $order_material_details = OrderMaterial::storeOrderMaterial($input);
            if ($order_material_details) {
                return json_encode(array("status" => "success", "data" => $order_material_details));
            } else {
                return json_encode(array("status" => "error", "message" => __('main.something_went_wrong')));
            }
        } catch (Exception $e) {
            return json_encode(array("status" => "error", "message" => __('main.something_went_wrong')));
        }
    }

    /**
     * [productDetails description]
     * @return [type] [description]
     */
    public function productDetails()
    {
        try {
            $product_id   = Request::get('product_id');
            $order_type   = Request::get('order_type');
            $warehouse_id = Request::get('warehouse_id');
            if ($product_id) {
                $product_details = OrderMaterial::getProductDetail($product_id, $order_type, $warehouse_id);
                if ($product_details) {
                    echo json_encode(array("status" => "success", "data" => $product_details));
                } else {
                    echo json_encode(array("status" => "error", "data" => "No data found"));
                }
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }

    /**
     * [getProductAvailabeQuantity description]
     * @return [type] [description]
     */
    public function getProductAvailabeQuantity()
    {

        $product_id   = Request::get('product_id');
        $order_type   = Request::get('order_type');
        $warehouse_id = Request::get('warehouse_id');
        $location_id  = Request::get('location_id');
        if ($product_id) {
            $product_details = OrderMaterial::getProductQuantityDetail($product_id, $order_type, $warehouse_id, $location_id);
            echo json_encode(array("status" => "success", "data" => $product_details));
        }
    }

    /**
     * [getReturnProduct description]
     * @param  boolean $order_id [description]
     * @return [type]            [description]
     */
    public function getReturnProduct($order_id = false)
    {
        try {
            $data['materials']  = OrderMaterial::ConstrtuctReturnOrderMaterials($order_id);
            $data['warehouses'] = Warehouse::orderBy('shortname', 'asc')->pluck('shortname', 'id');
            return view('ordermaterial.return_material', $data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

    /**
     * [createReturnOrder description]
     * @return [type] [description]
     */
    public function createReturnOrder()
    {
        try {
            $selected_materials = Request::get('selected_materials');
            $order_id           = Request::get('order_id');
            $result             = OrderMaterial::createReturnOrder(json_decode($selected_materials), $order_id);
            if ($result == 1) {
                return Redirect::back()->with('success', __('main.return_order_create_msg'));
            } else {
                return Redirect::back()->with('error', __('main.something_went_wrong'));
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

    /**
     * [openBillingDataView description]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public function openBillingDataView($order_id)
    {
        try {
            $data = OrderMaterial::getBillingData($order_id);
            return view('ordermaterial.billing_data', $data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

    /**
     * [storeBilllingData description]
     * @return [type] [description]
     */
    public function storeBilllingData()
    {
        try {
            $data               = Request::all();
            $store_billing_data = BillingData::saveOrUpdate($data);
            echo json_encode(array("status" => "success", "token" => csrf_token()));
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }

    /**
     * [getOnstockDetails description]
     * @return [type] [description]
     */
    public function getOnstockDetails()
    {
        $stock_id                = \Request::get('stock_id');
        $warehouse_id            = \Request::get('warehouse_id');
        $unique_id               = \Request::get('unique_id');
        $warehouseStatus         = \Request::get('warehouseStatus');
        $data                    = OrderMaterial::constrtuctOnstockRec(WarehouseDetails::getProductWarehouses($stock_id, $warehouse_id, 2));
        $data['unique_id']       = $unique_id;
        $data['warehouseStatus'] = $warehouseStatus;
        $stockView               = \View::make('ordermaterial.stockView', $data);
        $html                    = $stockView->render();
        if ($html) {
            echo json_encode(array("status" => 'success', 'data' => $html));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => trans('main.something_went_wrong')));
            exit();
        }
    }

    /**
     * [getWarehouseOption description]
     * @return [type] [description]
     */
    public function getWarehouseOption()
    {
        $product_id = \Request::get('product_id');
        $result     = OrderMaterial::constructWhsDropdown($product_id);
        echo json_encode(array("status" => 'success', 'data' => $result));
    }

    /**
     * [storeText description]
     * @return [type] [description]
     */
    public function storeText()
    {
        $result = OrderMaterial::storeText(Request::all());
        echo json_encode(array("status" => 'success', 'data' => $result));
    }

    /**
     * [UpdateSingleRecSort description]
     * @param [type] $id         [description]
     * @param [type] $sort_value [description]
     */
    public function UpdateSingleRecSort($id, $sort_value)
    {
        $result = OrderMaterial::whereId($id)->update(['sortorderval' => $sort_value]);
        echo json_encode(array("status" => 'success', 'data' => $result));
    }

    /**
     * [UpdateSort description]
     */
    public function UpdateSort()
    {
        $data     = Request::all();
        $material = json_decode($data['data']);
        foreach ($material as $key => $value) {
            OrderMaterial::whereId($value->id)->update(['sortorderval' => $value->sortVal]);
        }
        echo json_encode(array("status" => 'success', 'data' => []));
    }

    /**
     * [getSelect2Products description]
     * @return [type] [description]
     */
    public function getSelect2Products($type)
    {
        $products = collect();
        try {
            if (Request::get('q')) {
                $products = Product::getProductForSelect2(Request::get('q'), $type);
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
        }
        return response()->json($products);
    }

    /**
     * [storeInvoiceNumber description]
     * @return [type] [description]
     */
    public function storeInvoiceNumber()
    {
        $data = Request::all();
        Order::where('id', $data['order_id'])->update(['invoice_number' => $data['invoice_number']]);
        echo json_encode(array("status" => 'success', 'data' => []));
    }
}
