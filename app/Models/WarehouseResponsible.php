<?php

/*
*	Aravint.A aravinth@avalia.no
*	Created date: 15.05.2017
*	Warehouse responsible table, here we will shandle the warehouse responsible users
*	We will store userid along with warehouse id
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseResponsible extends Model {
	public $timestamps = false;
	protected $table = "warehouse_responsible";
    protected $fillable = array('warehouse_id', 'user_id');

    // get responsible users as an array
    public static function getResponsibleUserId ($id) {
    	try {
    		$responsible = array();
    		$responsible_details = WarehouseResponsible::where('warehouse_id', '=', $id)->get()->toArray();
        	$responsible_array = array();
	        foreach ($responsible_details as $key => $value) {
	            $responsible[] = $value['user_id'];
	        }
	        return $responsible;
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
