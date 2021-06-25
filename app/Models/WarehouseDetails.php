<?php
namespace App\Models;

use App\Models\WarehouseInventory;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Session;

class WarehouseDetails extends Model
{

    protected $table     = 'whs_inventory';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;

    protected $fillable = array('id', 'shortname', 'main', 'description', 'deleted_at');

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function warehouses()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function locations()
    {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id');
    }

    /**
     *    Get related warehouse inventory
     *     @return object
     **/
    public function whsInventory()
    {
        return $this->belongsTo('App\Models\WarehouseInventory', 'product_id', 'id');
    }

    /**
     *    Get related warehouse order details
     *     @return object
     **/
    public function warehouseOrderDetails()
    {
        return $this->belongsTo('App\Models\warehouseOrderDetails', 'product_id', 'id');
    }

    // get stock details
    public static function getStocks($conditions = false, $orderby = 'product_number', $order = 'asc', $warehouse_id = false, $warehouse_order_details = false, $product_id = false)
    {
        $search_by_warehouse = '';
        $search_query        = ' WHERE product.deleted_at is null';
        if ($warehouse_id) {
            $search_query .= " AND warehouse_id ='" . $warehouse_id . "'";
            $search_by_warehouse = " AND warehouse_id ='" . $warehouse_id . "'";
        }
        if ($product_id) {
            $search_query = ' WHERE product.id = "' . $product_id . '"';
        }
        $is_package   = 0;
        $search_query = ' WHERE product.is_package = "' . $is_package . '"';
        if (isset($conditions) && $conditions != '') {
            $search = $conditions;
            $search_query .= " AND (product_number LIKE  '%" . addslashes($search) . "%' OR description LIKE '%" . addslashes($search) . "%' OR stock_view.shortname LIKE '%" . addslashes($search) . "%') ";
        }
        $warehouse_stock_details = DB::select("SELECT product.id,description,product_number, stock_view.on_stock, on_order_view.on_order, on_order_view.pick_rest, whs.customer_order FROM product LEFT JOIN (SELECT SUM(qty) AS on_stock, product_id, warehouse_id,shortname from whs_inventory LEFT JOIN warehouse on warehouse.id = whs_inventory.warehouse_id  where  whs_inventory.deleted_at is null " . $search_by_warehouse . " GROUP BY product_id) AS stock_view ON stock_view.product_id = product.id LEFT JOIN (SELECT SUM(ordered_qty - received_qty) AS on_order, IFNULL(SUM(ordered_qty - picked_qty) ,0) AS pick_rest, product_id FROM warehouse_order_details WHERE ordered_qty - received_qty > 0 GROUP by product_id) AS on_order_view ON on_order_view.product_id = product.id left join (select product_id, sum(ordered) as customer_order from whs_inventory group by product_id ) as whs on product.id = whs.product_id" . $search_query . " order by product_number asc");
        $except_array            = array();
        for ($i = 0; $i < count($warehouse_stock_details); $i++) {
            $on_order = $warehouse_stock_details[$i]->on_order;
            if ($warehouse_stock_details[$i]->on_stock == 0 && $on_order == 0 || $warehouse_stock_details[$i]->on_stock == "" && $on_order == "" || $warehouse_stock_details[$i]->on_stock == "" && !isset($on_order)) {
                $except_array[] = $i;
            } else {
                $warehouse_stock_details[$i]->on_order = $on_order ? $on_order : "";
            }

        }
        foreach ($warehouse_stock_details as $key => $value) {
            if (in_array($key, $except_array)) {
                unset($warehouse_stock_details[$key]);
            }
        }
        $material_detail = OrderMaterial::selectRaw('*, sum(order_quantity) as sale_order_qty')->whereIn('product_number', collect($warehouse_stock_details)->pluck('id'))->where('approved_product', '!=', '1')->where('quantity', '<', 1)->groupBy('product_number')->get();
        $sale_orders     = $material_detail->pluck('sale_order_qty', 'product_number')->toArray();
        return ['warehouse_stock_details' => $warehouse_stock_details, 'sale_orders' => $sale_orders];
    }

    // apply pagination for warehouse details
    public static function paginateWarehouseDetails($results, $pageStart = 1, $warehouse_order_details)
    {
        $data    = array();
        $total   = count($results);
        $perPage = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        if (count($results)) {
            foreach ($results as $result) {
                $data[0][] = $result;
            }
            // Start displaying items from this number;
            $offSet = ($pageStart * $perPage) - $perPage;
            //Slice the collection to get the items to display in current page

            $currentPageSearchResults = array_slice($results, $offSet, count($results), true);

            $collection = new Collection($currentPageSearchResults);

            // Get only the items you need using array_slice
            $pagination[] = new LengthAwarePaginator($collection, 0, $total, Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));

            foreach ($data as $key => $value) {
                $itemsForCurrentPage = new Collection(array_slice($value, $offSet, $perPage, true));

                $pagination['all'] = new LengthAwarePaginator($itemsForCurrentPage, count($value), $perPage, Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
            }
            $pagination['total:protected'] = count($data);
            return $pagination;
        }
    }

    // get location and serial number
    public static function getLocationAndSerialNumber($warehouse_id = false, $product_id = false, $location_id = false)
    {
        try {
            if ($warehouse_id && $product_id) {
                if ($location_id) {
                    $product_details = $product = Product::where('id', '=', $product_id)->first();
                    if (@$product_details->sn_required == 1) {
                        $location_details = WarehouseDetails::selectRaw('*, sum(qty) as qty')->where('whs_inventory.product_id', '=', $product_id)->where(function ($query) {
                            $query->whereNull('ordered')->orWhere("ordered", 0);
                        })->with('locations')->where('warehouse_id', '=', $warehouse_id)->where("product_id", "=", $product_id)->where('location_id', '=', $location_id)->groupBy('warehouse_id', 'product_id', 'location_id')->first();
                    } else {
                        $location_details = WarehouseDetails::selectRaw('*, SUM(if(ordered is not null, qty - ordered, qty)) as qty')->with('locations')->where('warehouse_id', '=', $warehouse_id)->where("product_id", "=", $product_id)->where('location_id', '=', $location_id)->groupBy('warehouse_id', 'product_id', 'location_id')->first();
                    }
                    if ($location_details) {
                        return json_encode(array("status" => "success", "data" => $location_details, 'qty' => @$location_details->qty));
                    } else {
                        return json_encode(array("status" => "success", "data" => '', 'qty' => 0));
                    }
                } else {
                    $location_details = WarehouseDetails::selectRaw('*, sum(qty) as qty')->with('locations')->where('warehouse_id', '=', $warehouse_id)->where("product_id", "=", $product_id)->groupBy('location_id', 'serial_number')->get();
                    if ($location_details) {
                        $table_str = "";
                        foreach ($location_details as $key => $value) {
                            $table_str .= "<div class='row'>";
                            $table_str .= "<div class='col-sm-4'>" . $value->locations->name . "</div>";
                            $table_str .= "<div class='col-sm-4'>" . number_format($value->qty, 0) . "</div>";
                            $table_str .= "<div class='col-sm-4'>" . $value->serial_number . "</div>";
                            $table_str .= "</div>";
                        }
                        return json_encode(array("status" => "success", "data" => $table_str));
                    }
                }
            } else {
                return json_encode(array("status" => "error", "message" => trans('main.something_went_wrong')));
            }
        } catch (Exception $e) {
            return json_encode(array("status" => "error", "message" => trans('main.something_went_wrong')));
        }
    }

    // get product warehouses

    public static function getProductWarehouses($product_id, $warehouse_id = false, $return_type = 1)
    {
        try {
            $warehouse_order_details_search = '';
            $warehouse_inventory_search     = '';
            if ($warehouse_id) {
                $warehouse_order_details_search = " AND (source_whs_id ='" . $warehouse_id . "' OR destination_whs_id ='" . $warehouse_id . "')";
                $warehouse_inventory_search     = " AND whs_inventory.warehouse_id='" . $warehouse_id . "'";
            }
            if ($product_id) {
                $table_str       = "<div class='table-responsive'><table class='table table-striped table-hover stockTable'><tbody>";
                $product_details = DB::select("SELECT product_id,ordered_qty,picked_qty,received_qty,source_whs_id,destination_whs_id, shortname, coalesce(NULL,NULL) as location, coalesce(NULL,0) as qty, coalesce(0,0) as pic_qty, coalesce(0,0) as balance, sum(ordered_qty) - sum(received_qty) as rest, coalesce(0, 0) as location_id, warehouse.id as warehouse_id, product_id, coalesce(0, 0) as customer_order FROM warehouse_order_details LEFT JOIN warehouse ON warehouse.id = warehouse_order_details.destination_whs_id LEFT JOIN product ON product.id = warehouse_order_details.product_id where product_id = '" . $product_id . "' " . $warehouse_order_details_search . " GROUP BY product_id, destination_whs_id UNION ALL SELECT product_id,coalesce(0,0), coalesce(0,0),coalesce(0,0), coalesce(0,0), whs_inventory.warehouse_id,shortname, name  as location,SUM(CASE WHEN whs_inventory.deleted_at IS NULL  THEN qty ELSE 0 END) as 'qty', SUM(CASE WHEN  whs_inventory.deleted_at IS NOT NULL THEN qty ELSE 0 END) as 'pic_qty' , SUM(CASE WHEN  whs_inventory.deleted_at IS NULL THEN qty ELSE 0 END) as 'balance', (CASE WHEN sum(qty) = (SELECT sum(received_qty) from warehouse_order_details where source_whs_id = whs_inventory.warehouse_id and product_id = '" . $product_id . "'  " . $warehouse_inventory_search . " ) THEN (SELECT sum(ordered_qty-received_qty) from warehouse_order_details where source_whs_id = whs_inventory.warehouse_id and product_id = '" . $product_id . "' " . $warehouse_inventory_search . ") ELSE -(SELECT sum(ordered_qty-picked_qty) from warehouse_order_details where source_whs_id = whs_inventory.warehouse_id and product_id = '" . $product_id . "' " . $warehouse_inventory_search . ") END) as 'rest', whs_location.id as location_id, warehouse.id as warehouse_id, product_id, whs_inventory.ordered as customer_order FROM whs_inventory LEFT JOIN warehouse ON warehouse.id = whs_inventory.warehouse_id  LEFT JOIN whs_location on whs_location.id = whs_inventory.location_id where product_id = '" . $product_id . "' " . $warehouse_inventory_search . " GROUP BY whs_inventory.warehouse_id, location_id");
                if ($return_type == 2) {
                    return $product_details;
                }
                if ($product_details) {
                    $table_str .= WarehouseDetails::splitProductsByWarehouse($product_details);
                }
                $table_str .= "</tbody></table></div>";
                return $table_str;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function splitProductsByWarehouse($product_details)
    {
        try {
            $table_str     = '';
            $warehouse_ids = array();
            $table_str .= '<tr>';
            $table_str .= '<th width="20%">' . trans('main.warehouse') . '</th><th width="20%">' . trans('main.location') . '</th><th width="20%">' . trans('main.onstock') . '</th>';

            $table_str .= '<th width="20%">' . trans('main.customer_picked_qty') . '</th>';
            $table_str .= '<th width="20%">' . trans('main.available') . '</th>';
            $table_str .= '</tr>';
            foreach ($product_details as $key => $value) {
                $customer_order_view_id = $value->product_id . $value->location_id . $value->warehouse_id;
                if ($value->location_id) {
                    $customer_order = 0;
                    if ($value->location_id) {
                        $table_str .= '<tr data-toggle="collapse" data-target="#' . @$value->location_id . @$value->product_id . '" class="clickable open_serial_number_table stockTableTr" data-collapse-group="collapse_tr" product_id=' . $value->product_id . ' location_id=' . $value->location_id . ' warehouse_id=' . $value->warehouse_id . ' anc_tag_id="' . $customer_order_view_id . '"">';
                        $customer_order = OrderMaterial::where('product_number', $value->product_id)->where('location', $value->location_id)->where('warehouse', $value->warehouse_id)->where('approved_product', '!=', 1)->sum('quantity');

                    } else {
                        $table_str .= '<tr product_id=' . $value->product_id . ' >';
                    }
                    $table_str .= "<td>" . @$value->shortname . "</td>";
                    $table_str .= "<td>" . @$value->location . "</td>";
                    $table_str .= "<td>" . (($value->balance > 0) ? number_format($value->balance, 2, ',', ' ') : '') . "</td>";
                    $rest = '';
                    if (($value->qty - $value->balance) != number_format($value->pic_qty, 2, ',', ' ') || $value->qty == 0) {
                        $rest = $value->rest;
                    }

                    $table_str .= "<td class='customer_order_qty' customer_order_qty_val='2' customer_order=" . $customer_order . ">" . (($customer_order != 0) ? number_format($customer_order, 2, ',', ' ') : '') . "</td>";
                    $table_str .= "<td>" . number_format($value->balance - $value->customer_order, 2, ',', ' ') . "</td></tr>";
                }
            }
            return $table_str;
        } catch (Exception $e) {

        }
    }

/**
 *    Get warehouseDetailsData
 *    @param ids array
 *    @param warehosue_id string
 *    @return array
 **/
    public static function warehouseDetailsData($ids, $warehouse_id = false)
    {
        $warehouse_search = '';
        if ($warehouse_id) {
            $warehouse_search = " AND (warehouse.id ='" . $warehouse_id . "')";
        }

        $total_array = array();

        $ids = "'" . implode("','", $ids) . "'";

        $product_details = DB::select("SELECT product_id,curr_iso_name,ordered_qty,picked_qty,received_qty,source_whs_id,destination_whs_id, shortname, coalesce(NULL,NULL) as location, coalesce(NULL,0) as qty, coalesce(0,0) as pic_qty, coalesce(0,0) as balance, sum(ordered_qty) - sum(received_qty) as rest, product.product_number, product.description,product.list_price,product.vendor_price, coalesce(0, null) as location FROM warehouse_order_details LEFT JOIN warehouse ON warehouse.id = warehouse_order_details.destination_whs_id LEFT JOIN product ON product.id = warehouse_order_details.product_id where product_id IN (" . $ids . ") " . $warehouse_search . " GROUP BY product_id, destination_whs_id UNION ALL SELECT product_id,curr_iso_name,coalesce(0,0), coalesce(0,0),coalesce(0,0), coalesce(0,0), whs_inventory.warehouse_id,shortname, name  as location,SUM(CASE WHEN whs_inventory.deleted_at IS NULL  THEN qty ELSE 0 END) as 'qty', SUM(CASE WHEN  whs_inventory.deleted_at IS NOT NULL THEN qty ELSE 0 END) as 'pic_qty' , SUM(CASE WHEN  whs_inventory.deleted_at IS NULL THEN qty ELSE 0 END) as 'balance', (CASE WHEN  whs_inventory.deleted_at IS  NULL THEN IF(SUM(CASE WHEN  whs_inventory.deleted_at IS NOT NULL THEN qty ELSE 0 END) >0 , SUM(CASE WHEN  whs_inventory.deleted_at IS NOT NULL THEN qty ELSE 0 END) - (sum(qty) - (SELECT sum(picked_qty) from warehouse_order_details where whs_inventory.deleted_at is null and source_whs_id = whs_inventory.warehouse_id and ordered_qty != picked_qty and  product_id  IN (" . $ids . "))) , 0) ELSE 0 END) as 'rest', product.product_number, product.description, product.list_price,product.vendor_price, whs_location.name as location FROM whs_inventory LEFT JOIN warehouse ON warehouse.id = whs_inventory.warehouse_id LEFT JOIN product ON product.id = whs_inventory.product_id LEFT JOIN whs_location on whs_location.id = whs_inventory.location_id where  product_id  IN (" . $ids . ") " . $warehouse_search . " GROUP BY whs_inventory.product_id, whs_inventory.location_id");

        foreach ($product_details as $key => $value) {
            $stock_array = array();
            if ($value->balance != 0 || $value->rest != 0) {
                $stock_array[trans('main.product_number')] = '"' . @$value->product_number . '"';
                $stock_array[trans('main.description')]    = '"' . @$value->description . '"';
                $stock_array[trans('main.title')]          = '"' . @$value->shortname . '"';
                $stock_array[trans('main.location')]       = @$value->location && $value->location ? '"' . $value->location . '"' : '""';
                $list_price                                = $value->list_price;
                $vendor_price                              = $value->vendor_price;
                if ($value->curr_iso_name != 'NOK') {
                    $new_vendor_price = Currency::where('curr_iso_name', '=', $value->curr_iso_name)->orderBy('created_at', 'desc')->first();
                    if (@$new_vendor_price) {
                        $vendor_price = $new_vendor_price->exch_rate * $vendor_price;
                        $list_price   = $new_vendor_price->exch_rate * $list_price;
                    }
                }
                $stock_array[trans('main.list_price')]         = @$list_price ? '"' . number_format($list_price, 2, ',', ' ') . '"' : "0";
                $stock_array[trans('main.vendor_price')]       = @$vendor_price ? '"' . number_format($vendor_price, 2, ',', ' ') . '"' : "0";
                $stock_array[trans('main.available_quantity')] = '"' . number_format(@$value->balance, 2, ',', ' ') . '"';
                $stock_array[trans('main.onorder')]            = isset($value->rest) ? '"' . number_format(@$value->rest, 2, ',', ' ') . '"' : "0";

                $total_array[] = $stock_array;
            }
        }
        asort($total_array);
        return $total_array;
    }

/**
 * getProductSerialNumber
 * @param  string $product_id
 * @param  string $warehouse_id
 * @param  string $location_id
 * @return object
 */
    public static function getProductSerialNumber($product_id, $warehouse_id, $location_id)
    {
        if ($product_id && $warehouse_id && $location_id) {
            $wareinventoryDetails = WarehouseInventory::where('product_id', '=', $product_id)->where('warehouse_id', '=', $warehouse_id)->where('location_id', '=', $location_id)->where('serial_number', '!=', '')->where('qty', '!=', 0)->orderby('serial_number', 'asc')->get();
            $table_str            = '';
            if ($wareinventoryDetails) {
                $table_str = "<div class='table-responsive'><table class='table table-striped table-hover table-responsive'><tbody>";
                foreach ($wareinventoryDetails as $key => $value) {
                    $table_str .= "<tr><td class='col-sm-3'><a>" . @$value->serial_number . "</a></td></tr>";
                }
                $table_str .= "</tbody></table></div>";
            }
            return $table_str;
        } else {
            return false;
        }
    }

// Added By David
    /**
     * getProductActualQuantityByLocation
     * @param  string $product_id
     * @param  string $warehouse_id
     * @param  string $location_id
     * @return object
     */
    public static function getProductActualQuantityByLocation($product_id, $warehouse_id, $location_id)
    {
        $qty = 0;
        if ($product_id && $warehouse_id && $location_id) {
            $wareinventoryDetails = WarehouseInventory::selectRaw('SUM(qty) AS qty')->where('product_id', '=', $product_id)->where('warehouse_id', '=', $warehouse_id)->where('location_id', '=', $location_id)->groupBy('warehouse_id', 'location_id', 'product_id')->first();
            if (@$wareinventoryDetails) {
                $qty = $wareinventoryDetails->qty;
            }
            return $qty;
        } else {
            return $qty;
        }
    }

    /**
     * [constructOnOrderData description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function constructOnOrderData($id)
    {
        $result = array();
        $recs   = DB::table('warehouse_order_details')->where('product_id', $id)->get();
        if ($recs) {
            $i = 0;
            foreach ($recs as $key => $value) {
                if ($value->received_qty == null || $value->ordered_qty > $value->received_qty) {
                    $result[$i]['on_order']      = $value->ordered_qty - $value->received_qty;
                    $result[$i]['order_id']      = $value->whs_order_id;
                    $result[$i]['order_details'] = WarehouseOrder::find($value->whs_order_id);
                    $i++;
                }
            }
        }
        return $result;
    }

    /**
     * [constructSaleOrderData description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function constructSaleOrderData($product_id)
    {
        try {
            $order_material_data = OrderMaterial::selectRaw('order.id as id, order.order_number as order_number, order_material.order_quantity as quantity, order.order_date order_date')->leftjoin('order', 'order_material.order_id', '=', 'order.id')->where('order_material.product_number', "=", $product_id)->where('order_material.approved_product', '=', 0)->where('order_material.quantity', '<=', 0)->get();
            return $order_material_data;
        } catch (\Exception $e) {
            echo $e;
            exit();
        }
    }
}
