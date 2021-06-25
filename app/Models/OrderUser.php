<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderUser extends Model
{
    protected $table    = 'order_user';
    public $timestamps  = false;
    protected $fillable = array('order_id', 'user_id');

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
