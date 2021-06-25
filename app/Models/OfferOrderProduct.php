<?php
namespace App\Models;

use App\Helpers\GanticHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class OfferOrderProduct extends Model
{
    protected $table   = 'order_offer_product';
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = array(
        'order_id',
        'order_material_id',
        'product_id',
        'product_text',
        'qty',
        'unit',
        "price",
        "discount",
        'sum_ex_vat',
        'vat',
        'created_by',
        'updated_by',
        'is_approved',
        'deleted_at',
        'delivery_date',
        'is_text',
        'text',
    );

    /**
     * [offers description]
     * @return [type] [description]
     */
    public function orders()
    {
        return $this->belongsTo('App\Models\Order', 'id', 'order_id');
    }
    /**
     * [storeOfferProductDetails description]
     * @param  [type]  $data      [description]
     * @param  boolean $row_count [description]
     * @return [type]             [description]
     */
    public static function storeOfferOrderProductDetails($order_id, $data, $row_count = false)
    {
        try {
            $offer_order_product = array();
            for ($i = 1; $i <= $row_count; $i++) {
                $record             = array();
                $record['order_id'] = $order_id;
                if (@$data['is_text_' . $i]) {
                    $record['text']    = @$data['text_' . $i];
                    $record['is_text'] = 1;
                    OfferOrderProduct::create($record);
                    $offer_order_product[] = $record;
                } else {
                    if (@$data['product_' . $i] && $data['product_' . $i] != 'Select' && $data['save_val_' . $i] == 1) {
                        $record['product_id']    = @$data['product_' . $i];
                        $record['product_text']  = @$data['product_text_' . $i];
                        $record['qty']           = isset($data['qty_' . $i]) ? str_replace(",", ".", $data['qty_' . $i]) : "";
                        $record['unit']          = @$data['unit_' . $i];
                        $record["price"]         = isset($data['price_' . $i]) ? str_replace(",", ".", $data['price_' . $i]) : "";
                        $record["discount"]      = isset($data['discount_' . $i]) ? str_replace(",", ".", $data['discount_' . $i]) : "";
                        $record['sum_ex_vat']    = isset($data['sum_ex_vat_' . $i]) ? str_replace(",", ".", $data['sum_ex_vat_' . $i]) : "";
                        $record['vat']           = isset($data['vat_' . $i]) ? str_replace(",", ".", $data['vat_' . $i]) : "";
                        $record['created_by']    = Session::get('currentUserID');
                        $record['delivery_date'] = @$data['delivery_date_' . $i] ? date('Y-m-d', strtotime($data['delivery_date_' . $i])) : null;
                        $record['is_text']       = 0;
                        OfferOrderProduct::create($record);
                        $offer_order_product[] = $record;
                    }
                }
            }
            return $offer_order_product;
        } catch (\Exception $e) {
            echo $e;die;
            exit();
        }
    }

    /**
     * [copyProductsToMaterial description]
     * @param  boolean $status   [description]
     * @param  boolean $order_id [description]
     * @return [type]            [description]
     */
    public static function copyProductsToMaterial($status = false, $order_id = false)
    {
        try {
            if ($status == 2) {
                $offer_product_details = OfferOrderProduct::where('order_id', '=', $order_id)->where('is_approved', '=', 0)->get();
                if (@$offer_product_details) {
                    foreach ($offer_product_details as $key => $value) {
                        $input = array();
                        if ($value->is_text == 1) {
                            $input['id']                     = GanticHelper::gen_uuid();
                            $input['product_text']           = $value->text;
                            $input['order_id']               = $order_id;
                            $input['is_text']                = 1;
                            $input['text_ref_id']            = null;
                            $input['is_offerorder']          = 1;
                            $input['order_offer_product_id'] = $value['id'];
                            $result                          = OrderMaterial::create($input);
                        } else {
                            $product_details                 = Product::findOrFail($value['product_id']);
                            $input['id']                     = GanticHelper::gen_uuid();
                            $input['order_id']               = $order_id;
                            $input['warehouse']              = '';
                            $input['quantity']               = 0;
                            $input['invoice_quantity']       = 0;
                            $input['reference_id']           = null;
                            $input['location']               = '';
                            $input['userid']                 = Session::get('currentUserID');
                            $input['product_number']         = $value['product_id'];
                            $input['order_quantity']         = $value['qty'];
                            $input['discount']               = $value['discount'];
                            $input['offer_sale_price']       = $value['price'];
                            $input['is_offerorder']          = 1;
                            $input['order_offer_product_id'] = $value['id'];
                            $input['delivery_date']          = @$value['delivery_date'] ? date('Y-m-d', strtotime($value['delivery_date'])) : null;
                            if ($product_details->is_package == 1) {
                                $input['is_package'] = 1;
                            } else {
                                $input['is_package'] = 0;
                            }
                            $order_material_details = OrderMaterial::create($input);
                            if ($product_details->is_package == 1) {
                                OrderMaterial::storeOfferProductPackage($value['product_id'], $input['id'], $value['qty'], $input['delivery_date'], $order_id);
                            }
                        }
                    }
                }
            } elseif ($status == 1) {
                $offer_product_details = OfferOrderProduct::where('order_id', '=', $order_id)->where('is_approved', '=', 0)->get();
                if (@$offer_product_details) {
                    foreach ($offer_product_details as $key => $value) {
                        $order_material_detail = OrderMaterial::where('order_offer_product_id', '=', $value['id'])->first();
                        if (@$order_material_detail->warehouse && @$order_material_detail->warehouse && @$order_material_detail->quantity > 0) {
                            $inventory_details = WarehouseInventory::where('warehouse_id', '=', $order_material_detail->warehouse)->where('location_id', '=', $order_material_detail->location)->where('product_id', '=', $order_material_detail->product_number)->first();
                            if (@$inventory_details) {
                                $ordered = $inventory_details->ordered - $order_material_detail->quantity;
                                $result  = WarehouseInventory::where('id', '=', $inventory_details->id)->update(['ordered' => $ordered]);
                            }
                        }
                        OrderMaterial::where('id', '=', @$order_material_detail->id)->forceDelete();
                        OrderMaterial::where('reference_id', '=', @$order_material_detail->id)->forceDelete();
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }
}
