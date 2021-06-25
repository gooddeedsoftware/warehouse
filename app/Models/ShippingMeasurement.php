<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingMeasurement extends Model
{
    protected $table   = 'shipping_measurement';
    public $timestamps = true;
    use SoftDeletes;
    protected $fillable = array('id', 'order_id', 'height', 'weight', 'volume', 'width', 'length', 'shipment_status', 'shipping_id');
}
