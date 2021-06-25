<?php

namespace App\Traits;

use App\Helpers\GanticHelper;
use App\Models\DropdownHelper;
use App\Models\Order;
use App\Models\OrderContactPerson;
use App\Models\OrderDepartment;
use App\Models\OrderMaterial;
use App\Models\Shipping;
use App\Models\ShippingMeasurement;
use App\Models\UserOrder;
use DB;
use Session;

trait Offer
{
    /**
     * [getOfferStatusForEdit description]
     * @return [type] [description]
     */
    public static function getOfferStatusForEdit($offer_status, $order_id)
    {
        try {
            $order_status = array();
            if ($offer_status == 1) {
                $order_status = Self::getOfferStatus(1);
            } elseif ($offer_status == 2) {
                $order_status = Self::getOfferStatus(2);
            } elseif ($offer_status == 3) {
                if ($order_id == null) {
                    $order_status = Self::getOfferStatus(3);
                } else {
                    $order_status = Self::getOfferStatus(4);
                }
            } elseif ($offer_status == 4) {
                $order_status = Self::getOfferStatus(5);
            }
            return $order_status;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return [];
        }
    }

    /**
     * [getOfferStatus description]
     * @param  boolean $type [description]
     * @return [type]        [description]
     */
    public static function getOfferStatus($type = false)
    {
        try {
            $language     = Session::get('language') ? Session::get('language') : 'no';
            $order_status = array();
            if ($type == 1) {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [1, 2])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            } elseif ($type == 2) {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [2, 3, 4])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            } elseif ($type == 3) {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [2, 3])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            } elseif ($type == 4) {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [3])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            } elseif ($type == 5) {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [2, 4])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            } elseif ($type == 6) {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [1])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            } else {
                $order_status = DropdownHelper::where('language', $language)->where('groupcode', '020')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            }
            return $order_status;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return [];
        }
    }

    /**
     * [getOfferNumber description]
     * @return [type] [description]
     */
    public static function getOfferNumber()
    {
        try {
            $offer_number = "1000";
            $result       = Order::select(DB::raw("MAX(offer_number )  as offer_number"))->where('is_offer', 1)->first();
            if (isset($result->offer_number)) {
                $offer_number = $result->offer_number;
                $offer_number = sprintf('%04d', $offer_number + 1);
            } else {
                $offer_number = '1000';
            }
            return $offer_number;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return null;
        }
    }

    /**
     * [createOrderFromOffer description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function createOrderFromOffer($id)
    {
        try {
            $order                     = Order::find($id)->toArray();
            $new_order                 = $order;
            $new_order['is_offer']     = 0;
            $new_order['offer_number'] = null;
            $new_order['status']       = 2;
            $new_order['order_number'] = Order::getOrderNumber();
            $new_order['id']           = GanticHelper::gen_uuid();
            $new_order                 = Order::create($new_order);
            Order::whereId($id)->update(['offer_order_id' => $new_order['id'], 'offer_order_number' => $new_order['order_number']]);

            $order_department = OrderDepartment::where('order_id', $id)->get()->toArray();
            foreach ($order_department as $key => $value) {
                $value['order_id'] = $new_order['id'];
                OrderDepartment::create($value);
            }

            $order_contact_person = OrderContactPerson::where('order_id', $id)->get()->toArray();
            foreach ($order_contact_person as $key => $value) {
                $value['order_id'] = $new_order['id'];
                OrderContactPerson::create($value);
            }

            $user_order = UserOrder::where('order_id', $id)->get()->toArray();
            foreach ($user_order as $key => $value) {
                $value['order_id'] = $new_order['id'];
                UserOrder::create($value);
            }
            $order_material = OrderMaterial::where('order_id', $id)->with('product', 'product.supplier')->get()->toArray();
            foreach ($order_material as $key => $value) {
                $value['order_id']   = $new_order['id'];
                $value['id']         = GanticHelper::gen_uuid();
                $value['cost_price'] = $value['product']['cost_price'];
                $value['stockable']  = $value['product']['stockable'];
                $value['prod_nbr']   = $value['product']['product_number'];
                if ($value['is_package'] == 1) {
                    OrderMaterial::storeOfferProductPackage($value['product_number'], $value['id'], $value['order_quantity'], $value['delivery_date'], $id);
                }
                OrderMaterial::create($value);
            }

            $new_measure = ShippingMeasurement::where('order_id', $id)->first();
            if ($new_measure) {
                $new_measure             = $new_measure->toArray();
                $new_measure['id']       = GanticHelper::gen_uuid();
                $new_measure['order_id'] = $new_order['id'];
                ShippingMeasurement::create($new_measure);
            }

            $new_shipping = Shipping::where('order_id', $id)->first();
            if ($new_shipping) {
                $new_shipping             = $new_shipping->toArray();
                $new_shipping['id']       = GanticHelper::gen_uuid();
                $new_shipping['order_id'] = $new_order['id'];
                Shipping::create($new_shipping);
            }
            return $new_order['order_number'];
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return null;
        }
    }

}
