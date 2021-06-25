<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\LogistraDetails;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\Product;
use App\Models\Shipping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping extends Model
{

    protected $table   = 'shipment';
    public $timestamps = true;
    use SoftDeletes;

    protected $fillable = array('order_id',
        'sender_id',
        'product_name',
        'product_identifier',
        'carrier_name',
        'carrier_identifier',
        "customerprice",
        "grossprice",
        "netprice",
        "estimatedcost", 'shipment_status', 'consignment_id', 'track_number', 'printer');

    public function generateProductHtml($input = array())
    {
        try {
            ini_set('max_execution_time', 0);
            $sender_details = LogistraDetails::whereId($input['sender'])->first();
            $header         = array(
                "x-cargonizer-key:" . $sender_details->cargonizer_key . "",
                "x-cargonizer-sender:" . $sender_details->cargonizer_sender . "",
            );
            $carriers        = restApi(config('app.LOGISTRA_URL') . "/transport_agreements.xml", "GET", $header);
            $carriersContent = simplexml_load_string($carriers['response']);
            $jsonContent     = json_encode($carriersContent);
            $carrierArray    = json_decode($jsonContent, true);
            $order_details   = Order::whereId($input['order_id'])->with('customer')->first();
            if ($order_details->deliveraddress_zip) {
                $productFinalArray = self::createBringProductArray($carrierArray, $order_details, $sender_details, $input);
                $htmContent        = count($productFinalArray) > 0 ? self::constructProductHtmlContent($productFinalArray, $sender_details, $input['type'], @$input['printer']) : '';
                return json_encode(array('content' => $htmContent, 'type' => 'success'));
            }
            return json_encode(array('message' => __('main.zip_not_avaliable'), 'type' => 'fail'));
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'order.log');
            return json_encode(array('message' => __('main.something_went_wrong'), 'type' => 'fail'));
        }
    }

    public static function createBringProductArray($carrierArray, $order_details, $sender_details, $input)
    {
        $productFinalArray = [];
        $i                 = 0;
        foreach ($carrierArray['transport-agreement'] as $key => $value) {
            if ($value['carrier']['identifier'] == "bring2") {
                $product = array();
                $product = $value['products']['product'];
                foreach ($product as $productkey => $productvalue) {
                    $header = array(
                        "content-type: application/xml",
                        "x-cargonizer-key:" . $sender_details->cargonizer_key . "",
                        "x-cargonizer-sender:" . $sender_details->cargonizer_sender . "",
                    );
                    $body = '<consignments>
                                <consignment transport_agreement="' . $value['id'] . '">
                                    <product>' . $productvalue['identifier'] . '</product>
                                    <parts>
                                      <consignee>
                                        <name>' . $order_details->customer->name . '</name>
                                        <country>NO</country>
                                        <postcode>' . $order_details->deliveraddress_zip . '</postcode>
                                      </consignee>
                                    </parts>
                                    <items>
                                      <item type="package" amount="1" weight="' . $input['weight'] . '" volume="' . $input['volume'] . '"/>
                                    </items>
                                </consignment>
                            </consignments>';
                    $shipmentConstResponse = restApi(config('app.LOGISTRA_URL') . "/consignment_costs.xml", "POST", $header, $body);
                    $shipmentXMLContent    = simplexml_load_string($shipmentConstResponse['response']);
                    $shipmentJsonContent   = json_encode($shipmentXMLContent);
                    $shipmentArray         = json_decode($shipmentJsonContent, true);
                    if (@$shipmentArray['estimated-cost']) {
                        $productFinalArray[$i]                       = $shipmentArray;
                        $productFinalArray[$i]['product_identifer']  = $productvalue['identifier'];
                        $productFinalArray[$i]['product_name']       = $productvalue['name'];
                        $productFinalArray[$i]['carrier_identifier'] = $value['carrier']['identifier'];
                        $productFinalArray[$i]['carrier_name']       = $value['carrier']['name'];
                        $i++;
                    }
                }
            }
        }
        return $productFinalArray;
    }

    /**
     * [constructProductHtmlContent description]
     * @param  [type] $productFinalArray [description]
     * @return [type]                    [description]
     */
    public static function constructProductHtmlContent($productFinalArray, $sender_details, $type = false, $printer)
    {
        $htmContent = '';
        $k          = 1;
        foreach ($productFinalArray as $key => $value) {


            if (@$value['estimated-cost'] && gettype($value['estimated-cost']) == "string") {
                $htmContent .= '<tr class="unSaved">';
                $htmContent .= '<td id="carrier_name" sender_id="' . $sender_details->id . '" printer="' . $printer . '" identifier="' . $value['carrier_identifier'] . '">' . $value['carrier_name'] . '</td>';
                $htmContent .= '<td id="product_name" identifier="' . $value['product_identifer'] . '">' . $value['product_name'] . '</td>';
                $htmContent .= '<td id="amounTd" grossprice="' . $value['net-amount'] . '" netPrice="' . $value['net-amount'] . '" estimatedCost="' . $value['estimated-cost'] . '">' . replaceDotWithComma($value['net-amount']) . '</td>';
                $htmContent .= '<td><input name="customerprice" class="numberWithSingleComma customerprice form-control" value=' . replaceDotWithComma($value['gross-amount']) . ' id="customerprice_' . $k . '" readonly></input></td>';
                if ($type == "order") {
                    $htmContent .= '<td align="center"><div class="custom-control custom-checkbox mb-3"><input type="checkbox" class="custom-control-input saveShipment" id="saveShipment_' . $k . '"><label class="custom-control-label" for="saveShipment_' . $k . '"></label></div></td>';
                    $htmContent .= '<td></td><td></td><td></td></tr>';
                } else {
                    $htmContent .= '<td align="center"><div class="custom-control custom-checkbox mb-3"><input type="checkbox" class="custom-control-input saveShipment" id="saveShipment_' . $k . '"><label class="custom-control-label" for="saveShipment_' . $k . '"></label></div></td>';
                    $htmContent .= '</tr>';
                }
                $k++;
            }
        }
        return $htmContent;
    }

    /**
     * [generateLableAsPdf description]
     * @param  [type] $consignment_id [description]
     * @return [type]                 [description]
     */
    public static function generateLableAsPdf($consignment_id)
    {
        $shipping_details = Shipping::where('consignment_id', $consignment_id)->first();
        $sender_details   = LogistraDetails::whereId($shipping_details->sender_id)->first();
        $header           = array(
            "x-cargonizer-key:" . $sender_details->cargonizer_key . "",
            "x-cargonizer-sender:" . $sender_details->cargonizer_sender . "",
        );
        $pdf_response  = restApi(config('app.LOGISTRA_URL') . "/consignments/label_pdf?consignment_ids[]=" . $consignment_id . "", "GET", $header);
        $temp_pdf_name = GanticHelper::createTempFile("pdf");
        $myfile        = fopen($temp_pdf_name, "w") or die("Unable to open file!");
        fwrite($myfile, $pdf_response['response']);
        fclose($myfile);
        $fileData             = array();
        $fileData['fileName'] = "Label_" . $consignment_id . "_GANTIC_AS.pdf";
        $fileData['filePath'] = $temp_pdf_name;
        return $fileData;
    }

    /**
     * [createOfferMaterialRow description]
     * @param  [type] $shipping [description]
     * @return [type]           [description]
     */
    public function createOfferMaterialRow($shipping)
    {
        OrderMaterial::where('order_id', $shipping->order_id)->where('is_logistra', 1)->where('approved_product', '=!', 1)->forceDelete();
        $product_details = product::whereNobb(@$shipping->product_identifier)->first();
        if (!$product_details) {
            $product_array['id']             = GanticHelper::gen_uuid();
            $product_array['product_number'] = Product::getProductNumber();
            $product_array['description']    = $shipping->product_name;
            $product_array['nobb']           = $shipping->product_identifier;
            $product_array['sale_price']     = $shipping->customerprice;
            $product_array['stockable']      = 0;
            $product_details                 = Product::create($product_array);
        }
        if ($product_details) {
            $new_material                     = array();
            $new_material['id']               = GanticHelper::gen_uuid();
            $new_material['order_quantity']   = 1;
            $new_material['product_number']   = $product_details->id;
            $new_material['quantity']         = 1;
            $new_material['unit']             = 2;
            $new_material['order_id']         = $shipping->order_id;
            $new_material['offer_sale_price'] = $shipping->customerprice;
            $new_material['is_logistra']      = 1;
            $new_material['approved_product'] = 0;
            $new_material['sum_ex_vat']       = $shipping->customerprice;
            $new_material['cost_price']       = $shipping->netprice;
            $max_sort                         = \DB::table('order_material')->where('order_id', $shipping->order_id)->max('sortorderval');
            $new_material['sortorderval']     = @$max_sort ? $max_sort + 1 : 0;
            OrderMaterial::create($new_material);
        }

    }
}
