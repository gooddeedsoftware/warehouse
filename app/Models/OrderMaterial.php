<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderMaterialInventory;
use App\Models\PrinterDetail;
use App\Models\Product;
use App\Models\WarehouseInventory;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class OrderMaterial extends Model
{

    protected $table     = 'order_material';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;

    protected $dates    = ['deleted_at'];
    protected $fillable = array('id', 'product_number', 'warehouse', 'quantity', 'invoice_quantity', 'date', 'userid', 'order_id', 'deleted_at', 'description', 'location', 'sn_required', 'invoiced', 'order_quantity', 'is_package', 'reference_id', 'sort_number', 'package_quantity', 'discount', 'offer_sale_price', 'is_offerorder', 'return_quantity', 'approved_product', 'mail_notification_status', 'order_offer_product_id', 'delivery_date', 'shippment_id', 'product_text', 'is_text', 'text_ref_id', 'uni_status', 'sent_id', 'is_logistra', 'track_number', 'sortorderval',
        'vat', 'sum_ex_vat', 'unit', 'product_description', 'cost_price', 'dg', 'stockable', 'prod_nbr');

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id');
    }

    public function warehouses()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse', 'id');
    }

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_number');
    }

    public function warehouseLocation()
    {
        return $this->belongsTo('App\Models\Location', 'location', 'id');
    }

    // List order_materials (search, sort)
    public static function getOrderMaterials($conditions = false, $order_id = false, $orderby = 'product.product_number', $order = 'asc', $is_offer = 0)
    {
        $order_material = OrderMaterial::select('order_material.*', 'product.product_number', 'product.description', 'product.id as product_id', 'customer.customer as customer_no')->whereNull('text_ref_id');
        $order_material->orderBy('sortorderval', 'asc', );
        $order_material->leftjoin('user', 'userid', '=', "user.id");
        $order_material->leftjoin('product', 'product.id', '=', "order_material.product_number");
        $order_material->leftjoin('customer', 'product.supplier_id', '=', "customer.id");
        $search = @$conditions['product_search'];
        if ($is_offer == 1) {
            $search = @$conditions['offer_product_search_form'];
        }
        if (isset($search) && $search != '') {
            $order_material->where(function ($query) use ($search) {
                $query->orwhere('product.product_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('product.description', 'LIKE', '%' . $search . '%');
                $query->orwhere('quantity', 'LIKE', '%' . $search . '%');
                $query->orwhere('invoice_quantity', 'LIKE', '%' . $search . '%');
                $query->orwhereHas('user', function ($query) use ($search) {
                    $query->where('user.first_name', 'LIKE', '%' . $search . '%');
                });
                $query->orwhereHas('warehouses', function ($query) use ($search) {
                    $query->where('warehouse.shortname', 'LIKE', '%' . $search . '%');
                });
            });
        }
        if ($order_id) {
            $order_material->where('order_id', '=', $order_id);
        }
        $order_material->where(function ($query) {
            $query->where('reference_id', '=', '');
            $query->orWhereNull('reference_id');
        });
        $order_material            = $order_material->get();
        $has_non_approved_material = false;
        for ($i = 0; $i < count($order_material); $i++) {
            if ($order_material[$i]->is_package == 1) {
                $order_material[$i]->package_contents = '';
                $package_contents                     = OrderMaterial::select('order_material.*', 'product.product_number', 'product.description', 'product.id as product_id', 'customer.customer as customer_no')->where('reference_id', '=', $order_material[$i]->id)->leftjoin('user', 'userid', '=', "user.id")->leftjoin('product', 'product.id', '=', "order_material.product_number")->leftjoin('customer', 'product.supplier_id', '=', "customer.id")->orderBy('order_material.sort_number')->get();
                if (@$package_contents) {
                    $order_material[$i]->package_contents = $package_contents;
                }

            }
            if ($order_material[$i]->approved_product != 1) {
                $has_non_approved_material = true;
            }
        }
        $order_material->has_non_approved_material = $has_non_approved_material;
        $data                                      = OrderMaterial::getDatasForMaterial($order_id, $is_offer);
        $data['order_materials']                   = $order_material;
        return $data;
    }

    /**
     * [getDatasForMaterial description]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public static function getDatasForMaterial($order_id, $is_offer)
    {
        $usertype          = $data['usertype']          = Session::get('usertype');
        $user_id           = $data['user_id']           = Session::get('currentUserID');
        $data['warehouse'] = json_encode(Warehouse::orderBy('shortname', 'asc')->pluck('shortname', 'id'));
        $data['products']  = json_encode([]);
        if ($is_offer) {
            $data['products'] = json_encode( /*Product::retrieveAllProductsFromInventory($is_offer)*/[]);
        }
        $data['product_packages']         = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS name, id"))->orderBy("product_number", "asc")->where('is_package', '=', 1)->pluck("name", "id");
        $data['user_warehouse']           = Warehouse::leftjoin('warehouse_responsible', 'warehouse_responsible.warehouse_id', '=', 'warehouse.id')->where('warehouse_responsible.user_id', '=', $user_id)->orderby('shortname', 'asc')->pluck('shortname', 'id');
        $data['warehouse_dropdown_array'] = Warehouse::orderBy('shortname', 'asc')->pluck('shortname', 'id');
        if ($usertype != "Admin" && $usertype != "Administrative") {
            if (count($data['user_warehouse']) > 0) {
                $data['warehouse_dropdown_array'] = $data['user_warehouse'];
            } else {
                $data['warehouse_dropdown_array'] = Warehouse::orderBy('shortname', 'asc')->Where('main', 1)->pluck('shortname', 'id');
            }
        }
        if (@$data['user_warehouse']) {
            foreach (@$data['user_warehouse'] as $key => $value) {
                $data['user_warehouse_resposible_id'] = $key;
                break;
            }
            $data['user_warehouse_resposible'] = 1;
        } else {
            $data['user_warehouse_resposible']    = 0;
            $data['user_warehouse_resposible_id'] = '';
        }
        $data['order_id']                   = $order_id;
        $data['has_non_approved_materials'] = OrderMaterial::where('order_id', '=', $order_id)->where('approved_product', '=', 0)->first();
        $data['show_billing_data']          = 0;
        $data['order_details']              = $data['orders']              = Order::where('id', '=', $order_id)->with('customer')->first();
        $data['customerName']               = Customer::find(@$data['orders']->customer_id)->name;
        if ($usertype == "Admin" || $usertype == "Administrative" || $user_id == @$data['order_details']->order_user->user_id) {
            $data['show_billing_data'] = 1;
        }
        $data['units']             = Order::getUnits();
        $data['offer_order_units'] = json_encode($data['units']);
        $data['senders']           = LogistraDetails::where('status', 1)->pluck('name', 'id');
        $data['printers']          = PrinterDetail::pluck('name', 'id');
        $data['all_senders']       = LogistraDetails::pluck('name', 'id');
        $data['all_prodducts']     = Product::select(DB::Raw("product.*, CONCAT(product_number, ' - ', IFNULL(description, '')) AS product_text"))->orderBy('product_number', 'asc')->pluck('product_text', 'product.id')->toArray();
        return $data;
    }

    /**
     * applySearch
     * @param  object &$data
     * @param  object $input
     * @return object
     */
    public static function applySearch(&$data, $input)
    {

        if (@$input['product_search']) {
            Session::put('product_search', $input);
            $data['product_search_string'] = @$input['search'];
        }
        if (@$input['product_search'] == '') {
            Session::put('product_search', '');
        }

        if (Session::get('product_search')) {
            $input                         = Session::get('product_search');
            $data['product_search_string'] = isset($input['product_search']) ? $input['product_search'] : "";
            Session::put('product_search', $input);
        }
        return $data;
    }

    // update approved order_material (update the selected order_material)
    public static function updateOrderMaterials($order_material_ids, $update_invoice_quantity = false, $change_invoice_quantity = false, $type = false)
    {
        try {
            $order_material_ids           = explode(",", $order_material_ids);
            $separated_order_material_ids = "'" . implode("','", $order_material_ids) . "'";
            DB::statement("UPDATE  order_material SET approved_product = 1 WHERE id IN($separated_order_material_ids)");
            OrderMaterial::updateOfferOrderProduct($order_material_ids);
            $order_material_details = OrderMaterial::findOrFail($order_material_ids[0]);
            if ($order_material_details) {
                $order_id = $order_material_details->order_id;
            }
            $customer_order_details = Order::where('id', '=', $order_id)->first();

            if ($change_invoice_quantity == 1) {
                foreach ($order_material_ids as $key => $value) {
                    $order_material_details = OrderMaterial::where('id', '=', $value)->first();
                    if (@$order_material_details) {
                        if ($order_material_details->is_package == 1) {
                            $content_products = OrderMaterial::where('reference_id', '=', $value)->get();
                            if (@$content_products) {
                                foreach ($content_products as $keys => $value) {
                                    $content_array[$keys . $value->id]['product_id']       = $value->id;
                                    $content_array[$keys . $value->id]['material_id']      = $value->id;
                                    $content_array[$keys . $value->id]['invoice_quantity'] = $value->quantity;
                                }
                            }
                        }
                    }
                }
                if (@$content_array && $type == 1) {
                    OrderMaterial::updateContentProducts($content_array, $change_invoice_quantity, $order_id);
                }
            }
            // update invoice_quantity
            if ($update_invoice_quantity) {
                try {
                    $decoded_invoice_quantity = json_decode($update_invoice_quantity);
                    if ($decoded_invoice_quantity) {
                        foreach ($decoded_invoice_quantity as $key => $value) {
                            $delivery_date = @$value->delivery_date ? GanticHelper::formatDate(@$value->delivery_date, 'Y-m-d') : null;
                            if ($delivery_date) {
                                OrderMaterial::where('id', $value->material_id)->update(['delivery_date' => $delivery_date]);
                            }
                            if (isset($value->invoice_quantity) && $value->invoice_quantity != '') {
                                $invoice_quantity = str_replace(",", ".", $value->invoice_quantity);
                                DB::statement("UPDATE  order_material SET invoice_quantity = '" . $invoice_quantity . "' WHERE id ='" . $value->product_id . "'");
                            } else {
                                DB::statement("UPDATE  order_material SET invoice_quantity = NULL WHERE id ='" . $value->product_id . "'");
                            }
                            $product_details = OrderMaterial::where('id', '=', $value->product_id)->first();
                            if ($product_details) {
                                $location_id                 = $product_details->location;
                                $warehouse_id                = $product_details->warehouse;
                                $product_id                  = $product_details->product_number;
                                $warehouse_inventory_details = WarehouseInventory::where('product_id', '=', $product_id)->where('location_id', '=', $location_id)->where('warehouse_id', '=', $warehouse_id)->first();
                                if ($warehouse_inventory_details) {
                                    $ordered = $warehouse_inventory_details->ordered - $product_details->quantity;
                                    $qty     = $warehouse_inventory_details->qty - $product_details->quantity;
                                    WarehouseInventory::find($warehouse_inventory_details->id)->update(['ordered' => $ordered, 'qty' => $qty, 'delivered' => $warehouse_inventory_details->delivered + $product_details->quantity]);
                                }
                                $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $product_details->product_number)->first();
                                if ($whs_history_data) {
                                    $whs_history_id = $whs_history_data->id;
                                } else {
                                    $whs_history_id = WhsHistory::insertHistoryData($product_details->product_number, null, null, 0);
                                }
                                WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 5, $product_details->order_id, $product_details->warehouse, $product_details->location, null, null, $product_details->quantity, $customer_order_details->invoice_customer);
                            }
                        }
                    }
                } catch (Exception $e) {
                    return false;
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // add/edit order material
    public static function storeOrderMaterial($data)
    {
        try {
            if ($data) {
                $input['product_description'] = @$data['product_description'];
                $input['product_number']      = @$data['product'];
                $input['warehouse']           = @$data['warehouse'];
                $input['location']            = @$data['location'];
                $input['sortorderval']        = @$data['sortorderval'];
                $input['order_quantity']      = @$data['order_quantity'];
                $input['quantity']            = @$data['quantity'];

                if ($input['quantity'] > 0) {
                    $inventory_details = WarehouseInventory::where('product_id', '=', $input['product_number'])->where('warehouse_id', '=', $input['warehouse'])->where('location_id', '=', $input['location'])->first();
                    if ($input['quantity'] > ($inventory_details->qty - $inventory_details->ordered)) {
                        return false;
                    }
                }

                $input['userid']                 = isset($data['user']) ? $data['user'] : "";
                $input['order_id']               = $data['order_id'];
                $input['is_package']             = isset($data['is_package']) ? $data['is_package'] : 0;
                $input['sort_number']            = isset($data['sort_number']) ? $data['sort_number'] : 0;
                $input['reference_id']           = @$data['reference_id'];
                $input['package_quantity']       = @$data['package_quantity'];
                $input['order_offer_product_id'] = @$data['order_offer_product_id'];
                $input['delivery_date']          = @$data['delivery_date'] ? GanticHelper::formatDate(@$data['delivery_date'], 'Y-m-d') : null;
                $serial_numbers                  = isset($data['serial_numbers']) ? $data['serial_numbers'] : null;
                $input['discount']               = isset($data['discount']) ? str_replace(",", ".", $data['discount']) : 0.00;
                $input['offer_sale_price']       = isset($data['price']) ? str_replace(",", ".", $data['price']) : 0.00;
                $input['vat']                    = isset($data['vat']) ? str_replace(",", ".", $data['vat']) : 0.00;
                $input['sum_ex_vat']             = isset($data['sum_ex_vat']) ? str_replace(",", ".", $data['sum_ex_vat']) : 0.00;
                $input['cost_price']             = isset($data['cost_price']) ? str_replace(",", ".", $data['cost_price']) : 0.00;
                $input['dg']                     = isset($data['dg']) ? str_replace(",", ".", $data['dg']) : 0.00;
                $input['unit']                   = @$data['unit'];
                $input['stockable']              = @$data['stockable'];
                $input['invoice_quantity']       = @$data['quantity'] ? @$data['quantity'] : @$data['order_quantity'];
                if ($data['id']) {
                    // $input['invoice_quantity'] = $input['quantity'];
                    $old_ordered_quantity = OrderMaterial::select('quantity')->where('id', '=', $data['id'])->first();
                } else {
                    $input['prod_nbr'] = @$data['prod_nbr'];
                }
                if (@$data['order_offer_product_id']) {
                    $material_data = OrderMaterial::where('order_offer_product_id', '=', @$data['order_offer_product_id'])->orderby('created_at', 'asc')->first();
                    if ($material_data) {
                        $input['discount']         = @$material_data->discount;
                        $input['offer_sale_price'] = @$material_data->offer_sale_price;
                        $input['is_offerorder']    = @$material_data->is_offerorder;
                    }
                }
                $id = $data['id'] ? $data['id'] : GanticHelper::gen_uuid();
                OrderMaterial::updateOrCreate(['id' => $id], $input);

                //updateing the ordered value in inventory table
                $inventory_details = WarehouseInventory::where('product_id', '=', $input['product_number'])->where('warehouse_id', '=', $input['warehouse'])->where('location_id', '=', $input['location'])->first();
                $ordered           = 0;
                if ($data['id']) {
                    $ordered = @$inventory_details->ordered - $old_ordered_quantity->quantity;
                    $ordered = $ordered + $input['quantity'];
                } else {
                    if ($inventory_details) {
                        $ordered = @$inventory_details->ordered + $input['quantity'];
                    } else {
                        $ordered = $input['quantity'];
                    }
                }
                if (@$inventory_details) {
                    WarehouseInventory::find($inventory_details->id)->update(['ordered' => $ordered]);
                }
                return $id;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProductDetail($id, $order_type = false, $warehouse_id = false)
    {
        try {
            if ($id) {
                if ($order_type == "1") {
                    $location_details = WarehouseInventory::select(DB::Raw("whs_location.name, SUM(whs_inventory.qty) as qty, SUM(whs_inventory.ordered) as ordered, whs_inventory.location_id AS ID"))
                        ->leftjoin('whs_location', 'location_id', '=', 'whs_location.id')
                        ->leftjoin('product', 'whs_inventory.product_id', '=', 'product.id')
                        ->where('whs_inventory.product_id', '=', $id)
                        ->where('whs_inventory.warehouse_id', '=', $warehouse_id)
                        ->groupBy('location_id')
                        ->orderBy('whs_location.name', 'asc')
                        ->get();
                    $addional_location_details = array();
                    $sn_required               = 0;
                    if ($location_details) {
                        $i = 0;
                        foreach ($location_details as $key => $value) {
                            $name    = $value->name;
                            $ordered = $value->ordered;
                            $qty     = $value->qty;
                            if (number_format(($qty - $ordered), 0) > 0) {
                                $available_qty                            = number_format(($qty - $ordered), 0);
                                $location                                 = $name . "(" . $available_qty . ")";
                                $location_details[$i]['NAME']             = $location;
                                $addional_location_details[$i]['name']    = $name;
                                $addional_location_details[$i]['qty']     = $value->qty;
                                $addional_location_details[$i]['ordered'] = $value->ordered;
                                $addional_location_details[$i]['ID']      = $value->ID;
                                $addional_location_details[$i]['NAME']    = $location;
                                $i++;
                            }
                        }
                    }
                    $location_array = array('serial_numbers' => $addional_location_details);
                    return $location_array;
                }
            }
        } catch (Exception $e) {
            echo $e;die;
            return false;
        }
    }

    /**
     * [getProductQuantityDetail description]
     * @param  boolean $product_id   [description]
     * @param  boolean $order_type   [description]
     * @param  boolean $warehouse_id [description]
     * @param  boolean $location_id  [description]
     * @return [type]                [description]
     */
    public static function getProductQuantityDetail($product_id = false, $order_type = false, $warehouse_id = false, $location_id = false)
    {
        $quantity_details = WarehouseInventory::select(DB::Raw("whs_location.name, SUM(whs_inventory.qty) as qty, SUM(whs_inventory.ordered) as ordered, whs_inventory.location_id AS ID"))->leftjoin('whs_location', 'location_id', '=', 'whs_location.id')->leftjoin('product', 'whs_inventory.product_id', '=', 'product.id')->where('whs_inventory.product_id', '=', $product_id)->where('whs_inventory.warehouse_id', '=', $warehouse_id)->where('whs_inventory.location_id', '=', $location_id)->groupBy('location_id')->get();
        foreach ($quantity_details as $key => $value) {
            $ordered       = $value->ordered;
            $qty           = $value->qty;
            $available_qty = $qty - $ordered;
        }
        if (@$available_qty) {
            return $available_qty;
        } else {
            $available_qty = 0;
            return $available_qty;
        }

    }

    /**
     * [deleteOrderMaterial description]
     * @param  [type] $ordermaterial [description]
     * @return [type]                [description]
     */
    public static function deleteOrderMaterial($ordermaterial)
    {
        try {
            if ($ordermaterial) {
                if ($ordermaterial->sn_required == 1 && @$ordermaterial->orderMaterialInventory) {
                    foreach ($ordermaterial->orderMaterialInventory as $inventory_key => $inventory_value) {
                        if ($ordermaterial->sn_required) {
                            $input['inventory_id'] = @$inventory_value->inventory_id;
                            if (WarehouseInventory::find($input['inventory_id'])) {
                                WarehouseInventory::find($input['inventory_id'])->update(['ordered' => 0]);
                            }

                        }
                    }
                } else {
                    if ($ordermaterial->sn_required != '1') {
                        $location_id                 = $ordermaterial->location;
                        $warehouse_id                = $ordermaterial->warehouse;
                        $product_id                  = $ordermaterial->product_number;
                        $warehouse_inventory_details = WarehouseInventory::where('product_id', '=', $product_id)->where('location_id', '=', $location_id)->where('warehouse_id', '=', $warehouse_id)->first();
                        if ($warehouse_inventory_details && $warehouse_inventory_details->ordered > 0) {
                            $ordered = $warehouse_inventory_details->ordered - $ordermaterial->quantity;
                            if (WarehouseInventory::find($warehouse_inventory_details->id)) {
                                WarehouseInventory::find($warehouse_inventory_details->id)->update(['ordered' => $ordered]);
                            }

                        }
                    }
                }
            }
        } catch (Exception $e) {

        }
    }

    /**
     * [updateContentProducts description]
     * @return [type] [description]
     */
    public static function updateContentProducts($decoded_invoice_quantity, $change_invoice_quantity, $order_id)
    {
        try {
            if ($decoded_invoice_quantity) {
                foreach ($decoded_invoice_quantity as $key => $value) {
                    DB::statement("UPDATE  order_material SET approved_product = 1 WHERE id = '" . $value['product_id'] . "'");
                    if (isset($value['invoice_quantity'])) {
                        $invoice_quantity = str_replace(",", ".", $value['invoice_quantity']);
                        DB::statement("UPDATE  order_material SET invoice_quantity = 0 WHERE id ='" . $value['product_id'] . "'");
                    } else {
                        DB::statement("UPDATE  order_material SET invoice_quantity = NULL WHERE id ='" . $value['product_id'] . "'");
                    }
                    $product_details = OrderMaterial::where('id', '=', $value['product_id'])->first();
                    if ($product_details) {
                        $location_id                 = $product_details->location;
                        $warehouse_id                = $product_details->warehouse;
                        $product_id                  = $product_details->product_number;
                        $warehouse_inventory_details = WarehouseInventory::where('product_id', '=', $product_id)->where('location_id', '=', $location_id)->where('warehouse_id', '=', $warehouse_id)->first();
                        if ($warehouse_inventory_details) {
                            $ordered = $warehouse_inventory_details->ordered - $product_details->quantity;
                            $qty     = $warehouse_inventory_details->qty - $product_details->quantity;
                            WarehouseInventory::find($warehouse_inventory_details->id)->update(['ordered' => $ordered, 'qty' => $qty]);
                        }
                        $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $product_details->product_number)->first();
                        if ($whs_history_data) {
                            $whs_history_id = $whs_history_data->id;
                        } else {
                            $whs_history_id = WhsHistory::insertHistoryData($product_details->product_number, null, null, 0);
                        }
                        WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 5, $product_details->order_id, $product_details->warehouse, $product_details->location, null, null, $product_details->quantity, null);
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
            die;
        }
    }

    /**
     * [storeOfferProducts description]
     * @param  [type] $offer_product_details [description]
     * @param  [type] $order_id              [description]
     * @return [type]                        [description]
     */
    public static function storeOfferProducts($offer_product_details, $order_id)
    {
        if (@$order_id) {
            foreach ($offer_product_details as $key => $value) {
                $product_details           = Product::findOrFail($value['product_id']);
                $input['id']               = GanticHelper::gen_uuid();
                $input['order_id']         = $order_id;
                $input['warehouse']        = '';
                $input['quantity']         = 0;
                $input['invoice_quantity'] = 0;
                $input['reference_id']     = '';
                $input['location']         = '';
                $input['userid']           = Auth::User()->id;
                $input['product_number']   = $value['product_id'];
                $input['order_quantity']   = $value['qty'];
                $input['discount']         = $value['discount'];
                $input['offer_sale_price'] = $value['price'];
                if ($product_details->is_package == 1) {
                    $input['is_package'] = 1;
                } else {
                    $input['is_package'] = 0;
                }
                OrderMaterial::create($input);
                if ($product_details->is_package == 1) {
                    OrderMaterial::storeOfferProductPackage($value['product_id'], $input['id'], $value['qty'], $order_id);
                }
            }
        }

    }

    /**
     * [storeOfferProductPackage description]
     * @param  [type] $package_id   [description]
     * @param  [type] $reference_id [description]
     * @param  [type] $pacakge_qty  [description]
     * @param  [type] $order_id     [description]
     * @return [type]               [description]
     */
    public static function storeOfferProductPackage($package_id, $reference_id, $pacakge_qty, $delivery_date, $order_id)
    {
        $package_products = ProductPackage::selectRaw('product.*, product_package.*, product.id as product_id')->where("product_package.package_id", '=', $package_id);
        $package_products = $package_products->leftjoin('product', 'content', '=', 'product.id')->orderBy("product_package.sort_number", "desc")->get();
        foreach ($package_products as $key => $value) {
            $supplier                  = ProductSupplier::where('product_id', '=', $value['product_id'])->where('is_main', 1)->first();
            $input['id']               = GanticHelper::gen_uuid();
            $input['order_id']         = $order_id;
            $input['warehouse']        = '';
            $input['quantity']         = 0;
            $input['invoice_quantity'] = 0;
            $input['reference_id']     = $reference_id;
            $input['location']         = '';
            $input['userid']           = Auth::User()->id;
            $input['product_number']   = $value['product_id'];
            $input['package_quantity'] = $value['qty'];
            $input['sort_number']      = $value['sort_number'];
            $input['delivery_date']    = $delivery_date;
            $input['order_quantity']   = $value['qty'] * $pacakge_qty;
            $product_description       = @$value['product_number'] . ' - ' . @$value['description'];
            if (@$supplier) {
                $product_description = $product_description . ' - ' . $supplier->articlenumber;
            }
            $input['product_description'] = $product_description;
            OrderMaterial::create($input);
        }
    }

    /**
     * [updateOfferOrderProduct description]
     * @param  boolean $ids [description]
     * @return [type]       [description]
     */
    public static function updateOfferOrderProduct($ids = false)
    {
        if (@$ids) {
            foreach ($ids as $key => $value) {
                $order_material_details = OrderMaterial::where('id', '=', $value)->first();
                if (@$order_material_details && $order_material_details->is_offerorder == 1) {
                    OfferOrderProduct::where('id', '=', @$order_material_details->order_offer_product_id)->update(['is_approved' => 1]);
                }
            }
        }

    }

    /**
     * [ConstrtuctReturnOrderMaterials description]
     * @param boolean $order_id [description]
     */
    public static function ConstrtuctReturnOrderMaterials($order_id = false)
    {
        $order_materials = array();
        $materials       = OrderMaterial::where('order_id', '=', $order_id)->where('approved_product', '=', 1)->whereNull('is_logistra')->where('is_package', '=', 0)->where('quantity', '>', 0)->whereRaw('return_quantity < quantity')->with('product')->get();
        foreach ($materials as $key => $value) {
            $array_key                                      = GanticHelper::gen_uuid();
            $order_materials[$array_key]['serial_number']   = null;
            $order_materials[$array_key]['inventory_id']    = null;
            $order_materials[$array_key]['available_qty']   = $value->quantity - $value->return_quantity;
            $order_materials[$array_key]['warehouse']       = $value->warehouse;
            $order_materials[$array_key]['location']        = $value->location;
            $order_materials[$array_key]['product_details'] = $value->product;
            $order_materials[$array_key]['sn_required']     = 0;
            $order_materials[$array_key]['id']              = $array_key;
            $order_materials[$array_key]['material_id']     = $value->id;
            $order_materials[$array_key]['inventory_id']    = null;
            $order_materials[$array_key]['locations_array'] = Location::where('warehouse_id', '=', $value->warehouse)->orderBy('name', 'asc')->pluck('name', 'id');
        }
        return $order_materials;
    }

    /**
     * [createReturnOrder description]
     * @param  [type] $materials [description]
     * @param  [type] $order_id  [description]
     * @return [type]            [description]
     */
    public static function createReturnOrder($materials, $order_id)
    {
        $return_value  = 1;
        $order_details = Order::where('id', '=', $order_id)->first();
        foreach ($materials as $key => $value) {
            $result = OrderMaterial::adjustWarehouseInventoryWithOutSn($value);
            if ($result == null) {
                $return_value = 0;
            }
        }
        $whs_order = OrderMaterial::createWarehouseReturnOrder($materials, $order_details);
        WhsHistory::createReturnOrderHistory($materials, $whs_order);
        if ($whs_order == null) {
            $return_value = 0;
        }
        return $return_value;
    }

    public static function adjustWarehouseInventoryWithOutSn($value)
    {
        $inventory_details = WarehouseInventory::where('warehouse_id', '=', $value->warehouse)->where('location_id', '=', $value->location)->where('product_id', '=', $value->product_id)->first();
        if (@$inventory_details) {
            $qty    = $value->return_qty + $inventory_details->qty;
            $result = WarehouseInventory::where('id', '=', $inventory_details->id)->update(['qty' => $qty]);
        } else {
            $input                  = array();
            $input['id']            = GanticHelper::gen_uuid();
            $input['warehouse_id']  = $value->warehouse;
            $input['location_id']   = $value->location;
            $input['product_id']    = $value->product_id;
            $input['qty']           = 1;
            $input['serial_number'] = $value->serial_number;
        }
        $result = OrderMaterial::adjustMaterialValue($value->mateial_id, $value->return_qty);
        return $result;
    }

    /**
     * [adjustMaterialValue description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function adjustMaterialValue($id, $qty)
    {
        $material_details = OrderMaterial::where('id', '=', $id)->first();
        $return_quantity  = $qty + $material_details->return_quantity;
        $update_result    = OrderMaterial::where('id', '=', $id)->update(['return_quantity' => $return_quantity]);
        if ($material_details->invoiced == 0) {
            $invoice_quantity = @$material_details->invoice_quantity - $qty;
            if ($invoice_quantity < 0) {
                $invoice_quantity = 0;
            }
            $update_result = OrderMaterial::where('id', '=', $id)->update(['invoice_quantity' => $invoice_quantity]);
            if (@$material_details->reference_id) {
                $all_pacakage_contents = OrderMaterial::where('reference_id', '=', $material_details->reference_id)->whereRaw('return_quantity < quantity')->get();
                if (count($all_pacakage_contents) == 0) {
                    $package_details = OrderMaterial::where('id', '=', $material_details->reference_id)->first();
                    $update_package  = OrderMaterial::where('id', '=', $material_details->reference_id)->update(['invoice_quantity' => 0, 'return_quantity' => $package_details->quantity]);
                }
            }
        }
        return 1;

    }

    /**
     * [createWarehouseReturnOrder description]
     * @param  [type] $products      [description]
     * @param  [type] $order_details [description]
     * @return [type]                [description]
     */
    public static function createWarehouseReturnOrder($products, $order_details)
    {
        $input                          = array();
        $input['id']                    = GanticHelper::gen_uuid();
        $input['order_number']          = WarehouseOrder::getWarehouseOrderNumber();
        $input['order_date']            = date('Y-m-d');
        $input['added_by']              = Session::get('currentUserID');
        $input['updated_by']            = Session::get('currentUserID');
        $input['order_type']            = 4;
        $input['order_status']          = 6;
        $input['product_details']       = json_encode($products);
        $input['customer_order_id']     = $order_details->id;
        $input['customer_order_number'] = $order_details->order_number;
        $result                         = WarehouseOrder::create($input);
        return $result->id;
    }

    /**
     * [constrtuctOnstockRec description]
     * @param  [type] $warehouse_product_details [description]
     * @return [type]                            [description]
     */
    public static function constrtuctOnstockRec($warehouse_product_details)
    {
        $data                      = array();
        $usertype                  = $data['usertype']                  = Session::get('usertype');
        $user_id                   = Session::get('currentUserID');
        $warehouse_product_details = collect($warehouse_product_details);
        foreach ($warehouse_product_details as $key => $value) {
            $value->available_qty = $value->balance - $value->customer_order;
        }
        $data['warehouse_product_details'] = $warehouse_product_details->where('available_qty', '>', 0);
        $data['user_warehouse']            = [];
        if ($usertype != "Admin" && $usertype != "Administrative") {
            $data['user_warehouse'] = Warehouse::leftjoin('warehouse_responsible', 'warehouse_responsible.warehouse_id', '=', 'warehouse.id')->where('warehouse_responsible.user_id', '=', $user_id)->pluck('id')->toArray();
            $user_qty               = $data['warehouse_product_details']->whereIn('warehouse_id', $data['user_warehouse'])->sum('available_qty');
            if ($user_qty < 1) {
                $data['user_warehouse'] = Warehouse::orderBy('shortname', 'asc')->where('main', 1)->pluck('id')->toArray();
            }
        }
        return $data;
    }

    /**
     * [constructWhsDropdown description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public static function constructWhsDropdown($product_id)
    {
        $usertype                         = Session::get('usertype');
        $user_id                          = Session::get('currentUserID');
        $data['user_warehouse']           = Warehouse::leftjoin('warehouse_responsible', 'warehouse_responsible.warehouse_id', '=', 'warehouse.id')->where('warehouse_responsible.user_id', '=', $user_id)->orderby('shortname', 'asc')->pluck('shortname', 'id');
        $data['warehouse_dropdown_array'] = Warehouse::orderBy('shortname', 'asc')->pluck('shortname', 'id');
        if ($usertype != "Admin" && $usertype != "Administrative") {
            if (count($data['user_warehouse']) > 0) {
                $data['warehouse_dropdown_array'] = $data['user_warehouse'];
            } else {
                $data['warehouse_dropdown_array'] = Warehouse::orderBy('shortname', 'asc')->Where('main', 1)->pluck('shortname', 'id');
            }
        }
        foreach ($data['warehouse_dropdown_array'] as $key => $value) {
            $invetory                               = WarehouseInventory::select(DB::Raw("SUM(qty) as qty"))->where('warehouse_id', $key)->where('product_id', $product_id)->first();
            $ordered_invetory                       = WarehouseInventory::select(DB::Raw("SUM(ordered) as qty"))->where('warehouse_id', $key)->where('product_id', $product_id)->first();
            $qty                                    = $invetory->qty - $ordered_invetory->qty;
            $data['warehouse_dropdown_array'][$key] = $value . '(0)';
            if (@$qty > 0) {
                $data['warehouse_dropdown_array'][$key] = $value . '(' . number_format($qty, 2, ",", " ") . ')';
            }
        }
        return $data['warehouse_dropdown_array'];
    }

    /**
     * [storeText description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function storeText($data)
    {
        if ($data['id'] != -1) {
            OrderMaterial::whereId($data['id'])->update(['product_text' => $data['text']]);
            return $data['id'];
        } else {
            $input                 = array();
            $input['id']           = GanticHelper::gen_uuid();
            $input['product_text'] = @$data['text'];
            $input['order_id']     = @$data['order_id'];
            $input['sortorderval'] = @$data['sortorderval'];
            $input['is_text']      = 1;
            $input['text_ref_id']  = null;
            $result                = OrderMaterial::create($input);
            return $result->id;
        }
    }

    /**
     * [storeText description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function getBillingData($order_id)
    {
        $data             = array();
        $data['order_id'] = $order_id;
        $data['orders']   = Order::where('id', '=', $order_id)->first();
        $ordermaterial    = OrderMaterial::select('order_material.*')->where('order_id', '=', $order_id)->leftjoin('product', 'product.id', '=', "order_material.product_number")->orderby('sortorderval', 'asc');
        $ordermaterial->where(function ($query) {
            $query->orWhereNull('reference_id');
        });
        $data['ordermaterials']    = $ordermaterial->with('product')->orderBy('updated_at', 'desc')->get();
        $users                     = User::getUsersDropDown();
        $data['last_updated_time'] = GanticHelper::formatDate(@$data['orders']->updated_at, 'd.m.Y');
        $data['last_updated_by']   = @$data['orders']->updated_by ? @$users[@$data['orders']->updated_by] : @$users[@$data['orders']->added_by];
        $data['date']              = date('d.m.Y');
        $data['user_name']         = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $data['customerName']      = Customer::find($data['orders']->customer_id)->name;
        $data['invoice_units']     = DropdownHelper::where('language', 'no')->where('groupcode', '010')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return $data;
    }

    /**
     * [storeOfferMaterial description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function storeOfferMaterial($data)
    {
        {
            $input['product_number']      = @$data['product'];
            $input['is_package']          = @$data['is_package'];
            $input['sortorderval']        = @$data['sortorderval'];
            $input['order_quantity']      = isset($data['order_quantity']) ? str_replace(",", ".", $data['order_quantity']) : 0.00;
            $input['invoice_quantity']    = 0;
            $input['order_id']            = $data['order_id'];
            $input['delivery_date']       = @$data['delivery_date'] ? GanticHelper::formatDate(@$data['delivery_date'], 'Y-m-d') : null;
            $input['discount']            = isset($data['discount']) ? str_replace(",", ".", $data['discount']) : 0.00;
            $input['offer_sale_price']    = isset($data['price']) ? str_replace(",", ".", $data['price']) : 0.00;
            $input['vat']                 = isset($data['vat']) ? str_replace(",", ".", $data['vat']) : 0.00;
            $input['sum_ex_vat']          = isset($data['sum_ex_vat']) ? str_replace(",", ".", $data['sum_ex_vat']) : 0.00;
            $input['unit']                = $data['unit'];
            $input['product_description'] = $data['product_description'];
            $id                           = @$data['id'] ? $data['id'] : GanticHelper::gen_uuid();
            OrderMaterial::updateOrCreate(['id' => $id], $input);
            return $id;
        }

    }

}
