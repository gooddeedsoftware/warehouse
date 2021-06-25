<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderEmailHistory extends Model
{
    protected $table = "offer_order_mail_history";
    public $incremention = false;
    protected $fillable  = array('order_id',
        'email',
        'user_id',
        'user_name',
        'send_date',
        'order_status');
    /**
     * [storeOrderMailHistory description]
     * @return [type] [description]
     */
    public static function storeOrderMailHistory($order_id, $email, $status, $user_id){
    	$input = array();
    	$input['order_id'] = $order_id;
    	$input['email'] = $email;
    	$input['order_status'] = $status;
    	$input['user_id'] = $user_id;
    	$input['send_date'] = date('Y-m-d');
    	OrderEmailHistory::create($input);
    	return true;
    }
}
