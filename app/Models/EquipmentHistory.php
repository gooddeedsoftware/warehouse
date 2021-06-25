<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentHistory extends Model
{
    protected $table    = 'equipment_history';
    public $timestamps  = true;
    protected $fillable = array(
        'equipment_id',
        'order_id',
        'ordermaterial_id',
        'created_by',
		'inventory_id',
		'serial_number',
		'product_id',
    );
}
