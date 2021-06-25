<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductLocation extends Model
{
    use SoftDeletes;
    protected $table    = 'product_location';
    public $timestamps  = true;
    protected $fillable = array('product_id', 'warehouse_id', 'location_id');

    /**
     * [creatProductLocation description]
     * @param  [type] $product_location_data [description]
     * @return [type]                        [description]
     */
    public function creatProductLocation($product_location_data = array())
    {
        try {
            return $this->create($product_location_data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'product.log');
            return false;
        }
    }
}
