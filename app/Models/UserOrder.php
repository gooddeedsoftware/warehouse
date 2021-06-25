<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    protected $table    = 'user_order';
    public $timestamps  = false;
    protected $fillable = array('order_id', 'user_id');

    public static function getUserIdByOrderId($order_id)
    {
        $user_ids = array();
        $users    = UserOrder::where('order_id', $order_id)->get()->toArray();
        foreach ($users as $user) {
            $user_ids[] = $user['user_id'];
        }
        return $user_ids;
    }
}
