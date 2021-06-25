<?php
/**
* Added by Aravinth
* Order history pivot table
* 20.06.2016
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDepartment extends Model
{
    protected $table = 'order_department';
    public $timestamps = false;
    protected $fillable = array('order_id', 'department_id');
    
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order','order_id','id');
    }
    public static function getOrderDepartmentByOrderId($id) {
        return OrderUser::with('department','order')->where('order_id', $id)->first();
    }

    // get department by order id
    public static function getOrderDepartmentIDByOrderId ($order_id) {
        $department = array();
        $order_department = OrderDepartment::where('order_id', '=', $order_id)->get()->toArray();
        foreach($order_department as $key => $value) {
            $department[] = $value['department_id'];
        }
        return $department;
    }
}