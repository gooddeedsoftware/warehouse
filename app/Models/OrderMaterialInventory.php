<?php
/**
* Added by Aravinth
* Order Materail Inventory pivot table
* 06.05.2017
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMaterialInventory extends Model
{
    protected $table = 'order_material_inventory';
    public $timestamps = false;
    protected $fillable = array('order_material_id', 'inventory_id', 'is_returned', 'equipment_id');
    
    public function orderMaterial()
    {
        return $this->belongsTo('App\Models\OrderMaterial','order_material_id','id');
    }

    public function inventory()
    {
        return $this->belongsTo('App\Models\WarehouseInventory','inventory_id', 'id');
    }

    // get order material inventory details by materail id
    public static function getOrderMaterialInvenoty($material_id) {
        return OrderUser::with('orderMaterial','inventory')->where('order_material_id', $material_id)->first();
    }
}