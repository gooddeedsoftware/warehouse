<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseOrderDetails extends Model
{
    protected $primaryKey = 'whs_product_id';
    public $incrementing  = false;
    protected $table      = 'warehouse_order_details';
    public $timestamps    = false;
    protected $fillable   = array('whs_order_id', 'whs_product_id', 'product_id', 'source_whs_id', 'destination_whs_id', 'ordered_qty', 'picked_qty', 'received_qty');
}
