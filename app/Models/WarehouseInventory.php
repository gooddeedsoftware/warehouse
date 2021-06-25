<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\WarehouseOrderDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseInventory extends Model
{

    protected $table     = 'whs_inventory';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;

    protected $fillable = array('id', 'warehouse_id', 'location_id', 'product_id', 'qty', 'deleted_at', 'ordered', 'delivered');

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    /**
     *    Get related warehouse order details
     *     @return object
     **/
    public function warehouseOrderDetails()
    {
        return $this->belongsTo('App\Models\warehouseOrderDetails', 'product_id', 'product_id');
    }

    //Supplier Order
    public static function saveSupplierInventory($data, $order_type = false, $id = false)
    {
        $whs_order_details = WarehouseOrder::where('id', '=', $id)->first();
        $json_decoded_data = json_decode($data);
        $order_qty         = 0;
        for ($i = 0; $i < count($json_decoded_data); $i++) {
            // dd($json_decoded_data[$i]);
            $order_qty           = $order_qty + $json_decoded_data[$i]->qty;
            $whs_product_id      = $json_decoded_data[$i]->whs_product_id;
            $input['product_id'] = $json_decoded_data[$i]->product_id;
            $input['id']         = GanticHelper::gen_uuid();
            $received_quantity   = 0;

            if (!$json_decoded_data[$i]->order_details) {
                WarehouseOrderDetails::updateOrCreate(['destination_whs_id' => null, 'product_id' => $input['product_id'], 'whs_product_id' => $whs_product_id, 'whs_order_id' => $id], ['ordered_qty' => $json_decoded_data[$i]->qty, 'received_qty' => $received_quantity, 'destination_whs_id' => null, 'product_id' => $input['product_id'], 'whs_product_id' => $whs_product_id, 'whs_order_id' => $id]);
            }

            foreach ($json_decoded_data[$i]->order_details as $key => $value) {

                $received_quantity = $received_quantity + $value->received_quantity;
                WarehouseOrderDetails::updateOrCreate(['destination_whs_id' => null, 'product_id' => $input['product_id'], 'whs_product_id' => $whs_product_id, 'whs_order_id' => $id], ['ordered_qty' => $json_decoded_data[$i]->qty, 'received_qty' => $received_quantity, 'destination_whs_id' => null, 'product_id' => $input['product_id'], 'whs_product_id' => $whs_product_id, 'whs_order_id' => $id]);

                for ($j = 0; $j < count($value->serial_number_products); $j++) {
                    $input['qty']               = $value->received_quantity;
                    $input['location_id']       = isset($value->serial_number_products[$j]->rec_location_id) ? $value->serial_number_products[$j]->rec_location_id : "";
                    $input['warehouse_id']      = isset($value->serial_number_products[$j]->rec_warehouse_id) ? $value->serial_number_products[$j]->rec_warehouse_id : "";
                    $existing_inventory_details = WarehouseInventory::where('warehouse_id', '=', $input['warehouse_id'])->where('product_id', '=', $input['product_id'])->where('location_id', '=', $input['location_id'])->first();
                    if ($existing_inventory_details) {
                        $input['qty'] = $existing_inventory_details->qty;
                        if ($value->newly_received) {
                            $input['qty'] = $existing_inventory_details->qty + ($value->received_quantity ? $value->received_quantity : 0);
                        }
                        $inventory = WarehouseInventory::updateOrCreate(['warehouse_id' => $input['warehouse_id'], 'product_id' => $input['product_id'], 'location_id' => $input['location_id']], ['qty' => $input['qty']]);
                    } else {
                        $input['id'] = GanticHelper::gen_uuid();
                        $inventory   = WarehouseInventory::updateOrCreate(['warehouse_id' => $input['warehouse_id'], 'product_id' => $input['product_id'], 'location_id' => $input['location_id']], ['qty' => $input['qty'], 'id' => $input['id']]);
                    }
                    if ($value->newly_received) {
                        $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $json_decoded_data[$i]->product_id)->first();
                        if ($whs_history_data) {
                            $whs_history_id = $whs_history_data->id;
                        } else {
                            $whs_history_id = WhsHistory::insertHistoryData($json_decoded_data[$i]->product_id);
                        }
                        WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 3, $id, null, null, $input['warehouse_id'], $input['location_id'], $value->received_quantity, null);
                    }
                }
            }
        }
    }

    /**
     * [saveAdjustmentnventory description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function saveAdjustmentnventory($warehouseOrder)
    {
        try {
            $product_details = json_decode($warehouseOrder->product_details);
            if ($product_details) {
                foreach ($product_details as $key => $value) {
                    $existing_inventory_details = WarehouseInventory::where('warehouse_id', '=', $warehouseOrder->warehouse)->where('product_id', '=', $value->product_id)->where('location_id', '=', $value->location_id)->first();
                    if (@$existing_inventory_details) {
                        $updated_qty = $existing_inventory_details->qty + ($value->qty ? $value->qty : 0);
                        WarehouseInventory::whereId($existing_inventory_details->id)->update(['qty' => $updated_qty]);
                    } else {
                        $inventory_rec                 = array();
                        $inventory_rec['warehouse_id'] = $warehouseOrder->warehouse;
                        $inventory_rec['product_id']   = $value->product_id;
                        $inventory_rec['location_id']  = $value->location_id;
                        $inventory_rec['qty']          = $value->qty;
                        $inventory_rec['id']           = GanticHelper::gen_uuid();
                        WarehouseInventory::create($inventory_rec);
                    }

                    //storing the history part
                    $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $value->product_id)->first();
                    if ($whs_history_data) {
                        $whs_history_id = $whs_history_data->id;
                    } else {
                        $whs_history_id = WhsHistory::insertHistoryData($value->product_id);
                    }
                    WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 2, $warehouseOrder->id, null, null, $warehouseOrder->warehouse, $value->location_id, $value->qty, null);

                }
            }
        } catch (Exception $e) {
            echo $e;
            return false;
        }
    }

    public static function saveInventory($data, $warehouse = false, $source_warehouse = false, $destination_warehouse = false, $order_type = false, $id = false)
    {
        try {
            if ($data) {
                if ($warehouse) {
                    // adjustment order
                    $input['warehouse_id'] = $warehouse;
                } else if ($source_warehouse) {
                    // transfer order
                    $input['warehouse_id'] = $source_warehouse;
                } else if ($destination_warehouse) {
                    // supplier order
                    $input['warehouse_id'] = $destination_warehouse;
                }
                $delete_exist_record = 0;
                $whs_order_details   = WarehouseOrder::where('id', '=', $id)->first();
                if (@$whs_order_details->destination_warehouse) {
                    $delete_exist_record = 1;
                    $exist_warehouse_id  = $whs_order_details->destination_warehouse;
                }
                $json_decoded_data = json_decode($data);
                $order_qty         = 0;
                for ($i = 0; $i < count($json_decoded_data); $i++) {
                    $order_qty           = $order_qty + $json_decoded_data[$i]->qty;
                    $whs_product_id      = $json_decoded_data[$i]->whs_product_id;
                    $input['product_id'] = $json_decoded_data[$i]->product_id;

                    $input['id'] = GanticHelper::gen_uuid();
                    if (!$source_warehouse && $destination_warehouse) {
                        $input['location_id'] = $input['warehouse_id'];
                    } else {
                        $input['location_id'] = isset($json_decoded_data[$i]->location_id) ? $json_decoded_data[$i]->location_id : "";
                    }
                    $location_id = isset($json_decoded_data[$i]->location_id) ? $json_decoded_data[$i]->location_id : "";
                    WarehouseInventory::updateTransferPickedQtyDetails($json_decoded_data[$i], $location_id, $input);
                    $picked_quantity   = 0;
                    $received_quantity = 0;
                    foreach ($json_decoded_data[$i]->order_details as $key => $value) {
                        $location_id       = isset($json_decoded_data[$i]->location_id) ? $json_decoded_data[$i]->location_id : "";
                        $picked_quantity   = $picked_quantity + $value->picked_quantity;
                        $received_quantity = $received_quantity + (@$value->received_quantity ? $value->received_quantity : 0);
                        WarehouseOrderDetails::updateOrCreate(['destination_whs_id' => $destination_warehouse, 'source_whs_id' => $source_warehouse, 'product_id' => $input['product_id'], 'whs_product_id' => $whs_product_id, 'whs_order_id' => $id], ['ordered_qty' => $json_decoded_data[$i]->qty, 'received_qty' => $received_quantity, 'picked_qty' => $picked_quantity ? $picked_quantity : $json_decoded_data[$i]->qty]);

                        if ($value->newly_received) {
                            WarehouseInventory::updateTransferReceivedQtyDetails($value, $location_id, $input, $json_decoded_data[$i]->destination_warehouse_id, $id, $source_warehouse, $json_decoded_data[$i]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
            return false;
        }
    }

    // update order status
    public static function compareOrderAndReceiveQty($data, $order_type, $id)
    {
        try {
            if ($data) {
                $json_decoded_data  = json_decode($data);
                $order_qty          = 0;
                $received_order_qty = 0;
                $picked_quantity    = 0;
                for ($i = 0; $i < count($json_decoded_data); $i++) {
                    $order_qty = $order_qty + $json_decoded_data[$i]->qty;
                    foreach ($json_decoded_data[$i]->order_details as $key => $value) {
                        if (isset($value->received_quantity)) {
                            if ($order_type == '1') {
                                $received_order_qty = (float) $received_order_qty + (float) $value->received_quantity;
                                $picked_quantity    = (float) $picked_quantity + (float) $value->picked_quantity;
                            } else {
                                $received_order_qty = (float) $received_order_qty + (float) $value->received_quantity;
                            }
                        }
                    }
                }
                if (trim($order_qty) == trim($received_order_qty)) {
                    WarehouseOrder::updateWarehouseStatus($id, 5);
                } else if ($received_order_qty > 0 || $picked_quantity > 0) {
                    if ($order_type == '1') {
                        if ($order_qty == $picked_quantity && $order_qty != $received_order_qty) {
                            WarehouseOrder::updateWarehouseStatus($id, 9);
                        } else if ($order_qty == $received_order_qty) {
                            WarehouseOrder::updateWarehouseStatus($id, 5);
                        } else if ($received_order_qty > 0 && $order_qty != $received_order_qty) {
                            WarehouseOrder::updateWarehouseStatus($id, 4);
                        } else {
                            WarehouseOrder::updateWarehouseStatus($id, 8);
                        }
                    } else {
                        WarehouseOrder::updateWarehouseStatus($id, 4);
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // get inventory details from id
    public static function getLocationFromInventoryID($id = false)
    {
        try {
            if ($id) {
                $invetory_details = WarehouseInventory::findOrFail($id);
                return (($invetory_details) ? $invetory_details : false);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /*
     *    Get serial numbers
     *    This will be used in transfer order creation.
     *
     */
    public static function getSerialNumbersFromLocation($location_id = false, $product_id = false, $picked_quantity = false)
    {
        try {
            $invetory_details = WarehouseInventory::where('location_id', '=', $location_id)->where('qty', '!=', 0)->where(function ($query) {
                $query->whereNull('ordered')->orWhere("ordered", 0);
            });
            if ($product_id) {
                $invetory_details->where('product_id', '=', $product_id);
            }
            $invetory_details      = $invetory_details->orderby('serial_number', 'asc')->get();
            $available_qunatity    = 0;
            $serial_number_details = array();
            foreach ($invetory_details as $key => $value) {
                $serial_number_details[$value->id] = $value->serial_number;
                $available_qunatity                = $available_qunatity + $value->qty;
            }
            if ($picked_quantity && $available_qunatity < $picked_quantity) {
                return false;
            } else {
                return $serial_number_details;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // update transfer picked order details
    public static function updateTransferPickedQtyDetails($value, $locatoion, $input)
    {

        if ($value) {
            try {
                if ($value->serial_number_array) {

                    for ($j = 0; $j < count($value->serial_number_array); $j++) {
                        if ($value->serial_number_array[$j]->newly_picked) {
                            if (isset($value->serial_number_array[$j]->serial_number_id) && $value->serial_number_array[$j]->serial_number_id) {
                                $inventory_id = $value->serial_number_array[$j]->serial_number_id;

                                $existing_inventory_details = WarehouseInventory::where('id', '=', $inventory_id);
                                if ($existing_inventory_details) {
                                    WarehouseInventory::where('id', '=', $inventory_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                                }
                            } else {
                                $existing_inventory_details = WarehouseInventory::where('warehouse_id', '=', $input['warehouse_id'])->where('product_id', '=', $input['product_id'])->where('location_id', '=', $input['location_id'])->first();

                                $input['qty']       = @$existing_inventory_details->qty - (isset($value->serial_number_array[$j]->picked_quantity) ? @$value->serial_number_array[$j]->picked_quantity : 0);
                                $update_data['qty'] = $input['qty'];

                                if (@$existing_inventory_details->id) {
                                    $warehouseInventory = WarehouseInventory::findOrFail($existing_inventory_details->id);
                                    $warehouseInventory->fill($update_data);
                                    $warehouseInventory->save();
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
            }
        }
    }

    // update transeer order details
    public static function updateTransferReceivedQtyDetails($value, $location_id, $input, $destination_warehouse, $order_id, $source_warehouse, $json_decoded_data)
    {
        if ($value) {
            try {
                //WarehouseInventory::updateTransferPickedQtyDetails($value, $location_id, $input);
                for ($j = 0; $j < count($value->serial_number_products); $j++) {
                    if ($value->serial_number_products[$j]->serial_number_id) {
                        $whs_history_details          = WhsHistory::where('serial_number', '=', $value->serial_number_products[$j]->serial_number)->where('product_id', '=', $input['product_id'])->where('is_deleted', '=', 0)->first();
                        $inventory_id                 = $value->serial_number_products[$j]->serial_number_id;
                        $update_data['qty']           = 1;
                        $update_data['id']            = GanticHelper::gen_uuid();
                        $update_data['location_id']   = $value->serial_number_products[$j]->rec_location_id;
                        $update_data['warehouse_id']  = $destination_warehouse;
                        $update_data['product_id']    = $input['product_id'];
                        $update_data['serial_number'] = $value->serial_number_products[$j]->serial_number;
                        WarehouseInventory::create($update_data);
                        if (@$whs_history_details) {
                            WhsHistory::where('id', '=', @$whs_history_details->id)->update(['whs_inventory_id' => $update_data['id']]);
                            $whs_history_deatils_result = WhsHistoryDetails::insertHistoryDetailData($whs_history_details->id, 1, $order_id, $source_warehouse, $location_id, @$update_data['warehouse_id'], @$update_data['location_id'], 1, null);
                        }

                    } else {
                        $existing_inventory_details = WarehouseInventory::where('warehouse_id', '=', $destination_warehouse)->where('product_id', '=', $input['product_id'])->where('location_id', '=', $value->serial_number_products[$j]->rec_location_id)->first();

                        if ($existing_inventory_details) {
                            $update_data['qty'] = $existing_inventory_details->qty + ($value->received_quantity ? $value->received_quantity : 0);
                            $warehouseInventory = WarehouseInventory::findOrFail($existing_inventory_details->id);
                            $warehouseInventory->fill($update_data);
                            $warehouseInventory->save();
                        } else {
                            $input_data['id']           = GanticHelper::gen_uuid();
                            $input_data['warehouse_id'] = $destination_warehouse;
                            $input_data['product_id']   = $input['product_id'];
                            $input_data['location_id']  = $value->serial_number_products[$j]->rec_location_id;
                            $input_data['qty']          = $value->received_quantity;
                            WarehouseInventory::create($input_data);
                        }
                        $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $json_decoded_data->product_id)->first();
                        if ($whs_history_data) {
                            $whs_history_id = $whs_history_data->id;
                        } else {
                            $whs_history_id = WhsHistory::insertHistoryData($json_decoded_data->product_id, null, null, 0);
                        }
                        WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 1, $order_id, $source_warehouse, $location_id, $destination_warehouse, $value->serial_number_products[$j]->rec_location_id, $value->received_quantity, null);
                    }

                }
            } catch (Exception $e) {
            }
        }
    }
}
