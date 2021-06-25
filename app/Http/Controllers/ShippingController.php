<?php

namespace App\Http\Controllers;

use App\Models\LogistraDetails;
use App\Models\Shipping;
use App\Models\ShippingMeasurement;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listOrderShipping($order_id)
    {

        $data['order_id']    = $order_id;
        $data['measures']    = ShippingMeasurement::where('order_id', $order_id)->whereNull('shipment_status')->first();
        $data['products']    = Shipping::where('order_id', $order_id)->orderBy('created_at', 'desc')->get();
        $data['senders']     = LogistraDetails::where('status', 1)->pluck('name', 'id');
        $data['all_senders'] = LogistraDetails::pluck('name', 'id');
        return view('shipping.index', $data);
    }

    /**
     * [getPrices description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getPrices(Request $request)
    {
        $shipping = new Shipping();
        return $shipping->generateProductHtml($request->all());
    }

    /**
     * [storeShipping description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function storeShipping(Request $request)
    {
        $input                                  = $request->all();
        $measureData                            = array();
        $measureData['order_id']                = $input['shipmentData']['order_id'];
        $measureData['weight']                  = $input['shipmentData']['weight'];
        $measureData['volume']                  = $input['shipmentData']['volume'];
        $measureData['height']                  = $input['shipmentData']['height'];
        $measureData['length']                  = $input['shipmentData']['length'];
        $measureData['width']                   = $input['shipmentData']['width'];
        $input['shipmentData']['customerprice'] = replaceCommaWithDot($input['shipmentData']['customerprice']);
        ShippingMeasurement::where('order_id', $measureData['order_id'])->whereNull('shipment_status')->forceDelete();
        Shipping::where('order_id', $measureData['order_id'])->whereNull('shipment_status')->forceDelete();
        $shipping                   = Shipping::create($input['shipmentData']);
        $measureData['shipping_id'] = $shipping->id;
        ShippingMeasurement::create($measureData);
        if ($input['type'] == '2') {
            $shippingObj = new Shipping();
            $shippingObj->createOfferMaterialRow($shipping);
        }
        return json_encode(array('shipping' => $shipping));
    }

    /**
     * [updateShipping description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function updateShipping(Request $request)
    {
        $input = $request->all();
        if ($input['type'] == 1) {
            $input['customerprice'] = replaceCommaWithDot($input['customerprice']);
            $shipping               = Shipping::where('id', $input['id'])->update(['customerprice' => $input['customerprice']]);
        } else {
            $input['netprice'] = replaceCommaWithDot($input['customerprice']);
            $input['netprice'] = replaceCommaWithDot($input['netprice']);
            $shipping          = Shipping::where('id', $input['id'])->update(['customerprice' => $input['customerprice'], 'netprice' => $input['netprice']]);
        }
        return json_encode(array('shipping' => $shipping));
    }

    /**
     * [downloadShipmentLabel description]
     * @param  [type] $consignment_id [description]
     * @return [type]                 [description]
     */
    public function downloadShipmentLabel($consignment_id)
    {
        $fileData = Shipping::generateLableAsPdf($consignment_id);
        if ($fileData && file_exists($fileData['filePath'])) {
            $headers = array('Content-Type: application/pdf');
            return \Response::download($fileData['filePath'], $fileData['fileName'], $headers);
        } else {
            return \Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

}
