<?php
//This trait does the work of creating pick list and packlist

namespace App\Traits;

use App\Helpers\GanticHelper;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Location;
use App\Models\LogistraDetails;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\PacklistHistory;
use App\Models\PrinterDetail;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\ShippingMeasurement;
use App\Models\Warehouse;
use App\Models\WarehouseInventory;
use App\Models\WhsHistory;
use App\Models\WhsHistoryDetails;
use Auth;
use PDF;
use View;
trait PickPackList
{
    /**
     * [createPicklistPDF description]
     * @param  [type] $input_data [description]
     * @return [type]             [description]
     */
    public static function createPicklistPDF($input_data)
    {
        $warehouse_details = Warehouse::whereId($input_data['warehouse'])->first();
        $order_materials   = OrderMaterial::where('order_id', $input_data['order_id'])->orderBy('created_at', 'desc')->where('order_quantity', '>', 0)->where('quantity', '<', 1)->where('is_text', 0)->whereNull('reference_id')->get();
        if (@$input_data['location']) {
            $location_details = Location::whereId($input_data['location'])->first();
            foreach ($order_materials as $key => $value) {
                $package               = Product::where('id', '=', $value['product_number'])->first();
                $value->productDetails = $package;
                if ($value->is_package != 1) {
                    $value->pick_warehouse   = $warehouse_details->shortname;
                    $inventory_data          = WarehouseInventory::where('product_id', $value->product_number)->where('warehouse_id', $input_data['warehouse'])->where('location_id', $input_data['location'])->first();
                    $delivered_data          = OrderMaterial::where('order_id', $input_data['order_id'])->where('approved_product', 1)->where('product_number', $value->product_number)->sum('invoice_quantity');
                    $value->delivered_qty    = $delivered_data;
                    $value->pick_location_id = "undefined";
                    $value->available_qty    = 0;
                    if (@$inventory_data && $inventory_data->qty - $inventory_data->ordered > 0) {
                        $value->pick_location_id = $location_details->name;
                        $value->available_qty    = $inventory_data->qty - $inventory_data->ordered;
                    }
                } else {
                    $package_contents = OrderMaterial::where('reference_id', '=', $value->id)->get();
                    foreach ($package_contents as $pacakge_key => $package_value) {
                        $package                         = Product::where('id', '=', $package_value['product_number'])->first();
                        $package_value->productDetails   = $package;
                        $package_value->pick_warehouse   = $warehouse_details->shortname;
                        $inventory_data                  = WarehouseInventory::where('product_id', $package_value->product_number)->where('warehouse_id', $input_data['warehouse'])->where('location_id', $input_data['location'])->first();
                        $package_value->pick_location_id = "undefined";
                        $package_value->available_qty    = 0;
                        $delivered_data                  = OrderMaterial::where('order_id', $input_data['order_id'])->where('approved_product', 1)->where('product_number', $value->product_number)->sum('invoice_quantity');
                        $value->delivered_qty            = $delivered_data;
                        if (@$inventory_data && $inventory_data->qty - $inventory_data->ordered > 0) {
                            $package_value->pick_location_id = $location_details->name;
                            $package_value->delivered_qty    = $inventory_data->delivered;
                        }
                    }
                    $value->package_contents = $package_contents;
                }
            }
        } else {
            $all_locations       = Location::where('warehouse_id', '=', $input_data['warehouse'])->pluck('id');
            $constructedMaterial = array();
            foreach ($order_materials as $key => $value) {
                $package                 = Product::where('id', '=', $value['product_number'])->first();
                $value->productDetails   = $package;
                $value->pick_warehouse   = $warehouse_details->shortname;
                $value->pick_location_id = "undefined";
                $value->available_qty    = 0;
                if ($value->is_package != 1) {
                    $qty                  = WarehouseInventory::where('product_id', $value->product_number)->where('warehouse_id', $input_data['warehouse'])->whereIn('location_id', $all_locations)->sum('qty');
                    $ordered              = WarehouseInventory::where('product_id', $value->product_number)->where('warehouse_id', $input_data['warehouse'])->whereIn('location_id', $all_locations)->sum('ordered');
                    $delivered_data       = OrderMaterial::where('order_id', $input_data['order_id'])->where('approved_product', 1)->where('product_number', $value->product_number)->sum('invoice_quantity');
                    $value->delivered_qty = $delivered_data;
                    if ($qty - $ordered > 0) {
                        foreach ($all_locations as $location_key => $location_value) {
                            $qty     = WarehouseInventory::where('product_id', $value->product_number)->where('warehouse_id', $input_data['warehouse'])->where('location_id', $location_value)->sum('qty');
                            $ordered = WarehouseInventory::where('product_id', $value->product_number)->where('warehouse_id', $input_data['warehouse'])->where('location_id', $location_value)->sum('ordered');
                            if ($qty - $ordered > 0) {
                                $value->available_qty    = $qty - $ordered;
                                $location_details        = Location::whereId($location_value)->first();
                                $value->pick_location_id = $location_details->name;
                                $constructedMaterial[]   = $value->toArray();
                            }
                        }
                    } else {
                        $constructedMaterial[] = $value;
                    }
                } else {
                    $contentConstructedArray = array();
                    $package_contents        = OrderMaterial::where('reference_id', '=', $value->id)->get();
                    foreach ($package_contents as $pacakge_key => $package_value) {
                        $package                         = Product::where('id', '=', $package_value['product_number'])->first();
                        $package_value->productDetails   = $package;
                        $package_value->pick_warehouse   = $warehouse_details->shortname;
                        $package_value->pick_location_id = "undefined";
                        $package_value->available_qty    = 0;
                        $qty                             = WarehouseInventory::where('product_id', $package_value->product_number)->where('warehouse_id', $input_data['warehouse'])->whereIn('location_id', $all_locations)->sum('qty');
                        $ordered                         = WarehouseInventory::where('product_id', $package_value->product_number)->where('warehouse_id', $input_data['warehouse'])->whereIn('location_id', $all_locations)->sum('ordered');
                        $delivered_data                  = OrderMaterial::where('order_id', $input_data['order_id'])->where('approved_product', 1)->where('product_number', $package_value->product_number)->sum('invoice_quantity');
                        $package_value->delivered_qty    = $delivered_data;
                        if ($qty - $ordered > 0) {
                            foreach ($all_locations as $location_key => $location_value) {
                                $qty     = WarehouseInventory::where('product_id', $package_value->product_number)->where('warehouse_id', $input_data['warehouse'])->where('location_id', $location_value)->sum('qty');
                                $ordered = WarehouseInventory::where('product_id', $package_value->product_number)->where('warehouse_id', $input_data['warehouse'])->where('location_id', $location_value)->sum('ordered');
                                if ($qty - $ordered > 0) {
                                    $package_value->available_qty    = $qty - $ordered;
                                    $location_details                = Location::whereId($location_value)->first();
                                    $package_value->pick_location_id = $location_details->name;
                                    $contentConstructedArray[]       = $package_value->toArray();
                                }
                            }
                        } else {
                            $contentConstructedArray[] = $package_value;
                        }
                    }
                    $value->package_contents = json_decode(json_encode($contentConstructedArray), false);
                    $constructedMaterial[]   = $value->toArray();
                }
            }
            $order_materials = json_decode(json_encode($constructedMaterial), false);
        }
        $data = Order::editOrder($input_data['order_id']);
        if (@$data['orders']->customer_id) {
            $data['customer_details'] = Customer::where('id', '=', $data['orders']->customer_id)->with('shippingCustomerAddress', 'departmentCustomerAddress')->first();
        }
        $data['company_information'] = Company::first();
        $data['order_materials']     = $order_materials;
        $footer                      = View::make('order.picklistFooter', $data);
        $picklist_detail_view        = \View::make('order.picklist', $data);
        $temp_pdf_name               = GanticHelper::createTempFile("pdf");
        $pdfReport                   = PDF::loadHTML($picklist_detail_view)->setOption('footer-html', $footer)->save($temp_pdf_name);
        $fileData                    = array();
        $fileData['fileName']        = "Plukkliste_" . $data['orders']->order_number . "_GANTIC_AS.pdf";
        $fileData['filePath']        = $temp_pdf_name;
        return $fileData;
    }

    /**
     * [createPackListPDF description]
     * @param  [type] $input_data [description]
     * @return [type]             [description]
     */
    public static function createPackListPDF($input_data)
    {
        try {
            self::updateNonStockableProduct($input_data['order_id']);
            $shipping_data   = Shipping::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->first();
            $logistraProduct = 1;
            if (!$shipping_data || @$shipping_data->product_name == "Hentes") {
                $logistraProduct = 0;
            }
            $consignment = $logistraProduct == 1 ? self::generateConsignment($input_data) : 1;
            if ($logistraProduct == 1 && @$consignment['errors']) {
                return false;
            }
            $order_materials = OrderMaterial::orderBy('sortorderval', 'asc')->where('order_id', $input_data['order_id'])->whereNull('reference_id')->whereNull('is_logistra')->get();
            if ($consignment) {
                self::updateMaterialData($input_data, $consignment, $logistraProduct);
                $packlist_history_data                     = [];
                $data                                      = Order::editOrder($input_data['order_id']);
                $constructed_material                      = self::constructOrdrMaterialDataForReport($input_data, $order_materials);
                $packlist_history_data['logistra_product'] = $data['logistra_product'] = OrderMaterial::where('approved_product', 0)->where('order_id', $input_data['order_id'])->where('is_logistra', 1)->with('product')->first();
                $packlist_history_data['order_materials']  = $data['order_materials']  = $constructed_material['order_materials'];
                self::storePackListHistoryData($input_data, $packlist_history_data);
                self::approveMaterials($order_materials, $input_data);
                if (@$data['orders']->customer_id) {
                    $data['customer_details'] = Customer::where('id', '=', $data['orders']->customer_id)->with('shippingCustomerAddress', 'departmentCustomerAddress')->first();
                }
                $data['company_information'] = Company::first();

                $footer                        = View::make('order.packlistFooter', $data);
                $packlist_detail_view          = \View::make('order.packlist', $data);
                $temp_pdf_name                 = GanticHelper::createTempFile("pdf");
                $pdfReport                     = PDF::loadHTML($packlist_detail_view)->setOption('footer-html', $footer)->save($temp_pdf_name);
                $fileData                      = array();
                $fileData['fileName']          = "Pakkseddel_" . $data['orders']->order_number . "_GANTIC_AS.pdf";
                $fileData['filePath']          = $temp_pdf_name;
                $fileData['rest_order']        = $constructed_material['rest_order'];
                $fileData['rest_order_number'] = $constructed_material['rest_order_number'];
                return $fileData;
            }
            return [];
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'pickpacklist.log');
            return [];
        }
    }

    /**
     * [storePackListHistoryData description]
     * @param  [type] $input_data [description]
     * @return [type]             [description]
     */
    public static function storePackListHistoryData($input_data, $pdf_data)
    {
        try {
            $pack_list_input             = [];
            $pack_list_input['user_id']  = Auth::user()->id;
            $pack_list_input['order_id'] = $input_data['order_id'];
            $pack_list_input['pdf_data'] = json_encode($pdf_data);
            PacklistHistory::create($pack_list_input);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'pickpacklist.log');
        }
    }

    /**
     * [approveMaterials description]
     * @param  [type] $order_materials [description]
     * @return [type]                  [description]
     */
    public static function approveMaterials($order_materials, $input_data)
    {

        //Approving the logistra product
        $customer_order_details = Order::where('id', '=', $input_data['order_id'])->first();
        $order_material         = OrderMaterial::where('order_id', $input_data['order_id'])->where('is_logistra', 1)->update(['approved_product' => 1, 'quantity' => 1, 'invoice_quantity' => 1]);
        foreach ($order_materials as $key => $value) {
            if (!$value->shippment_id) {
                // For ordinary products
                $approveMaterial = OrderMaterial::where('id', $value->id);
                $approveMaterial->where(function ($query) {
                    $query->where('quantity', '>', 0);
                    $query->orWhere('is_text', '=', 1);
                });
                $approveMaterial->update(['approved_product' => 1, 'invoice_quantity' => @$value->quantity]);
                $inventory_data = WarehouseInventory::where('product_id', $value->product_number)->where('warehouse_id', $value->warehouse)->where('location_id', $value->location)->first();
                if (@$inventory_data && $inventory_data->qty - $inventory_data->ordered >= 0) {
                    WarehouseInventory::find($inventory_data->id)->update(['ordered' => $inventory_data->ordered - $value->quantity, 'qty' => $inventory_data->qty - $value->quantity, 'delivered' => $inventory_data->delivered + $value->quantity]);
                }

                //updating the history
                if ($value->quantity >= 1) {
                    $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $value->product_number)->first();
                    if ($whs_history_data) {
                        $whs_history_id = $whs_history_data->id;
                    } else {
                        $whs_history_id = WhsHistory::insertHistoryData($value->product_number, null, null, 0);
                    }
                    WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 5, $value->order_id, $value->warehouse, $value->location, null, null, $value->quantity, $customer_order_details->invoice_customer);
                }
                //For product package
                if ($value->is_package == 1 && $value->quantity >= 1) {
                    $package_contents = OrderMaterial::where('reference_id', '=', $value->id)->get();
                    foreach ($package_contents as $pacakge_key => $pacakge_value) {
                        OrderMaterial::where('id', @$pacakge_value->id)->where('quantity', '>', 0)->update(['approved_product' => 1, 'invoice_quantity' => @$pacakge_value->quantity]);
                        $inventory_data = WarehouseInventory::where('product_id', $pacakge_value->product_number)->where('warehouse_id', $pacakge_value->warehouse)->where('location_id', $pacakge_value->location)->first();
                        if (@$inventory_data && $inventory_data->qty - $inventory_data->ordered >= 0) {
                            $pacakge_value->available_qty = $inventory_data->qty - $inventory_data->ordered;
                            WarehouseInventory::find($inventory_data->id)->update(['ordered' => $inventory_data->ordered - $pacakge_value->quantity, 'qty' => $inventory_data->qty - $pacakge_value->quantity, 'delivered' => $inventory_data->delivered + $pacakge_value->quantity]);
                        }

                        //updating the history
                        if ($pacakge_key->quantity >= 1) {
                            $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $pacakge_key->product_number)->first();
                            if ($whs_history_data) {
                                $whs_history_id = $whs_history_data->id;
                            } else {
                                $whs_history_id = WhsHistory::insertHistoryData($value->product_number, null, null, 0);
                            }
                            WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 5, $pacakge_key->order_id, $pacakge_key->warehouse, $pacakge_key->location, null, null, $pacakge_key->quantity, $customer_order_details->invoice_customer);
                        }

                    }

                }
            }
        }

    }

    /**
     * [constructOrdrMaterialDataForReport description]
     * @param  [type] $input_data      [description]
     * @param  [type] $order_materials [description]
     * @return [type]                  [description]
     */
    public static function constructOrdrMaterialDataForReport($input_data, $order_materials)
    {
        $diff_material = [];
        foreach ($order_materials as $key => $value) {
            $value->productDetails   = Product::where('id', '=', $value['product_number'])->first();
            $value->location_details = Location::where('id', $value->location)->first();
            if (!$value->shippment_id) {
                if ($value->quantity > 0 && $value->order_quantity > $value->quantity && $value->is_package != 1) {
                    $new_material                   = array();
                    $new_material['id']             = GanticHelper::gen_uuid();
                    $new_material['product_number'] = $value->product_number;
                    $new_material['user_id']        = $value->user_id;
                    $new_material['order_quantity'] = $value->order_quantity - $value->quantity;
                    $diff_material[]                = $new_material;
                    $value->order_quantity          = $value->quantity;
                    OrderMaterial::whereId($value->id)->update(['order_quantity' => $value->quantity]);
                }
                if ($value->is_package == 1) {
                    $package_contents = OrderMaterial::where('reference_id', '=', $value->id)->get();
                    foreach ($package_contents as $pacakge_key => $pacakge_value) {
                        $pacakge_value->productDetails   = Product::where('id', '=', $pacakge_value['product_number'])->first();
                        $pacakge_value->location_details = Location::where('id', $pacakge_value->location)->first();
                    }
                    $value->package_contents = $package_contents;
                }
            } else {
                if ($value->is_package == 1) {
                    $package_contents = OrderMaterial::where('reference_id', '=', $value->id)->get();
                    foreach ($package_contents as $pacakge_key => $pacakge_value) {
                        $pacakge_value->productDetails   = Product::where('id', '=', $pacakge_value['product_number'])->first();
                        $pacakge_value->location_details = Location::where('id', $pacakge_value->location)->first();
                    }
                    $value->package_contents = $package_contents;
                }
            }
        }
        $rest_order_number = "";
        if (count($diff_material) > 0) {
            $rest_order_number = self::createRestOrder($input_data['order_id'], $diff_material);
        }
        return ['order_materials' => $order_materials, 'rest_order' => count($diff_material), 'rest_order_number' => $rest_order_number];
    }

    /**
     * [createRestOrder description]
     * @param  [type] $order_id      [description]
     * @param  [type] $diff_material [description]
     * @return [type]                [description]
     */
    public static function createRestOrder($order_id, $diff_material)
    {
        $order_details                 = Order::find($order_id)->toArray();
        $order_details['is_res_order'] = 1;
        $order_details['res_order_id'] = $order_details['id'];
        $order_details['order_number'] = Order::getOrderNumber();
        $order_details['id']           = GanticHelper::gen_uuid();
        $order_details['sum']          = 0.00;
        $order_details['mva']          = 0.00;
        $order_details['round_down']   = 0.00;
        $order_details['total']        = 0.00;
        $order_details['order_status'] = 2;
        $rest_order                    = Order::create($order_details);
        foreach ($diff_material as $key => $value) {
            $diff_material[$key]['order_id'] = $rest_order->id;
            OrderMaterial::create($diff_material[$key]);
        }
        return $order_details['order_number'];
    }

    /**
     * [updateMaterialData description]
     * @param  [type] $input_data      [description]
     * @param  [type] $consignment     [description]
     * @param  [type] $logistraProduct [description]
     * @return [type]                  [description]
     */
    public static function updateMaterialData($input_data, $consignment, $logistraProduct)
    {
        if ($logistraProduct == 1) {
            OrderMaterial::where('order_id', $input_data['order_id'])->whereNull('shippment_id')->where('quantity', '>', 0)->update(['track_number' => @$consignment['bundles']['bundle']['pieces']['piece']['number-with-checksum'], 'shippment_id' => (int) $consignment['id']]);
            OrderMaterial::where('order_id', $input_data['order_id'])->whereNull('shippment_id')->where('is_text', '=', 1)->update(['track_number' => @$consignment['bundles']['bundle']['pieces']['piece']['number-with-checksum'], 'shippment_id' => (int) $consignment['id']]);
            ShippingMeasurement::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->update(['shipment_status' => (int) $consignment['id']]);
            Shipping::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->update(['track_number' => @$consignment['bundles']['bundle']['pieces']['piece']['number-with-checksum'], 'shipment_status' => 1, 'consignment_id' => (int) $consignment['id']]);
        } else {
            OrderMaterial::where('order_id', $input_data['order_id'])->whereNull('shippment_id')->where('quantity', '>', 0)->update(['track_number' => null, 'shippment_id' => 1]);
            OrderMaterial::where('order_id', $input_data['order_id'])->whereNull('shippment_id')->where('is_text', '=', 1)->update(['track_number' => null, 'shippment_id' => 1]);
            ShippingMeasurement::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->update(['shipment_status' => 1]);
            Shipping::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->update(['track_number' => null, 'shipment_status' => 1, 'consignment_id' => 1]);
        }
    }

    /**
     * [generateConsignment description]
     * @param  [type] $input_data [description]
     * @return [type]             [description]
     */
    public static function generateConsignment($input_data)
    {
        $order_details                = Order::whereId($input_data['order_id'])->with('customer')->first();
        $shipping_details             = Shipping::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->first();
        $shipping_measurement_details = ShippingMeasurement::where('order_id', $input_data['order_id'])->whereNull('shipment_status')->first();
        $sender_details               = LogistraDetails::whereId($shipping_details->sender_id)->first();
        $company_information          = Company::first();
        $header                       = array(
            "x-cargonizer-key:" . @$sender_details->cargonizer_key . "",
            "x-cargonizer-sender:" . @$sender_details->cargonizer_sender . "",
            "Content-Type:application/xml",
        );

        $body = '<consignments>
                    <consignment transport_agreement="' . config('app.TRANSPORT_AGREEMENT') . '" estimate="true" print="false">
                      <values>
                      </values>
                      <transfer>false</transfer>
                      <product>' . $shipping_details->product_identifier . '</product>
                      <parts>
                         <consignee>
                            <customer-number>' . $order_details->customer->customer . '</customer-number>
                            <name>' . $order_details->customer->name . '</name>
                            <address1>' . $order_details->deliveraddress1 . '</address1>
                            <country>NO</country>
                            <postcode>' . $order_details->deliveraddress_zip . '</postcode>
                            <city>' . $order_details->deliveraddress_city . '</city>
                            <phone>' . $order_details->customer->phone . '</phone>
                            <mobile></mobile>
                            <email>' . $order_details->customer->email . '</email>
                            <contact-person />
                         </consignee>
                         <return_address>
                            <name>' . $company_information->name . '</name>
                            <country>NO</country>
                            <address1>' . $company_information->post_address . '</address1>
                            <postcode>' . $company_information->zip . '</postcode>
                         </return_address>
                      </parts>
                      <items>
                         <item amount="1" description="Pakke#1" type="package" length="' . $shipping_measurement_details->length . '" height="' . $shipping_measurement_details->height . '" volume="' . $shipping_measurement_details->weight . '" width="' . $shipping_measurement_details->width . '" weight="' . $shipping_measurement_details->weight . '" />
                      </items>
                      <services>
                         <service id="postnord_notification_sms" />
                         <service id="postnord_notification_email" />
                      </services>
                      <messages>
                         <carrier />
                         <consignee />
                      </messages>
                      <references>
                         <consignor></consignor>
                         <consignee />
                      </references>
                   </consignment>
                </consignments>';
        $consignment       = restApi(config('app.LOGISTRA_URL') . "/consignments.xml", "POST", $header, $body);
        $consignment       = simplexml_load_string($consignment['response']);
        $jsonContent       = json_encode($consignment);
        $consignment_array = json_decode($jsonContent, true);
        if ($consignment_array && @$consignment_array['consignment']['id']) {
            if (config('app.env') == "production") {
                self::printConsignment($consignment_array['consignment'], $header, $shipping_details->printer);
            }
            return $consignment_array['consignment'];
        }
        return false;
    }

    /**
     * [generateLatestPacklistPDF description]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public static function generateLatestPacklistPDF($order_id)
    {
        try {
            $data                     = Order::editOrder($order_id);
            $pack_list_data           = PacklistHistory::where('order_id', $order_id)->orderBy('created_at', 'desc')->first();
            $pdf_data                 = json_decode($pack_list_data->pdf_data);
            $data['logistra_product'] = $pdf_data->logistra_product;
            $data['order_materials']  = $pdf_data->order_materials;
            if (@$data['orders']->customer_id) {
                $data['customer_details'] = Customer::where('id', '=', $data['orders']->customer_id)->with('shippingCustomerAddress', 'departmentCustomerAddress')->first();
            }
            $data['company_information'] = Company::first();
            $footer                      = View::make('order.packlistFooter', $data);
            $packlist_detail_view        = \View::make('order.packlist', $data);
            $temp_pdf_name               = GanticHelper::createTempFile("pdf");
            $pdfReport                   = PDF::loadHTML($packlist_detail_view)->setOption('footer-html', $footer)->save($temp_pdf_name);
            $fileData                    = array();
            $fileData['fileName']        = "Pakkseddel_" . $data['orders']->order_number . "_GANTIC_AS.pdf";
            $fileData['filePath']        = $temp_pdf_name;
            return $fileData;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'pickpacklist.log');
            return [];
        }
    }

    /**
     * [printConsignment description]
     * @param  [type] $consignment_id [description]
     * @return [type]                 [description]
     */
    public static function printConsignment($consignment_id, $header, $printer_id)
    {
        try {
            $printer_details = PrinterDetail::whereId($printer_id)->first();
            if ($printer_details) {
                $url       = config('app.LOGISTRA_URL') . "/consignments/label_direct?printer_id='" . $printer_details->number . "'&consignment_ids[]=" . $consignment_id;
                $print_pdf = restApi($url, "POST", $header, "");
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'pickpacklist.log');
            return [];
        }
    }

    /**
     * [updateNonStockableProduct description]
     * @return [type] [description]
     */
    public static function updateNonStockableProduct($order_id)
    {
        try {
            $non_stockable_materials = OrderMaterial::where('order_id', $order_id)->where('stockable', 0)->where('approved_product', 0)->get();
            foreach ($non_stockable_materials as $key => $value) {
                OrderMaterial::where('id', $value->id)->update(['quantity' => $value->order_quantity]);
            }
            return true;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'pickpacklist.log');
        }
    }
}
