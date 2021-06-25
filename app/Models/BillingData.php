<?php
namespace App\Models;

use App\Helpers\GanticHelper;
use Illuminate\Database\Eloquent\Model;
use Session;

class BillingData extends Model
{
    protected $table    = 'billing_data';
    public $timestamps  = true;
    protected $fillable = array('ordermaterial_id', 'product_number', 'description', 'invoice_quantity', 'unit', 'sale_price', 'discount', 'vat', 'updated_by', 'order_id');

    /**
     * [saveOrUpdate description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function saveOrUpdate($data)
    {
        try {
            if ($data) {
                $billing_data             = BillingData::where('ordermaterial_id', '=', @$data['ordermaterial_id'])->first();
                $data['invoice_quantity'] = isset($data['invoice_quantity']) ? str_replace(",", ".", $data['invoice_quantity']) : "";
                $data['sale_price']       = isset($data['sale_price']) ? str_replace(",", ".", $data['sale_price']) : "";
                $data['discount']         = isset($data['discount']) ? str_replace(",", ".", $data['discount']) : "";
                $data['vat']              = isset($data['vat']) ? str_replace(",", ".", $data['vat']) : "";
                $data['updated_by']       = Session::get('currentUserID');

                if (@$billing_data) {
                    $billing_data->fill($data);
                    $billing_data->save();
                } else {
                    $billing_data = BillingData::create($data);
                }
                return $billing_data;
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }

    }

}
